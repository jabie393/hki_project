<?php
session_start();
include 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'")->fetch_assoc();

$profile_picture = "uploads/users/$user_id/profile.jpg";
if (!file_exists($profile_picture)) {
    $profile_picture = "assets/image/default-avatar.png"; // Default jika belum ada foto
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 3px solid #007bff;
        }
        .profile-info {
            text-align: left;
        }
    </style>
</head>
<body class="bg-light">

<div class="container">
    <div class="profile-container">
        <img src="<?= $profile_picture ?>" alt="Foto Profil" class="profile-picture">
        <h2><?= htmlspecialchars($profile['nama_lengkap']) ?></h2>
        <p><strong>No. KTP:</strong> <?= htmlspecialchars($profile['no_ktp']) ?></p>
        <p><strong>Telepon:</strong> <?= htmlspecialchars($profile['telephone']) ?></p>
        <p><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($profile['birth_date']) ?></p>
        <p><strong>Jenis Kelamin:</strong> <?= htmlspecialchars($profile['gender']) ?></p>
        <p><strong>Kewarganegaraan:</strong> <?= htmlspecialchars($profile['nationality']) ?></p>
        <p><strong>Tipe Pemohon:</strong> <?= htmlspecialchars($profile['type_of_applicant']) ?></p>
        <a href="edit_profile.php" class="btn btn-primary">Edit Profil</a>
    </div>
</div>

<div>
    <a href="dashboard.php">Dashboard</a>
</div>
<div>
    <a href="status_pengajuan.php">Lihat Status Pengajuan</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
