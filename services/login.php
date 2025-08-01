<?php
include '../config/config.php';
session_start();

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk mendapatkan data pengguna berdasarkan email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../dashboard");
            exit();
        } else {
            $_SESSION['error_message'] = "Password salah. Silakan coba lagi.";
        }
    } else {
        $_SESSION['error_message'] = "Email tidak ditemukan.";
    }

    // Redirect kembali ke login jika ada kesalahan
    header("Location: ../login");
    exit();
} else {
    $_SESSION['error_message'] = "Silakan isi email dan password.";
    header("Location: ../login");
    exit();
}
?>