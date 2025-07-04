<?php
include '../config/config.php';
session_start();
header('Content-Type: application/json');

// Validasi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak!']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_FILES['file'])) {
    $id = intval($_POST['id']);
    $file = $_FILES['file'];

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
        // Hapus semua file di dalam folder certificate
        $files = glob($cert_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        // Hapus foldernya
        @rmdir($cert_dir);
    }

    $rejection_path = null;

    if (!empty($file['name'])) {
        // Buat direktori jika belum ada
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

        // Buat nama file baru
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = "rejection_" . time() . "." . $file_ext;
        $full_path = $upload_dir . $file_name;
        $db_path = "uploads/users/$user_id/files/$id/rejection_file/$file_name";

        // Pindahkan file
        if (!move_uploaded_file($file['tmp_name'], $full_path)) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengunggah file penolakan.']);
            exit();
        }

        $rejection_path = $db_path;
    }

    // Simpan ke database: status, waktu tolak, path file, dan nomor_pengajuan
    $update_query = "UPDATE registrations SET status = 'Ditolak', rejected_at = NOW(), rejection_path = ?";
    if ($nomor_pengajuan === "" || $nomor_pengajuan === null) {
        $update_query .= ", nomor_pengajuan = NULL";
    } else {
        $update_query .= ", nomor_pengajuan = ?";
    }
    $update_query .= " WHERE id = ?";

    if ($nomor_pengajuan === "" || $nomor_pengajuan === null) {
        $update = $conn->prepare($update_query);
        $update->bind_param("si", $rejection_path, $id);
    } else {
        $update = $conn->prepare($update_query);
        $update->bind_param("ssi", $rejection_path, $nomor_pengajuan, $id);
    }
    $update->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Pengajuan berhasil ditolak dan file disimpan. Akan dihapus otomatis dalam 7 hari.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
}
