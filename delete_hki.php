<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus dokumen terkait
    $doc = $conn->query("SELECT file_path FROM documents WHERE registration_id = '$id'")->fetch_assoc();
    if ($doc && file_exists($doc['file_path'])) {
        unlink($doc['file_path']); // Hapus file dari server
    }
    $conn->query("DELETE FROM documents WHERE registration_id = '$id'");

    // Hapus pendaftaran
    $conn->query("DELETE FROM registrations WHERE id = '$id'");

    echo "Pendaftaran berhasil dihapus!";
}

header("Location: admin.php");
?>
