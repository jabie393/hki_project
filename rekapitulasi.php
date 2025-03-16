<?php
include 'config/config.php';

$result = $conn->query("SELECT registrations.*, users.name FROM registrations JOIN users ON registrations.user_id = users.id WHERE registrations.status = 'Terdaftar'");

echo "<h2>Rekapitulasi Hak Cipta yang Sudah Terdaftar</h2>";
echo "<table border='1'>";
echo "<tr><th>Nama Pemohon</th><th>Judul Hak Cipta</th><th>Deskripsi</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['name']}</td>
        <td>{$row['judul_hak_cipta']}</td>
        <td>{$row['deskripsi']}</td>
    </tr>";
}

echo "</table>";
?>
