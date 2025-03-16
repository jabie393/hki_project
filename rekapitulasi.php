<?php
include 'config/config.php';

$result = $conn->query("SELECT registrations.*, users.name FROM registrations JOIN users ON registrations.user_id = users.id WHERE registrations.status = 'Terdaftar'");

echo "<h2>Rekapitulasi Hak Cipta yang Sudah Terdaftar</h2>";
echo "<table border='1'>";
echo "<tr><th>Pencipta</th><th>Pemegang Hak Cipta</th><th>Judul Hak Cipta</th><th>Jenis</th><th>Sub Jenis</th><th>Status</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
    <td><ul>";
    foreach (json_decode($row['nama_pencipta'], true) as $pencipta) {
        echo "<li>" . htmlspecialchars($pencipta) . "</li>";
    }
    echo "</ul>
    </td>
    <th>Universitas Islam Raden Rahmat Malang</th>
    <td>{$row['judul_hak_cipta']}</td>
    <td>{$row['jenis_hak_cipta']}</td>
    <td>{$row['sub_jenis_hak_cipta']}</td>
    <td>{$row['status']}</td>
    </tr>";
}

echo "</table>";
?>
<br>
    <button onclick="kembali()">Kembali</button>

<script>
    function kembali() {
        window.history.back();
    }
</script>
