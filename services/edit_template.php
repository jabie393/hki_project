<?php
include '../config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $docType = $_POST['doc_type'];
    $fileName = $_FILES['file']['name'];
    $fileTmp = $_FILES['file']['tmp_name'];
    $targetDir = '../uploads/template_documents/'; // Folder penyimpanan tetap di luar root
    $targetFile = $targetDir . basename($fileName);
    $dbFilePath = 'uploads/template_documents/' . basename($fileName); // Path yang disimpan di database tanpa '../'    

    if (!is_dir($targetDir))
        mkdir($targetDir, 0777, true);

    // Check if the document type already exists
    $result = $conn->query("SELECT COUNT(*) as count FROM template_documents WHERE doc_type='$docType'");
    $row = $result->fetch_assoc();
    if ($row['count'] >= 1) {
        echo 'Dokumen ini sudah diunggah sebelumnya.';
    } else {
        if (move_uploaded_file($fileTmp, $targetFile)) {
            // Simpan path tanpa '../' ke database
            $conn->query("INSERT INTO template_documents (doc_type, filename, filepath) VALUES ('$docType', '$fileName', '$dbFilePath')");
            echo 'Upload berhasil.';
        } else {
            echo 'Upload gagal.';
        }
    }
    header('Location: ../template.php');
    exit();
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $docType = $_GET['delete'];
    $result = $conn->query("SELECT * FROM template_documents WHERE doc_type='$docType'");
    if ($row = $result->fetch_assoc()) {
        unlink('../' . $row['filepath']); // Tambahkan '../' agar menghapus dari lokasi sebenarnya
        $conn->query("DELETE FROM template_documents WHERE doc_type='$docType'");
    }
    header('Location: ../template.php');
    exit();
}
?>