<?php
session_start();
include 'config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login");
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

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/reset_password.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">
    <link rel="stylesheet" href="css/select2.css">
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
                <label for="new_role" class="custom-label">Role:</label>
                <select id="new_role" name="new_role" class="custom-input" required>
                <option value="" disabled selected></option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="new_password" class="custom-label">Password Baru:</label>
                <input type="password" name="new_password" class="custom-input" placeholder="Opsional" autocomplete="new-password">
            </div>

            <button type="submit" class="button">Simpan Perubahan</button>
        </form>

        <!-- Tombol Detail User -->
        <div id="detailButtonWrapper" style="display: none; margin-top: 15px;">
            <button type="button" class="button" id="showUserDetailBtn">Detail User</button>
        </div>
    </div>

    <script src="js/reset_password.js"></script>
</div>
<!-- Modal Detail User -->
<div id="modal-page">
    <div id="userDetailModal" class="modal modal-hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Pengguna</h2>
                <button class="close" onclick="closeProfileModal()">&times;</button>
            </div>
            <div id="userDetailContent">
                <!-- Konten AJAX -->
            </div>
        </div>
    </div>
</div>