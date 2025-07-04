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

    $query = $conn->query("SELECT id FROM registrations WHERE id = '$id'");
    if (!$query->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan!']);
        exit();
    }

    $update_query = "UPDATE registrations SET status='Ditinjau' WHERE id='$id'";

    if ($conn->query($update_query)) {
        echo json_encode(['success' => true, 'message' => 'Status berhasil diubah menjadi Ditinjau.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
}
?>