<?php
include '../config/config.php';
session_start();

header('Content-Type: application/json');

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak!']);
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cek apakah pengajuan ditemukan
    $stmt = $conn->prepare("SELECT id FROM registrations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update status menjadi 'Ditolak' dan isi rejected_at
        $update = $conn->prepare("UPDATE registrations SET status = 'Ditolak', rejected_at = NOW() WHERE id = ?");
        $update->bind_param("i", $id);
        $update->execute();

        echo json_encode(['success' => true, 'message' => 'Status pengajuan diubah menjadi Ditolak. Akan dihapus otomatis dalam 7 hari.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pengajuan tidak ditemukan.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid!']);
}
