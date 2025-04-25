<!-- Flow FE -->
<!-- USER -->
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
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/update_account.css">
</head>

<div id="update_account-page">
    <h2>Update Profil</h2>
    <form id="updateForm">
        <div class="form-group">
            <label for="new_username">Username:</label>
            <input type="text" name="new_username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="form-group">
            <label for="new_email">Email:</label>
            <input type="email" name="new_email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="new_password">Password Baru (Opsional):</label>
            <input type="password" name="new_password" autocomplete="new-password">
        </div>
        <div class="form-group">
            <label for="old_password">Masukkan Password Lama Untuk Menyimpan Perubahan:</label>
            <input type="password" name="old_password" required autocomplete="off">
        </div>
        <button type="submit">Simpan Perubahan</button>
    </form>
</div>

<script src="js/update_account.js"></script>