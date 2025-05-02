<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu.']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT password FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$query->close();

$new_username = $_POST['new_username'];
$new_email = $_POST['new_email'];
$new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;
$old_password = $_POST['old_password'];

// Cek password lama
if (!password_verify($old_password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Password lama salah!']);
    exit();
}

// Cek email dan username sudah dipakai?
$email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$email_check->bind_param("si", $new_email, $user_id);
$email_check->execute();
$email_check->store_result();
if ($email_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email sudah digunakan oleh user lain!']);
    exit();
}
$email_check->close();

$username_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$username_check->bind_param("si", $new_username, $user_id);
$username_check->execute();
$username_check->store_result();
if ($username_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username sudah digunakan oleh user lain!']);
    exit();
}
$username_check->close();

// Update username dan email
$query = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
$query->bind_param("ssi", $new_username, $new_email, $user_id);
$query->execute();
$query->close();

// Update password jika ada
if ($new_password) {
    $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $query->bind_param("si", $new_password, $user_id);
    $query->execute();
    $query->close();
}

// Perbarui session username
$_SESSION['user_username'] = $new_username;

echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui!']);

