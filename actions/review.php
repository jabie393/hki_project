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
    $nomor_sertifikat = $_POST['nomor_sertifikat'] ?? null;

    $query = $conn->query("SELECT user_id FROM registrations WHERE id = '$id'");
    $data = $query->fetch_assoc();
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan hak cipta tidak ditemukan!']);
        exit();
    }

    // Update status menjadi Ditinjau
    $update_query = "UPDATE registrations SET status='Ditinjau'";
    $update_query .= ", nomor_pengajuan=" . (empty($nomor_pengajuan) ? "NULL" : "'" . $conn->real_escape_string($nomor_pengajuan) . "'");
    $update_query .= ", nomor_sertifikat=" . (empty($nomor_sertifikat) ? "NULL" : "'" . $conn->real_escape_string($nomor_sertifikat) . "'");
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