<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// Ambil semua pendaftaran yang sudah disetujui
$result = $conn->query("SELECT registrations.*, users.name FROM registrations 
                        JOIN users ON registrations.user_id = users.id 
                        WHERE registrations.status = 'Terdaftar'");

echo "<h2>Rekapitulasi Hak Cipta Terdaftar</h2>";
echo "<table border='1'>";
echo "<tr><th>Nama</th><th>Judul</th><th>Status</th><th>Aksi</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['name']}</td>
        <td>{$row['judul_hak_cipta']}</td>
        <td>{$row['status']}</td>
        <td>
            <a href='delete_hki.php?id={$row['id']}' onclick=\"return confirm('Yakin ingin menghapus?')\">Hapus</a>
        </td>
    </tr>";
}
echo "</table>";
?>
<div>
    <a href="logout.php">Logout</a>
</div>
<div>
    <a href="admin.php">Daftar pengajuan HKI</a>
</div>
