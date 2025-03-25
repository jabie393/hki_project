<?php
session_start();
include 'config/config.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user saat ini
$query = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$query->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['new_username'];
    $new_email = $_POST['new_email'];
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;
    $old_password = $_POST['old_password'];

    // Periksa apakah password lama benar
    if (!password_verify($old_password, $user['password'])) {
        echo "<script>alert('Password lama salah!'); window.location.href='update_account.php';</script>";
        exit();
    }

    // Cek apakah email baru sudah digunakan oleh user lain
    $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $email_check->bind_param("si", $new_email, $user_id);
    $email_check->execute();
    $email_check->store_result();
    if ($email_check->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan oleh user lain!'); window.location.href='update_account.php';</script>";
        exit();
    }
    $email_check->close();

    // Cek apakah username baru sudah digunakan oleh user lain
    $username_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $username_check->bind_param("si", $new_username, $user_id);
    $username_check->execute();
    $username_check->store_result();
    if ($username_check->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan oleh user lain!'); window.location.href='update_account.php';</script>";
        exit();
    }
    $username_check->close();

    // Update username dan email
    $query = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $query->bind_param("ssi", $new_username, $new_email, $user_id);
    $query->execute();

    // Update password jika diisi
    if ($new_password) {
        $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $query->bind_param("si", $new_password, $user_id);
        $query->execute();
    }

    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='update_account.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profil</title>
</head>
<body>
    <h2>Update Profil</h2>
    <form method="post">
        <label for="new_username">Nama:</label>
        <input type="text" name="new_username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <br><br>

        <label for="new_email">Email:</label>
        <input type="email" name="new_email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <br><br>

        <label for="new_password">Password Baru (Opsional):</label>
        <input type="password" name="new_password" autocomplete="new-password">

        <br><br>

        <label for="old_password">Masukkan Password Lama Untuk Menyimpan Perubahan:</label>
        <input type="password" name="old_password" required autocomplete="off">

        <br><br>

        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>