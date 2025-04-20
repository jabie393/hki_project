<?php
include '../config/config.php';
session_start();

header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized!']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['nomor_sertifikat'])) {
    $id = $_POST['id'];
    $nomor_sertifikat = trim($_POST['nomor_sertifikat']);

    // Pastikan Nomor Sertifikat tidak kosong
    if (empty($nomor_sertifikat)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor Sertifikat tidak boleh kosong!']);
        exit();
    }

    // Update database
    $stmt = $conn->prepare("UPDATE registrations SET nomor_sertifikat = ? WHERE id = ?");
    $stmt->bind_param("si", $nomor_sertifikat, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Nomor Sertifikat berhasil diperbarui!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Nomor Sertifikat!']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
}
?>