<?php
include '../config/config.php';
session_start();

// Set header JSON agar respons bisa diterima JS
header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nomor_pengajuan = $_POST['nomor_pengajuan'] ?? null;
    $nomor_sertifikat = $_POST['nomor_sertifikat'] ?? null;

    // Cari user_id dari registration
    $query = $conn->query("SELECT user_id FROM registrations WHERE id = '$id'");
    $data = $query->fetch_assoc();
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Pendaftaran tidak ditemukan!']);
        exit();
    }
    $user_id = $data['user_id'];

    $certificate_path = null;

    if (!empty($_FILES["certificate"]["name"])) {
        $upload_dir = "../uploads/users/$user_id/files/$id/certificates/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = "certificate_" . time() . "." . pathinfo($_FILES["certificate"]["name"], PATHINFO_EXTENSION);
        $full_path = $upload_dir . $file_name;
        $db_file_path = "uploads/users/$user_id/files/$id/certificates/$file_name";

        if (!move_uploaded_file($_FILES["certificate"]["tmp_name"], $full_path)) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengunggah sertifikat.']);
            exit();
        }

        $certificate_path = $db_file_path;
    }

    // Update status
    $update_query = "UPDATE registrations SET status='Terdaftar'";
    if ($certificate_path) {
        $update_query .= ", certificate_path='$certificate_path'";
    }
    if ($nomor_pengajuan) {
        $update_query .= ", nomor_pengajuan='$nomor_pengajuan'";
    }
    if ($nomor_sertifikat) {
        $update_query .= ", nomor_sertifikat='$nomor_sertifikat'";
    }
    $update_query .= " WHERE id='$id'";

    if ($conn->query($update_query)) {
        echo json_encode(['success' => true, 'message' => 'Pengajuan telah disetujui.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
}
?>