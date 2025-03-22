<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cek apakah pendaftaran milik user yang login
    $user_id = $_SESSION['user_id'];
    $check = $conn->query("SELECT * FROM registrations WHERE id = '$id' AND user_id = '$user_id' AND status = 'Pending'");

    if ($check->num_rows > 0) {
        // Ambil daftar file yang terkait
        $doc_query = $conn->query("SELECT file_path FROM documents WHERE registration_id = '$id'");

        // Hapus semua file terkait
        while ($doc = $doc_query->fetch_assoc()) {
            if ($doc['file_path'] && file_exists($doc['file_path'])) {
                unlink($doc['file_path']); // Hapus file dari server
            }
        }

        // Hapus data dokumen dari database
        $conn->query("DELETE FROM documents WHERE registration_id = '$id'");

        // Hapus folder pendaftaran jika kosong
        $folder_path = "../uploads/users/$user_id/files/$id/";
        if (is_dir($folder_path)) {
            // Hapus semua file dalam folder (jika ada)
            $files = array_diff(scandir($folder_path), array('.', '..'));
            foreach ($files as $file) {
                unlink($folder_path . $file);
            }
            // Hapus folder setelah kosong
            rmdir($folder_path);
        }

        // Hapus pendaftaran
        $conn->query("DELETE FROM registrations WHERE id = '$id'");

        echo "Pendaftaran berhasil dibatalkan!";
    } else {
        echo "Pendaftaran tidak bisa dibatalkan!";
    }
}

header("Location: ../status_pengajuan.php");
exit();
?>
