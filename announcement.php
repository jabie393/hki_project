<!-- Flow FE -->
<!-- ADMIN -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM announcement");
?>

<head>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/announcement.css">
</head>

<div id="announcement-page">
    <main>
        <h2>Kelola Pengumuman</h2>

        <form id="announcementForm" enctype="multipart/form-data">
            <div class="custom-file-upload">
                <input type="file" name="image" id="fileInput" required>
                <div class="upload-box">
                    <span class="upload-icon">📃</span>
                    <span id="upload-text">Klik untuk memilih file pengumuman</span>
                </div>
            </div>
            <span id="file-name">Belum ada file dipilih</span>
            <button type="submit">Upload</button>
        </form>

        <h3>Daftar Pengumuman</h3>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <img src="<?= $row['image_path'] ?>" alt="Pengumuman">
                    <div class="action">
                        <a class="delete-btn" href="#" data-id="<?= $row['id'] ?>">Hapus</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="nav">
            <a href="#" onclick="loadContent('profile.php')">Profil</a> |
            <a href="#" onclick="loadContent('rekap_hki.php')">Rekap HKI</a> |
            <a href="#" onclick="loadContent('admin.php')">Dashboard</a> |c
            <a href="#" onclick="loadContent('template.php')">Template Dokumen</a> |
            <a href="#" onclick="loadContent('reset_password.php')">Reset Password User</a> |
            <a href="services/logout.php">Logout</a>
        </div>
    </main>

    <script src="js/announcement.js"></script>
</div>
