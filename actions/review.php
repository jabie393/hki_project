<?php
include '../config/config.php';
session_start();

header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nomor_pengajuan = $_POST['nomor_pengajuan'] ?? null;

    // Ambil user_id dari database
    $stmt = $conn->prepare("SELECT user_id FROM registrations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan hak cipta tidak ditemukan!']);
        exit();
    }

    $user_id = $data['user_id'];

    // Set nomor_sertifikat dan certificate_path ke NULL di database
    $null_cert = $conn->prepare("UPDATE registrations SET nomor_sertifikat = NULL, certificate_path = NULL WHERE id = ?");
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

    // Update status menjadi Ditinjau dan nomor_pengajuan jika ada
    $update_query = "UPDATE registrations SET status='Ditinjau'";
    $update_query .= ", nomor_pengajuan=" . (empty($nomor_pengajuan) ? "NULL" : "'" . $conn->real_escape_string($nomor_pengajuan) . "'");
    $update_query .= " WHERE id='$id'";

    if ($conn->query($update_query)) {
        echo json_encode(['success' => true, 'message' => 'Pengajuan telah ditandai sebagai Ditinjau.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
}
?>