<!-- Flow FE -->
<!-- ADMIN -->
<?php
session_start();
include 'config/config.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['new_username'];
    $new_email = $_POST['new_email'];
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;

    $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $email_check->bind_param("si", $new_email, $user_id);
    $email_check->execute();
    $email_check->store_result();
    if ($email_check->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan oleh user lain!'); window.location.href='reset_password.php';</script>";
        exit();
    }
    $email_check->close();

    $username_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $username_check->bind_param("si", $new_username, $user_id);
    $username_check->execute();
    $username_check->store_result();
    if ($username_check->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan oleh user lain!'); window.location.href='reset_password.php';</script>";
        exit();
    }
    $username_check->close();

    $query = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $query->bind_param("ssi", $new_username, $new_email, $user_id);
    $query->execute();

    if ($new_password) {
        $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $query->bind_param("si", $new_password, $user_id);
        $query->execute();
    }

    echo "<script>alert('Data user berhasil diperbarui!'); window.location.href='reset_password.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/reset_password.css">
</head>

<body>
    <div class="container">
        <h2>Kelola User</h2>
        <form method="post">
            <div class="form-group">
                <label for="user_id" class="custom-label">Pilih User:</label>
                <select id="userSelect" name="user_id" style="width: 100%;" required>
                    <option value="">Cari User...</option>
                    <?php
                    $result = $conn->query("SELECT id, username FROM users");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['username']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="new_username" class="custom-label">Username:</label>
                <input type="text" id="new_username" name="new_username" class="custom-input" required
                    autocomplete="off">
            </div>

            <div class="form-group">
                <label for="new_email" class="custom-label">Email:</label>
                <input type="email" id="new_email" name="new_email" class="custom-input" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="new_password" class="custom-label">Password Baru (Opsional):</label>
                <input type="password" name="new_password" class="custom-input" autocomplete="new-password">
            </div>

            <button type="submit" class="button">Simpan Perubahan</button>
        </form>

        <div class="nav-links">
            <a href="profile.php">Profil</a> |
            <a href="admin.php">Dashboard</a> |
            <a href="rekap_hki.php">Rekap HKI</a> |
            <a href="announcement.php">Pengumuman</a> |
            <a href="template.php">Template Dokumen</a> |
            <a href="services/logout.php">Logout</a>
        </div>
    </div>

    <script src="js/reset_password.js"></script>
</body>

</html>