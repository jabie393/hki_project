<?php
include '../config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email atau username sudah terdaftar
    $check_query = "SELECT username, email FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['email'] == $email) {
            $_SESSION['error_email'] = "Email sudah terdaftar. Gunakan email lain.";
            $_SESSION['input_email'] = $email;
        }
        if ($row['username'] == $username) {
            $_SESSION['error_username'] = "Username sudah terdaftar. Gunakan username lain.";
            $_SESSION['input_username'] = $username;
        }
    }

    if (isset($_SESSION['error_email']) || isset($_SESSION['error_username'])) {
        header("Location: ../register");
        exit();
    }

    // Insert user ke database
    $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $user_folder = "../uploads/users/" . $user_id;
        if (!file_exists($user_folder)) {
            mkdir($user_folder, 0777, true);
            mkdir($user_folder . "/profile", 0777, true);
            mkdir($user_folder . "/files", 0777, true);
        }
        $_SESSION['register_success'] = true;
        header("Location: ../register");
        exit();
    } else {
        $_SESSION['error_general'] = "Gagal mendaftar. Coba lagi.";
        header("Location: ../register");
        exit();
    }
}
?>