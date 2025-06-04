<?php
include '../config/config.php';
session_start();

header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized!']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['nomor_pengajuan'])) {
    $id = $_POST['id'];
    $nomor_pengajuan = trim($_POST['nomor_pengajuan']);

    // Pastikan Nomor Pengajuan tidak kosong
    if (empty($nomor_pengajuan)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor Pengajuan tidak boleh kosong!']);
        exit();
    }

    // Update database
    $stmt = $conn->prepare("UPDATE registrations SET nomor_pengajuan = ? WHERE id = ?");
    $stmt->bind_param("si", $nomor_pengajuan, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Nomor Pengajuan berhasil diperbarui!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Nomor Pengajuan!']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
}
?>