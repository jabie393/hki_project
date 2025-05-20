<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login");
    exit();
}

$result = $conn->query("SELECT * FROM template_documents");
if (!$result) {
    die('Query gagal: ' . $conn->error);
}
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/template.css">
    <link rel="stylesheet" href="css/alert.css">
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
                <input type="file" name="file" id="fileInput" accept=".pdf,.doc,.docx" required>
                <label for="fileInput" class="upload-box">
                    <span class="upload-icon">📁</span>
                    <span id="upload-text">Klik untuk memilih dokumen</span>
                </label>
            </div>
            <span id="file-name">Belum ada dokumen yang dipilih</span>
            <button type="submit">Upload</button>
        </form>

        <h2>Dokumen Tersedia</h2>
        <ul id="document-list">
            <?php include 'services/get_templates.php'; ?>
        </ul>
    </main>
    <script src="js/template.js"></script>
</div>