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

    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/template.css">
    <link rel="stylesheet" href="css/alert.css">
    <link rel="stylesheet" href="css/select2.css">
</head>

<div id="template-page">
    <main>
        <h2>Upload Dokumen</h2>
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="custom-select-wrapper">
                <select id="template" name="doc_type" required
                    oninvalid="this.setCustomValidity('Silakan pilih template dokumen.')"
                    oninput="this.setCustomValidity('')">
                    <option value="">-- Pilih Template Dokumen --</option>
                    <option value="surat_pernyataan">Surat Pernyataan</option>
                    <option value="surat_pengalihan_hak">Surat Pengalihan Hak</option>
                </select>
            </div>

            <div class="custom-file-upload">
                <input type="file" name="file" id="fileInput" accept=".pdf,.doc,.docx" required
                    oninvalid="this.setCustomValidity('Silakan pilih file dokumen.')"
                    oninput="this.setCustomValidity('')">
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