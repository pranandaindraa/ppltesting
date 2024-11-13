<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Nama di Tabel</title>
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
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #0066ff;
        }

         button {
            margin-right: 5px;
            padding: 5px 10px;
            background-color: greenyellow;
        }
    </style>
</head>
<body>
    <h1>Tabel Mahasiswa</h1>
    <label for="searchName">Cari Nama atau NIM:</label>
    <input type="text" id="searchName" onkeyup="searchTable()" placeholder="Masukkan nama atau NIM...">

    <table id="dataTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Koneksi ke database MySQL menggunakan XAMPP
            $conn = new mysqli("localhost", "root", "", "pplsemester7"); 

            // Periksa koneksi
            if ($conn->connect_error) {
                die("Koneksi gagal: " . $conn->connect_error);
            }

            // Query untuk mendapatkan data mahasiswa
            $sql = "SELECT nama, nim FROM mahasiswa"; 
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Menampilkan data dalam tabel
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                    echo "<td><a href='action.php?nama=" . urlencode($row['nama']) . "&nim=" . urlencode($row['nim']) . "' target='_blank'>
                    <button>Detail</button></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data</td></tr>";
            }

            // Tutup koneksi
            $conn->close();
            ?>
        </tbody>
    </table>
    <br>
    <button onclick="prevPage()">Back</button>
    <button onclick="nextPage()">Next</button>

    <script>
        let currentPage = 0;
        const rowsPerPage = 10;

        function paginateTable() {
            const table = document.getElementById("dataTable");
            const rows = table.getElementsByTagName("tr");

            // Sembunyikan semua baris terlebih dahulu
            for (let i = 1; i < rows.length; i++) {
                rows[i].style.display = "none";
            }

            // Tampilkan baris sesuai dengan halaman saat ini
            const start = currentPage * rowsPerPage + 1;
            const end = start + rowsPerPage;
            for (let i = start; i < end && i < rows.length; i++) {
                rows[i].style.display = "";
            }
        }

        function nextPage() {
            const table = document.getElementById("dataTable");
            const rows = table.getElementsByTagName("tr");
            const totalPages = Math.ceil((rows.length - 1) / rowsPerPage);

            // Pindah ke halaman berikutnya jika belum di akhir
            if (currentPage < totalPages - 1) {
                currentPage++;
                paginateTable();
            }
        }

        function prevPage() {
            // Kembali ke halaman sebelumnya jika belum di awal
            if (currentPage > 0) {
                currentPage--;
                paginateTable();
            }
        }

        // Panggil fungsi untuk menampilkan halaman pertama
        paginateTable();

        function searchTable() {
            const input = document.getElementById("searchName").value.toLowerCase();
            const table = document.getElementById("dataTable");
            const rows = table.getElementsByTagName("tr");

            // Loop melalui setiap baris tabel
            for (let i = 1; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName("td")[1];
                const nimCell = rows[i].getElementsByTagName("td")[2];
                if (nameCell || nimCell) {
                    const nameText = nameCell.textContent || nameCell.innerText;
                    const nimText = nimCell.textContent || nimCell.innerText;
                    if (nameText.toLowerCase().indexOf(input) > -1 || nimText.toLowerCase().indexOf(input) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
