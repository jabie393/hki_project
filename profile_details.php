<!-- ADMIN -->
<?php
include 'config/config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $profile = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'")->fetch_assoc();

    // Jika data kosong, tampilkan pesan default
    $nama_lengkap = $profile['nama_lengkap'] ?? 'Belum diisi';
    $no_ktp = $profile['no_ktp'] ?? 'Belum diisi';
    $telephone = $profile['telephone'] ?? 'Belum diisi';
    $birth_date = $profile['birth_date'] ?? 'Belum diisi';
    $gender = $profile['gender'] ?? 'Belum diisi';
    $nationality = $profile['nationality'] ?? 'Belum diisi';
    $type_of_applicant = $profile['type_of_applicant'] ?? 'Belum diisi';

    // Periksa apakah ada foto profil
    $profile_picture = "uploads/users/$user_id/profile/profile.jpg";
    if (!file_exists($profile_picture) || empty($profile['profile_picture'])) {
        $profile_picture = "assets/image/default-avatar.png"; // Foto default jika belum diunggah
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

        h2 {
            color: #333;
        }
        p {
            font-size: 16px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
            <img src="<?= $profile_picture ?>" class="profile-img" alt="Foto Profil">
            <h2><?= $nama_lengkap ?></h2>
            
            <p><strong>No. KTP:</strong> <?= $no_ktp ?></p>
            <p><strong>Telepon:</strong> <?= $telephone ?></p>
            <p><strong>Tanggal Lahir:</strong> <?= $birth_date ?></p>
            <p><strong>Jenis Kelamin:</strong> <?= $gender ?></p>
            <p><strong>Kewarganegaraan:</strong> <?= $nationality ?></p>
            <p><strong>Tipe Pemohon:</strong> <?= $type_of_applicant ?></p>
        </div>
        
        <?php
    } else {
        echo "<p>Data profil tidak ditemukan.</p>";
    }
    ?>
</body>
</html>
