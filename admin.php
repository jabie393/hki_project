<!-- Flow FE -->
<!-- ADMIN -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil semua data pengajuan yang belum disetujui
$result = $conn->query("SELECT registrations.*, users.username FROM registrations 
                        JOIN users ON registrations.user_id = users.id 
                        WHERE registrations.status != 'Terdaftar'");
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/hki.css">
    <link rel="stylesheet" href="css/modal.css">
</head>

<!-- admin.php (HTML Table Layout) -->
<div id="hki-page">
    <h2>Daftar Pengajuan HKI</h2>
    <div class="table-wrapper">
        <table id="hki-table">
            <thead>
                <tr>
                    <th>Nama Pemohon</th>
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
                    <th>No. Permohonan</th>
                    <th>No. Sertifikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <form id="form_<?= $row['id'] ?>" enctype="multipart/form-data">
                        <tr id="row_<?= $row['id'] ?>">
                            <td><a href="#" onclick="showProfile(<?= $row['user_id'] ?>)"
                                    class="profile-link"><?= htmlspecialchars($row['username']) ?></a></td>
                            <td><?= htmlspecialchars($row['jenis_permohonan']) ?></td>
                            <td><?= htmlspecialchars($row['jenis_hak_cipta']) ?></td>
                            <td><?= htmlspecialchars($row['sub_jenis_hak_cipta']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pengumuman']) ?></td>
                            <td><?= htmlspecialchars($row['judul_hak_cipta']) ?></td>
                            <td><button type="button"
                                    onclick="openDescriptionModal('<?= htmlspecialchars($row['deskripsi']) ?>')"
                                    class="btn btn-info">Lihat</button></td>
                            <td>
                                <div><strong>Negara:</strong> <?= htmlspecialchars($row['negara_pengumuman']) ?></div>
                                <div><strong>Kota:</strong> <?= htmlspecialchars($row['kota_pengumuman']) ?></div>
                            </td>
                            <td><button type="button" onclick="openModal('<?= $row['id'] ?>')" class="btn btn-info">Detail
                                    Pencipta</button></td>
                            <td>
                                <?php
                                $reg_id = $row['id'];
                                $files = $conn->query("SELECT file_name, file_path FROM documents WHERE registration_id = '$reg_id'");
                                while ($file = $files->fetch_assoc()) {
                                    echo '<a href="' . htmlspecialchars($file['file_path']) . '" download="' . htmlspecialchars($file['file_name']) . '" class="btn btn-download">Download</a><br>';
                                }
                                ?>
                            </td>
                            <td><span
                                    class="badge badge-<?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                            </td>
                            <td><input type="text" name="nomor_permohonan" class="input-field" placeholder="Opsional"></td>
                            <td><input type="text" name="nomor_sertifikat" class="input-field" placeholder="Opsional"></td>
                            <td>
                                <div class="input-wrapper">
                                    <label for="certificate_<?= $row['id'] ?>" class="file-label">Sertifikat</label>
                                    <div class="input-file-wrapper">
                                        <input type="file" id="certificate_<?= $row['id'] ?>" name="certificate"
                                            class="input-file">
                                    </div>
                                    <button type="button" class="btn btn-safe approve-btn" data-id="<?= $row['id'] ?>"
                                        data-form="form_<?= $row['id'] ?>" data-user="<?= $row['user_id'] ?>">
                                        ✔ Setujui
                                    </button>
                                    <button class="btn btn-danger delete-btn" data-id="<?= $row['id'] ?>"
                                        data-row="row_<?= $row['id'] ?>">
                                        ✖ Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </form>
                <?php } ?>
            </tbody>
        </table>
    </div>
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