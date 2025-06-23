<?php
header('Content-Type: application/json');
include '../config/config.php';
session_start();

// Pastikan admin yang mengakses
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login");
    exit();
}

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan saat mengunggah sertifikat.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_certificate']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Ambil user_id dan sertifikat lama berdasarkan registration_id
    $query = $conn->query("SELECT user_id, certificate_path FROM registrations WHERE id = '$id'");
    $data = $query->fetch_assoc();
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Pengajuan hak cipta tidak ditemukan!']);
        exit();
    }

    $user_id = $data['user_id'];
    $old_certificate = $data['certificate_path']; // Sertifikat lama

    // Direktori penyimpanan sertifikat baru
    $upload_dir = "../uploads/users/$user_id/files/$id/certificates/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $fileTmpPath = $_FILES['new_certificate']['tmp_name'];
    $fileName = basename($_FILES['new_certificate']['name']);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

    // Validasi ekstensi file
    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Format file tidak diizinkan.']);
        exit;
    }

    // Hapus sertifikat lama jika ada
    if (!empty($old_certificate) && file_exists("../" . $old_certificate)) {
        unlink("../" . $old_certificate);
    }

    // Nama file baru untuk sertifikat
    $newFileName = 'certificate_' . time() . '.' . $ext;
    $targetFile = $upload_dir . $newFileName;

    // Pindahkan file yang di-upload
    if (move_uploaded_file($fileTmpPath, $targetFile)) {
        // Simpan path relatif di database
        $db_file_path = "uploads/users/$user_id/files/$id/certificates/$newFileName";
        $stmt = $conn->prepare("UPDATE registrations SET certificate_path = ? WHERE id = ?");
        $stmt->bind_param("si", $db_file_path, $id);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Sertifikat berhasil diperbarui.',
                'new_path' => $db_file_path
            ]);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data sertifikat.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah sertifikat.']);
        exit;
    }
}

echo json_encode($response);
