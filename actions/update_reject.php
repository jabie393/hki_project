<?php
include '../config/config.php';
session_start();
header('Content-Type: application/json');

// Validasi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak!']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;
    $has_new_file = isset($_POST['has_new_file']) ? $_POST['has_new_file'] : '0';

    // Ambil nomor_pengajuan dari POST jika ada
    $nomor_pengajuan = isset($_POST['nomor_pengajuan']) ? trim($_POST['nomor_pengajuan']) : null;

    // Ambil user_id dan path lama dari database
    $stmt = $conn->prepare("SELECT user_id, rejection_path FROM registrations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan tidak ditemukan.']);
        exit();
    }

    $data = $result->fetch_assoc();
    $user_id = $data['user_id'];
    $old_rejection_path = $data['rejection_path'];

    // Set certificate_path ke NULL di database
    $null_cert = $conn->prepare("UPDATE registrations SET certificate_path = NULL WHERE id = ?");
    $null_cert->bind_param("i", $id);
    $null_cert->execute();

    // Hapus folder certificate jika ada
    $cert_dir = "../uploads/users/$user_id/files/$id/certificate/";
    if (is_dir($cert_dir)) {
        $files = glob($cert_dir . '*');
        foreach ($files as $file_item) {
            if (is_file($file_item)) {
                @unlink($file_item);
            }
        }
        @rmdir($cert_dir);
    }

    $rejection_path = $old_rejection_path;

    // Jika ada file baru, upload dan update rejection_path
    if ($has_new_file === '1' && !empty($file['name'])) {
        $upload_dir = "../uploads/users/$user_id/files/$id/rejection_file/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Hapus file lama jika ada
        if (!empty($old_rejection_path)) {
            $old_file = "../" . $old_rejection_path;
            if (file_exists($old_file)) {
                @unlink($old_file);
            }
        }

        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = "rejection_" . time() . "." . $file_ext;
        $full_path = $upload_dir . $file_name;
        $db_path = "uploads/users/$user_id/files/$id/rejection_file/$file_name";

        if (!move_uploaded_file($file['tmp_name'], $full_path)) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengunggah file penolakan.']);
            exit();
        }

        $rejection_path = $db_path;
    }

    // Bangun query update dinamis seperti update_approve.php
    $update_query = "UPDATE registrations SET ";
    $fields = [];

    // Update rejection_path jika ada file baru
    if ($has_new_file === '1' && !empty($file['name'])) {
        $fields[] = "rejection_path='" . $conn->real_escape_string($rejection_path) . "'";
    }

    // Update nomor_pengajuan
    if ($nomor_pengajuan === "" || $nomor_pengajuan === null) {
        $fields[] = "nomor_pengajuan=NULL";
    } else {
        $fields[] = "nomor_pengajuan='" . $conn->real_escape_string($nomor_pengajuan) . "'";
    }

    if (empty($fields)) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada data yang diubah.']);
        exit();
    }

    $update_query .= implode(", ", $fields) . " WHERE id='$id'";

    if ($conn->query($update_query)) {
        $response = ['success' => true, 'message' => 'Penolakan berhasil diperbarui.'];
        if ($has_new_file === '1' && !empty($file['name'])) {
            $response['new_rejection_path'] = $rejection_path;
        }
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data penolakan.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
}
