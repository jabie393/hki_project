<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'")->fetch_assoc();
$user_role = $_SESSION['role'] ?? 'user'; // Ambil role dari sesi, default 'user'

// Jika data kosong, tampilkan pesan default
$nama_lengkap = $profile['nama_lengkap'] ?? 'Belum diisi';
$no_ktp = $profile['no_ktp'] ?? 'Belum diisi';
$telephone = $profile['telephone'] ?? 'Belum diisi';
$birth_date = $profile['birth_date'] ?? 'Belum diisi';
$gender = $profile['gender'] ?? 'Belum diisi';
$nationality = $profile['nationality'] ?? 'Belum diisi';

// Periksa apakah user adalah admin
$isAdmin = ($user_role === 'admin');
$type_of_applicant = $isAdmin ? '' : ($profile['type_of_applicant'] ?? 'Belum diisi');

// Periksa apakah ada foto profil
$profile_picture = "uploads/users/$user_id/profile.jpg";
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 16px;
            margin: 5px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .fa-check-circle {
            color: #007bff;
            margin-left: 5px;
        }
    </style>
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
    <a href="dashboard.php">Dashboard</a>
</div>
<div>
    <a href="status_pengajuan.php">Lihat Status Pengajuan</a>
</div>
</body>
</html>

