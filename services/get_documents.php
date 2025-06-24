<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$registration_id = $_GET['registration_id'] ?? '';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT d.file_name, d.file_path 
    FROM documents d
    JOIN registrations r ON d.registration_id = r.id
    WHERE d.registration_id = ? AND r.user_id = ?");
$stmt->bind_param("ii", $registration_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();

if ($data) {
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false]);
}