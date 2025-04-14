<?php
include '../config/config.php';
session_start();

if (isset($_GET['doc_type'])) {
    $docType = $_GET['doc_type'];
    $result = $conn->query("SELECT * FROM template_documents WHERE doc_type='$docType'");

    if ($row = $result->fetch_assoc()) {
        $filePath = '../' . $row['filepath'];

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo 'File tidak ditemukan.';
        }
    } else {
        echo 'Dokumen tidak tersedia.';
    }
}
?>