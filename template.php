<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM template_documents");
if (!$result) {
    die('Query gagal: ' . $conn->error);
}
?>

<head>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/template.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<div id="template-page">
    <main>
        <h2>Upload Dokumen</h2>
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="custom-select-wrapper">
                <select name="doc_type" required>
                    <option value="surat_pernyataan">Surat Pernyataan</option>
                    <option value="surat_pengalihan_hak">Surat Pengalihan Hak</option>
                </select>
                <span class="custom-arrow">
                    <svg width="12" height="12" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5" stroke="#4f46e5" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
            </div>

            <div class="custom-file-upload">
                <input type="file" name="file" id="fileInput" required>
                <div class="upload-box">
                    <span class="upload-icon">📁</span>
                    <span id="upload-text">Klik untuk memilih file</span>
                </div>
            </div>
            <span id="file-name">Belum ada file dipilih</span>
            <button type="submit">Upload</button>
        </form>

        <h2>Dokumen Tersedia</h2>
        <ul id="document-list">
            <?php
            $types = ['surat_pernyataan', 'surat_pengalihan_hak'];
            foreach ($types as $type):
                $res = $conn->query("SELECT * FROM template_documents WHERE doc_type = '$type'");
                $row = $res->fetch_assoc();
                ?>
                <li data-doc-type="<?= $type; ?>">
                    <?php
                    $label = ucwords(str_replace('_', ' ', $type)); // menghapus "_"
                    ?>
                    <strong><?= $label; ?>:</strong><br>
                    <?php if ($row): ?>
                        <span class="doc-actions">
                            <a class="download-btn" href="<?= $row['filepath']; ?>" download><?= $row['filename']; ?></a>
                            <a class="delete-btn" href="#" onclick="deleteDocument('<?= $type; ?>'); return false;">Hapus</a>
                        </span>
                    <?php else: ?>
                        <span class="no-file">📄 Dokumen belum diunggah.</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="nav-links">
            <a href="profile.php">Profil</a> |
            <a href="admin.php">Dashboard</a> |
            <a href="rekap_hki.php">Rekap HKI</a> |
            <a href="announcement.php">Pengumuman</a> |
            <a href="reset_password.php">Reset Password</a> |
            <a href="services/logout.php">Logout</a>
        </div>
    </main>
    <script src="js/template.js"></script>
</div>