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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Status Pengajuan HKI</title>
    <link rel="stylesheet" href="css/pengajuan.css">
</head>
<body>
    <h2>Rekapitulasi Hak Cipta Terdaftar</h2>
    <table>
        <tr>
            <th>Nama Pemilik</th>
            <th>Jenis Permohonan</th>
            <th>Jenis Ciptaan</th>
            <th>Sub Jenis Ciptaan</th>
            <th>Tanggal Pengumuman</th>
            <th>Judul</th>
            <th>Negara Pengumuman</th>
            <th>Kota Pengumuman</th>
            <th>Pencipta</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['jenis_permohonan']); ?></td>
                <td><?php echo htmlspecialchars($row['jenis_hak_cipta']); ?></td>
                <td><?php echo htmlspecialchars($row['sub_jenis_hak_cipta']); ?></td>
                <td><?php echo htmlspecialchars($row['tanggal_pengumuman']); ?></td>
                <td><?php echo htmlspecialchars($row['judul_hak_cipta']); ?></td>
                <td><?php echo htmlspecialchars($row['negara_pengumuman']); ?></td>
                <td><?php echo htmlspecialchars($row['kota_pengumuman']); ?></td>
                <td>
                    <button onclick="openModal('<?php echo $row['id']; ?>')" class="btn btn-info">Detail Pencipta</button>
                </td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="delete_hki.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <div>
        <a href="rekap_hki.php">Lihat Rekap HKI</a> |
        <a href="logout.php">Logout</a>
    </div>

    <!-- Modal untuk Detail Pencipta -->
    <div id="creatorModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Pencipta</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div id="creatorDetails"></div>
        </div>
    </div>

    <script>
        function openModal(id) {
            fetch('creator_details.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('creatorDetails').innerHTML = data;
                    document.getElementById('creatorModal').style.display = 'flex';
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal() {
            document.getElementById('creatorModal').style.display = 'none';
        }
    </script>

</body>
</html>

<div>
    <a href="logout.php">Logout</a>
</div>
<div>
    <a href="admin.php">Daftar pengajuan HKI</a>
</div>
