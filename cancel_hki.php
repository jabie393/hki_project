<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cek apakah pendaftaran milik user yang login
    $user_id = $_SESSION['user_id'];
    $check = $conn->query("SELECT * FROM registrations WHERE id = '$id' AND user_id = '$user_id' AND status = 'Pending'");
    if ($check->num_rows > 0) {
        // Hapus dokumen terkait
        $doc = $conn->query("SELECT file_path FROM documents WHERE registration_id = '$id'")->fetch_assoc();
        if ($doc && file_exists($doc['file_path'])) {
            unlink($doc['file_path']); // Hapus file dari server
        }
        $conn->query("DELETE FROM documents WHERE registration_id = '$id'");

        // Hapus pendaftaran
        $conn->query("DELETE FROM registrations WHERE id = '$id'");

        echo "Pendaftaran berhasil dibatalkan!";
    } else {
        echo "Pendaftaran tidak bisa dibatalkan!";
    }
}

header("Location: dashboard.php");
?>
