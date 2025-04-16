<!-- Flow FE -->
<!-- ADMIN & USER -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'")->fetch_assoc();
$user_role = $_SESSION['role'] ?? 'user'; // Ambil role dari sesi, default 'user'

// Jika data kosong, tampilkan pesan default
$nama_lengkap = $profile['nama_lengkap'] ?? 'Nama?🤔';
$no_ktp = $profile['no_ktp'] ?? 'Belum diisi';
$telephone = $profile['telephone'] ?? 'Belum diisi';
$birth_date = $profile['birth_date'] ?? 'Belum diisi';
$gender = $profile['gender'] ?? 'Belum diisi';
$nationality = $profile['nationality'] ?? 'Belum diisi';

// Periksa apakah user adalah admin
$isAdmin = ($user_role === 'admin');
$type_of_applicant = $isAdmin ? '' : ($profile['type_of_applicant'] ?? 'Belum diisi');

// Periksa apakah ada foto profil
$profile_picture = "uploads/users/$user_id/profile/profile.jpg";
if (!file_exists($profile_picture) || empty($profile['profile_picture'])) {
    $profile_picture = "assets/image/default-avatar.png"; // Foto default jika belum diunggah
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS-->
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>

    <div class="container">
        <img src="<?= $profile_picture ?>" class="profile-img" alt="Foto Profil">
        <h2><?= $nama_lengkap ?> <?= $isAdmin ? '<i class="fas fa-check-circle"></i>' : '' ?></h2>

        <p><strong>No. KTP:</strong> <?= $no_ktp ?></p>
        <p><strong>Telepon:</strong> <?= $telephone ?></p>
        <p><strong>Tanggal Lahir:</strong> <?= $birth_date ?></p>
        <p><strong>Jenis Kelamin:</strong> <?= $gender ?></p>
        <p><strong>Kewarganegaraan:</strong> <?= $nationality ?></p>

        <!-- Tipe Pemohon hanya ditampilkan jika bukan admin -->
        <?php if (!$isAdmin): ?>
            <p><strong>Tipe Pemohon:</strong> <?= $type_of_applicant ?></p>
        <?php endif; ?>

        <a href="edit_profile.php" class="btn">Edit Profil</a>
    </div><br>
    <div>
        <?php if ($isAdmin): ?>
            <a href="admin.php">Dashboard</a> |
            <a href="rekap_hki.php">Rekap HKI</a> |
            <a href="announcement.php">Pengumuman</a> |
            <a href="template.php">Template Dokumen</a> |
            <a href="reset_password.php">Reset Password User |</a>
            <a href="services/logout.php">Logout</a>
        <?php else: ?>
            <a href="dashboard.php">Dashboard</a> |
            <a href="status_pengajuan.php">Lihat Status Pengajuan</a> |
            <a href="update_account.php">Update Data Akun</a> |
            <a href="services/logout.php">Logout</a>
        <?php endif; ?>
    </div>

</body>

</html>