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
</head>

<body>
    <h2>Kelola Pengumuman</h2>

    <form action="services/announcement_update.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <button type="submit">Upload</button>
    </form>

    <h3>Daftar Pengumuman</h3>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <img src="<?= $row['image_path'] ?>" width="100">
                <a href="services/announcement_update.php?delete=<?= $row['id'] ?>"
                    onclick="return confirm('Hapus gambar ini?')">Hapus</a>
            </li>
        <?php endwhile; ?>
    </ul>

    <div>
        <a href="profile.php">Profil</a> |
        <a href="rekap_hki.php">Lihat Rekap HKI</a> |
        <a href="admin.php">Dashboard</a> |
        <a href="template.php">Template Dokumen</a> |
        <a href="reset_password.php">Reset Password User |</a>
        <a href="services/logout.php">Logout</a>
    </div>
</body>

</html>