<?php
include '../config/config.php';
session_start();

header('Content-Type: application/json');

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak!']);
    exit();
}

// Fungsi rekursif untuk menghapus folder beserta isinya
function deleteFolder($folder_path)
{
    if (!is_dir($folder_path))
        return;

    $files = array_diff(scandir($folder_path), ['.', '..']);
    foreach ($files as $file) {
        $file_path = $folder_path . DIRECTORY_SEPARATOR . $file;
        is_dir($file_path) ? deleteFolder($file_path) : unlink($file_path);
    }

    rmdir($folder_path);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $user_query = $conn->query("SELECT user_id FROM registrations WHERE id = '$id'");
    if ($user_query->num_rows > 0) {
        $user_data = $user_query->fetch_assoc();
        $user_id = $user_data['user_id'];

        $doc_query = $conn->query("SELECT file_path FROM documents WHERE registration_id = '$id'");
        while ($doc = $doc_query->fetch_assoc()) {
            if ($doc['file_path'] && file_exists($doc['file_path'])) {
                unlink($doc['file_path']);
            }
        }

        $conn->query("DELETE FROM documents WHERE registration_id = '$id'");

        $folder_path = "../uploads/users/$user_id/files/$id/";
        deleteFolder($folder_path);

        $conn->query("DELETE FROM registrations WHERE id = '$id'");

        echo json_encode(['success' => true, 'message' => 'Hak cipta berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pengajuan hak cipta tidak ditemukan!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid!']);
}
