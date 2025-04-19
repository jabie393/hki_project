<!-- Flow FE -->
<!-- ADMIN -->
<?php
session_start();
include 'config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<head>
    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/reset_password.css">
</head>

<div id="reset_password-page">
    <div class="container">
        <h2>Kelola User</h2>
        <form id="resetUserForm" method="post">
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
            <a href="#" onclick="loadContent('profile.php')">Profil</a> |
            <a href="#" onclick="loadContent('admin.php')">Dashboard</a> |
            <a href="#" onclick="loadContent('rekap_hki.php')">Rekap HKI</a> |
            <a href="#" onclick="loadContent('announcement.php')">Pengumuman</a> |
            <a href="#" onclick="loadContent('template.php')">Template Dokumen</a> |
            <a href="services/logout.php">Logout</a>
        </div>
    </div>

    <script src="js/reset_password.js"></script>
</div>