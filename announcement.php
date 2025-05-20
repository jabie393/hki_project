<!-- Flow FE -->
<!-- ADMIN -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login");
    exit();
}

$result = $conn->query("SELECT * FROM announcement");
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/announcement.css">
    <link rel="stylesheet" href="css/alert.css">
</head>

<div id="announcement-page">
    <main>
        <h2>Kelola Pengumuman</h2>

        <form id="announcementForm" enctype="multipart/form-data">
            <div class="custom-file-upload">
                <input type="file" name="image" id="fileInput" accept="image/*" required>
                <label for="fileInput" class="upload-box">
                    <span class="upload-icon">📃</span>
                    <span id="upload-text">Klik untuk memilih file pengumuman</span>
                </label>
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
    </main>

    <script src="js/announcement.js"></script>
</div>