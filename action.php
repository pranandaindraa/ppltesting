<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Nilai Mahasiswa</title>
    <style>
        body {
            background-color: gold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: aqua;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0066ff ;
        }
        .semester-buttons {
            margin-bottom: 20px;
        }
        .semester-buttons button {
            margin-right: 5px;
            padding: 5px 10px;
            background-color: greenyellow;
        }

        button {
            background-color: #ff9966;
        }
    </style>
</head>
<body>
    <h1>Detail Nilai Mahasiswa</h1>

    <?php
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "pplsemester7");

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Ambil data nama mahasiswa dan NIM dari URL
    $nama = isset($_GET['nama']) ? $conn->real_escape_string($_GET['nama']) : 'Tidak Diketahui';
    $nim = isset($_GET['nim']) ? $conn->real_escape_string($_GET['nim']) : '';

    // Ambil semester dari URL, default ke semua semester jika tidak ada
    $semester = isset($_GET['semester']) ? $conn->real_escape_string($_GET['semester']) : '';

    // Query untuk mendapatkan data jurusan, jenis kelamin, IPS, dan IPK
    $sql_mahasiswa = "SELECT jurusan, jenisKelamin, IPS, IPK FROM mahasiswa WHERE nama = '$nama'";
    $result_mahasiswa = $conn->query($sql_mahasiswa);
    $jurusan = $jenisKelamin = $ips = $ipk = "Data tidak tersedia";
    if ($result_mahasiswa->num_rows > 0) {
        $row = $result_mahasiswa->fetch_assoc();
        $jurusan = $row['jurusan'];
        $jenisKelamin = $row['jenisKelamin'];
        $ips = $row['IPS'];
        $ipk = $row['IPK'];
    }

    echo "<h2>$nama</h2>";
    ?>
    <table>
        <thead>
            <tr>
                <th>Jurusan</th>
                <th>Jenis Kelamin</th>
                <th>IPS</th>
                <th>IPK</th>
                <th>Indeks Nilai</th> <!-- Added column for grade index -->
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($jurusan); ?></td>
                <td><?php echo htmlspecialchars($jenisKelamin); ?></td>
                <td><?php echo htmlspecialchars($ips); ?></td>
                <td><?php echo htmlspecialchars($ipk); ?></td>
            </tr>
        </tbody>
    </table>
<br>
    <!-- Button Filter Semester -->
    <div class="semester-buttons">
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=1'">Semester 1</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=2'">Semester 2</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=3'">Semester 3</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=4'">Semester 4</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=5'">Semester 5</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=6'">Semester 6</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=7'">Semester 7</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>&semester=8'">Semester 8</button>
        <button onclick="window.location.href='?nim=<?php echo $nim; ?>&nama=<?php echo urlencode($nama); ?>'">Semua Semester</button>
    </div>

    <?php
    // Query untuk mendapatkan tahun dari tabel KRS berdasarkan semester yang dipilih
    if ($semester) {
        $sql_tahun = "SELECT DISTINCT tahun FROM krs WHERE nim = ? AND semesterKRS = ?";
        $stmt_tahun = $conn->prepare($sql_tahun);
        $stmt_tahun->bind_param("ss", $nim, $semester);
        $stmt_tahun->execute();
        $result_tahun = $stmt_tahun->get_result();

        if ($result_tahun->num_rows > 0) {
            $row_tahun = $result_tahun->fetch_assoc();
            $tahun = $row_tahun['tahun'];
        } else {
            $tahun = "Tidak Diketahui";
        }
        $stmt_tahun->close();
    } else {
        $tahun = "Semua Tahun";
    }

    echo "<h2>Daftar Mata Kuliah Mahasiswa Tahun $tahun</h2>";
    ?>

    <!-- Tabel Daftar Mata Kuliah -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode MK</th>
                <th>Mata Kuliah</th>
                <th>SKS</th>
                <th>Nilai</th>
                <th>Indeks Nilai</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Query SQL untuk mendapatkan data mata kuliah dan nilai
        $query = "
            SELECT 
                mk.kodeMK, 
                mk.mataKuliah, 
                mk.sks, 
                krs.nilai
            FROM 
                mata_kuliah mk
            INNER JOIN 
                krs ON mk.kodeMK = krs.kodeMK
            INNER JOIN 
                mahasiswa m ON m.nim = krs.nim
            WHERE 
                m.nim = ?";

        // Tambahkan filter semester jika dipilih
        if ($semester) {
            $query .= " AND krs.semesterKRS = ?";
        }

        // Persiapan statement
        $stmt = $conn->prepare($query);

        // Jika semester dipilih, bind parameter semester
        if ($semester) {
            $stmt->bind_param("ss", $nim, $semester);
        } else {
            $stmt->bind_param("s", $nim);
        }
        
        $stmt->execute();

        // Ambil hasil
        $result = $stmt->get_result();
        $no = 1;

        // Tampilkan data dalam tabel
        while ($row = $result->fetch_assoc()) {
            $nilai = $row['nilai'];


         // Menentukan indeks nilai berdasarkan angka
         if ($nilai >= 3.5) {
            $indeks = 'A';
        } elseif ($nilai >= 3.0) {
            $indeks = 'B+';
        } elseif ($nilai >= 2.5) {
            $indeks = 'B';
        } elseif ($nilai >= 2.0) {
            $indeks = 'C+';
        } else {
            $indeks = 'C';
        }
        


            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . htmlspecialchars($row['kodeMK']) . "</td>";
            echo "<td>" . htmlspecialchars($row['mataKuliah']) . "</td>";
            echo "<td>" . htmlspecialchars($row['sks']) . "</td>";
            echo "<td>" . htmlspecialchars($nilai) . "</td>";
            echo "<td>" . $indeks . "</td>";  // Displaying the grade index
            echo "</tr>";
        }

        // Tutup koneksi
        $stmt->close();
        $conn->close();
        ?>
        </tbody>
    </table>
<br>
    <button onclick="window.location.href='index.php'">Back</button>
</body>
</html>
