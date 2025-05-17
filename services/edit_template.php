<?php
include '../config/config.php';
session_start();
header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docType = $_POST['doc_type'];
    $fileName = $_FILES['file']['name'];
    $fileTmp = $_FILES['file']['tmp_name'];
    $targetDir = '../uploads/template_documents/';
    $targetFile = $targetDir . basename($fileName);
    $dbFilePath = 'uploads/template_documents/' . basename($fileName);

    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $check = $conn->query("SELECT COUNT(*) as count FROM template_documents WHERE doc_type='$docType'");
    $row = $check->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Dokumen ini sudah diunggah sebelumnya.']);
    } else {
        if (move_uploaded_file($fileTmp, $targetFile)) {
            $conn->query("INSERT INTO template_documents (doc_type, filename, filepath) VALUES ('$docType', '$fileName', '$dbFilePath')");
            echo json_encode(['status' => 'success', 'message' => 'Dokumen berhasil diunggah.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Dokumen gagal diunggah.']);
        }
    }
    exit();
}

if (isset($_GET['delete'])) {
    $docType = $_GET['delete'];
    $res = $conn->query("SELECT * FROM template_documents WHERE doc_type='$docType'");
    if ($row = $res->fetch_assoc()) {
        unlink('../' . $row['filepath']);
        $conn->query("DELETE FROM template_documents WHERE doc_type='$docType'");
        echo json_encode(['status' => 'success', 'message' => 'Dokumen berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Dokumen tidak ditemukan.']);
    }
    exit();
}
?>
