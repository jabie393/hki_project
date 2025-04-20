<?php
include '../config/config.php';
session_start();

header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized!']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['nomor_permohonan'])) {
    $id = $_POST['id'];
    $nomor_permohonan = trim($_POST['nomor_permohonan']);

    // Pastikan Nomor Permohonan tidak kosong
    if (empty($nomor_permohonan)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor Permohonan tidak boleh kosong!']);
        exit();
    }

    // Update database
    $stmt = $conn->prepare("UPDATE registrations SET nomor_permohonan = ? WHERE id = ?");
    $stmt->bind_param("si", $nomor_permohonan, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Nomor Permohonan berhasil diperbarui!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Nomor Permohonan!']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
}
?>