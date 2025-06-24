<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id = $_GET['id'] ?? '';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM registrations WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    // Ambil data pencipta
    $stmt2 = $conn->prepare("SELECT * FROM creators WHERE registration_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $creators = [];
    $res2 = $stmt2->get_result();
    while ($c = $res2->fetch_assoc()) {
        $creators[] = $c;
    }
    $data['creators'] = $creators;
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
}