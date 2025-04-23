<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    $check = $conn->query("SELECT * FROM registrations WHERE id = '$id' AND user_id = '$user_id' AND status = 'Pending'");
    if ($check->num_rows > 0) {
        // Hapus file & database
        $doc_query = $conn->query("SELECT file_path FROM documents WHERE registration_id = '$id'");
        while ($doc = $doc_query->fetch_assoc()) {
            if ($doc['file_path'] && file_exists($doc['file_path'])) {
                unlink($doc['file_path']);
            }
        }

        $conn->query("DELETE FROM documents WHERE registration_id = '$id'");

        $folder_path = "../uploads/users/$user_id/files/$id/";
        if (is_dir($folder_path)) {
            $files = array_diff(scandir($folder_path), ['.', '..']);
            foreach ($files as $file) {
                unlink($folder_path . $file);
            }
            rmdir($folder_path);
        }

        $conn->query("DELETE FROM registrations WHERE id = '$id'");

        echo "Pendaftaran berhasil dibatalkan!";
    } else {
        http_response_code(400);
        echo "Tidak bisa dibatalkan.";
    }
}
?>
