<?php
include 'config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: " . ($user['role'] == 'admin' ? 'admin.php' : 'dashboard.php'));
    } else {
        echo "Login gagal. Cek email atau password.";
    }
}
?>
