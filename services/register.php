<?php
include '../config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email atau nama sudah terdaftar
    $check_query = "SELECT name, email FROM users WHERE email = ? OR name = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $email, $name);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['email'] == $email) {
            $_SESSION['error_email'] = "Email sudah terdaftar. Gunakan email lain.";
        }
        if ($row['name'] == $name) {
            $_SESSION['error_name'] = "Nama sudah terdaftar. Gunakan nama lain.";
        }
    }

    if (isset($_SESSION['error_email']) || isset($_SESSION['error_name'])) {
        header("Location: ../register.php");
        exit();
    }

    $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        // Ambil ID user yang baru dibuat
        $user_id = $stmt->insert_id;

        // Buat direktori user berdasarkan user_id
        $user_folder = "../uploads/users/" . $user_id;
        if (!file_exists($user_folder)) {
            mkdir($user_folder, 0777, true);
            mkdir($user_folder . "/profile", 0777, true); // Untuk foto profil
            mkdir($user_folder . "/files", 0777, true);   // Untuk file lain
        }

        echo "<script>alert('Pembuatan akun berhasil! Silakan login.'); window.location.href='../login.php';</script>";
        exit();
    } else {
        $_SESSION['error_general'] = "Gagal mendaftar. Coba lagi.";
        header("Location: ../register.php");
        exit();
    }
}
