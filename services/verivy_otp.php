<?php
include '../config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);

    // Ambil user berdasarkan email
    $query = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "Email tidak ditemukan!";
        exit();
    }

    $user_id = $user['id'];

    // Cek OTP
    $otp_query = $conn->prepare("SELECT * FROM user_otps WHERE user_id = ? AND otp_code = ? AND created_at >= NOW() - INTERVAL 10 MINUTE");
    $otp_query->bind_param("is", $user_id, $otp);
    $otp_query->execute();
    $otp_result = $otp_query->get_result();

    if ($otp_result->num_rows > 0) {
        // Verifikasi berhasil, update status user
        $conn->query("UPDATE users SET is_verified = 1 WHERE id = $user_id");

        // Hapus OTP setelah digunakan
        $conn->query("DELETE FROM user_otps WHERE user_id = $user_id");

        echo "Verifikasi berhasil! Silakan login.";
    } else {
        echo "Kode OTP salah atau sudah kadaluarsa.";
    }
}
?>
