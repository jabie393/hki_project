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

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengumuman</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/announcement.css">
</head>

<body>
    <main>
        <h2>Kelola Pengumuman</h2>

        <form action="services/announcement_update.php" method="POST" enctype="multipart/form-data">

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
                        <a class="delete-btn" href="services/announcement_update.php?delete=<?= $row['id'] ?>"
                            onclick="return confirm('Hapus gambar ini?')">Hapus</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>


        <div class="nav">
            <a href="profile.php">Profil</a> |
            <a href="rekap_hki.php">Rekap HKI</a> |
            <a href="admin.php">Dashboard</a> |
            <a href="template.php">Template Dokumen</a> |
            <a href="reset_password.php">Reset Password User</a> |
            <a href="services/logout.php">Logout</a>
        </div>
        <script src="js/announcement.js"></script>
    </main>
</body>

</html>