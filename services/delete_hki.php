<?php
include '../config/config.php';
session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.html");
    exit();
}

// Fungsi rekursif untuk menghapus folder beserta isinya
function deleteFolder($folder_path) {
    if (!is_dir($folder_path)) {
        return;
    }
    
    $files = array_diff(scandir($folder_path), array('.', '..'));
    foreach ($files as $file) {
        $file_path = $folder_path . DIRECTORY_SEPARATOR . $file;
        if (is_file($file_path)) {
            unlink($file_path);
        } elseif (is_dir($file_path)) {
            deleteFolder($file_path); // Hapus folder dalamnya jika ada subfolder
        }
    }
    
    rmdir($folder_path); // Hapus folder setelah kosong
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil user_id berdasarkan registration_id
    $user_query = $conn->query("SELECT user_id FROM registrations WHERE id = '$id'");
    if ($user_query->num_rows > 0) {
        $user_data = $user_query->fetch_assoc();
        $user_id = $user_data['user_id'];

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

        // Hapus folder pendaftaran secara rekursif
        $folder_path = "../uploads/users/$user_id/files/$id/";
        deleteFolder($folder_path);

        // Hapus pendaftaran dari database
        $conn->query("DELETE FROM registrations WHERE id = '$id'");

        echo "Pendaftaran berhasil dihapus!";
    } else {
        echo "Pendaftaran tidak ditemukan!";
    }
}

$redirect_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../admin.php';
header("Location: $redirect_page");
exit();
?>
