<!-- Flow FE -->
<!-- ADMIN -->
<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Ambil semua pendaftaran yang sudah disetujui dengan filter pencarian
$query = "SELECT registrations.*, users.username FROM registrations 
          JOIN users ON registrations.user_id = users.id 
          WHERE registrations.status = 'Terdaftar' 
          AND (users.username LIKE '%$search%' 
          OR registrations.nomor_permohonan LIKE '%$search%' 
          OR registrations.jenis_permohonan LIKE '%$search%' 
          OR registrations.jenis_hak_cipta LIKE '%$search%' 
          OR registrations.sub_jenis_hak_cipta LIKE '%$search%' 
          OR registrations.tanggal_pengumuman LIKE '%$search%' 
          OR registrations.judul_hak_cipta LIKE '%$search%' 
          OR registrations.negara_pengumuman LIKE '%$search%' 
          OR registrations.kota_pengumuman LIKE '%$search%'
          OR registrations.nomor_sertifikat LIKE '%$search%')";
$result = $conn->query($query);
?>

<head>
    <link rel="stylesheet" href="css/pengajuan.css">
    <link rel="stylesheet" href="css/modal.css">
</head>

<div id="admin-section">
    <h2>Rekapitulasi Hak Cipta Terdaftar</h2>

    <!-- Form Pencarian -->
    <form method="GET" class="search-form">
        <input type="text" name="search" class="input-field" placeholder="Cari Data Hak Cipta"
            value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-info">Cari</button>
    </form>

    <div class="table-wrapper">
        <table id="admin-hki-table">
            <thead>
                <tr>
                    <th>Nama Pemilik</th>
                    <th>Nomor Permohonan</th>
                    <th>Jenis Permohonan</th>
                    <th>Jenis Ciptaan</th>
                    <th>Sub Jenis Ciptaan</th>
                    <th>Tanggal Pengumuman</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Tempat Pengumuman</th>
                    <th>Pencipta</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Sertifikat</th>
                    <th>Edit Sertifikat</th>
                    <th>Nomor Sertifikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="#" onclick="showProfile(<?php echo $row['user_id']; ?>)" class="profile-link">
                                <?php echo htmlspecialchars($row['username']); ?>
                            </a>
                        </td>
                        <td>
                            <form action="services/edit_nomor_permohonan.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <input type="text" name="nomor_permohonan"
                                    value="<?= htmlspecialchars($row['nomor_permohonan'] ?? ''); ?>"
                                    placeholder="Nomor Permohonan" class="input-field" required>
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Yakin ingin mengedit nomor permohonan?')">Edit & Simpan</button>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($row['jenis_permohonan']); ?></td>
                        <td><?= htmlspecialchars($row['jenis_hak_cipta']); ?></td>
                        <td><?= htmlspecialchars($row['sub_jenis_hak_cipta']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pengumuman']); ?></td>
                        <td><?= htmlspecialchars($row['judul_hak_cipta']); ?></td>
                        <td>
                            <button onclick="openDescriptionModal('<?php echo htmlspecialchars($row['deskripsi']); ?>')"
                                class="btn btn-info">Lihat</button>
                        </td>
                        <td>
                            <div><strong>Negara:</strong> <?= htmlspecialchars($row['negara_pengumuman']); ?></div>
                            <div><strong>Kota:</strong> <?= htmlspecialchars($row['kota_pengumuman']); ?></div>
                        </td>
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
                        <td>
                            <span class="badge badge-<?= strtolower($row['status']) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($row['certificate_path'])) { ?>
                                <a href="<?= $row['certificate_path'] ?>" class="btn btn-download" download>Download</a>
                            <?php } else { ?>
                                <span>Belum ada sertifikat</span>
                            <?php } ?>
                        </td>
                        <td>
                        <div class="input-wrapper">
                            <form action="services/edit_certificate.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="input-file-wrapper">
                                    <input type="file" name="new_certificate" class="input-file" required>
                                </div>
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Yakin ingin mengedit sertifikat?')">Edit</button>
                            </form>
                            </div>
                        </td>
                        <td>
                            <form action="services/edit_nomor_sertifikat.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <input type="text" name="nomor_sertifikat"
                                    value="<?= htmlspecialchars($row['nomor_sertifikat'] ?? ''); ?>"
                                    placeholder="Nomor Sertifikat" class="input-field" required>
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Yakin ingin mengedit nomor permohonan?')">Edit & Simpan</button>
                            </form>
                        </td>
                        <td>
                            <a href="services/delete_hki.php?id=<?= $row['id']; ?>" class="btn btn-danger"
                                onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Modal untuk Profil User -->
    <div id="modal-page">
    <div id="profileModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Profil Pengguna</h2>
                <button class="close" onclick="closeProfileModal()">&times;</button>
            </div>
            <div id="profileDetails"></div>
        </div>
    </div>
    </div>

    <!-- Modal untuk Deskripsi -->
    <div id="modal-page">
        <div id="descriptionModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Deskripsi Ciptaan</h2>
                    <button class="close" onclick="closeDescriptionModal()">&times;</button>
                </div>
                <div id="descriptionDetails"></div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Detail Pencipta -->
    <div id="modal-page">
        <div id="creatorModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Detail Pencipta</h2>
                    <button class="close" onclick="closeModal()">&times;</button>
                </div>
                <div id="creatorDetails"></div>
            </div>
        </div>
    </div>

<script src="js/hki.js"></script>