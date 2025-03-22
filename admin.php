<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// Ambil semua data pengajuan yang belum disetujui
$result = $conn->query("SELECT registrations.*, users.name FROM registrations 
                        JOIN users ON registrations.user_id = users.id 
                        WHERE registrations.status != 'Terdaftar'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Status Pengajuan HKI</title>
    <link rel="stylesheet" href="css/pengajuan.css">
    <link rel="stylesheet" href="css/modal.css">
</head>
<body>
    <h2>Daftar Pengajuan HKI</h2>
    <table>
        <tr>
            <th>Nama Pemohon</th>
            <th>Jenis Permohonan</th>
            <th>Jenis Ciptaan</th>
            <th>Sub Jenis Ciptaan</th>
            <th>Tanggal Pengumuman</th>
            <th>Judul</th>
            <th>Negara Pengumuman</th>
            <th>Kota Pengumuman</th>
            <th>Pencipta</th>
            <th>File</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td>
                    <a href="#" onclick="showProfile(<?php echo $row['user_id']; ?>)" class="profile-link">
                    <?php echo htmlspecialchars($row['name']); ?>
                    </a>
                </td>
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
                <td>
                    <?php
                        $reg_id = $row['id'];
                        $files = $conn->query("SELECT file_name, file_path FROM documents WHERE registration_id = '$reg_id'");
                        while ($file = $files->fetch_assoc()) {
                        echo '<a href="' . htmlspecialchars($file['file_path']) . '" download="' . htmlspecialchars($file['file_name']) . '" class="btn btn-download">Download</a><br>';
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] == 'Pending') { ?>
                        <form action="approve.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="file" name="certificate">
                            <button type="submit" class="btn btn-safe" onclick="return confirm('Yakin ingin menyetujui pengajuan ini?')">Upload & Setujui</button>
                            <a href="delete_hki.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </form>
                    <?php } elseif (!empty($row['certificate_path'])) { ?>
                        <a href="<?= $row['certificate_path'] ?>" class="btn btn-primary" download>Unduh</a>
                    <?php } else { ?>
                        <span>Belum ada sertifikat</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <div>
        <a href="profile.php">Profil</a> |
        <a href="rekap_hki.php">Lihat Rekap HKI</a> |
        <a href="logout.php">Logout</a>
    </div>

    <!-- Modal untuk Profil User -->
    <div id="profileModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Profil Pengguna</h2>
                <button class="close" onclick="closeProfileModal()">&times;</button>
            </div>
            <div id="profileDetails"></div>
        </div>
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
        //script detail profil user
        function showProfile(userId) {
            fetch('profile_details.php?id=' + userId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('profileDetails').innerHTML = data;
                    document.getElementById('profileModal').style.display = 'flex';
                })
                .catch(error => console.error('Error:', error));
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
        }

        //script detail pencipta
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
