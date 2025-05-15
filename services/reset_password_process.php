<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['new_username'];
    $new_email = $_POST['new_email'];
    $new_role = $_POST['new_role']; // Ambil role dari input
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;

    // Validasi email
    $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $email_check->bind_param("si", $new_email, $user_id);
    $email_check->execute();
    $email_check->store_result();
    if ($email_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email sudah digunakan oleh user lain.']);
        exit();
    }
    $email_check->close();

    // Validasi username
    $username_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $username_check->bind_param("si", $new_username, $user_id);
    $username_check->execute();
    $username_check->store_result();
    if ($username_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan oleh user lain.']);
        exit();
    }
    $username_check->close();

    // Update username, email, dan role
    $query = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
    $query->bind_param("sssi", $new_username, $new_email, $new_role, $user_id);
    $query->execute();

    // Update password jika diisi
    if ($new_password) {
        $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $query->bind_param("si", $new_password, $user_id);
        $query->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Data user berhasil diperbarui!', 'redirect' => 'reset_password.php']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
exit();
