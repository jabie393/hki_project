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

<head>
    <!-- Css -->
    <link rel="stylesheet" href="css/profile.css">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<div id="profile-page">

    <div class="container">
        <img src="<?= $profile_picture ?>" class="profile-img" alt="Foto Profil">
        <h2><?= $nama_lengkap ?> <?= $isAdmin ? '<i class="fas fa-check-circle"></i>' : '' ?></h2>

        <p class="profile-row">
            <span class="profile-label">No. KTP:</span>
            <span class="profile-value"><?= $no_ktp ?></span>
        </p>
        <p class="profile-row">
            <span class="profile-label">Telepon:</span>
            <span class="profile-value"><?= $telephone ?></span>
        </p>
        <p class="profile-row">
            <span class="profile-label">Tanggal Lahir:</span>
            <span class="profile-value"><?= $birth_date ?></span>
        </p>
        <p class="profile-row">
            <span class="profile-label">Jenis Kelamin:</span>
            <span class="profile-value"><?= $gender ?></span>
        </p>
        <p class="profile-row">
            <span class="profile-label">Kewarganegaraan:</span>
            <span class="profile-value"><?= $nationality ?></span>
        </p>

        <?php if (!$isAdmin): ?>
            <p class="profile-row">
                <span class="profile-label">Tipe Pemohon:</span>
                <span class="profile-value"><?= $type_of_applicant ?></span>
            </p>
        <?php endif; ?>


        <button onclick="loadEditProfile()" class="btn">✏️ Edit Profile</button>
    </div>
</div>

<script>
    function loadEditProfile() {
    fetch('edit_profile.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById('content-main').innerHTML = html;

            // Eksekusi ulang semua script yang ada di edit_profile.php
            const container = document.getElementById('content-main');
            const scripts = container.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                    newScript.async = false;
                    document.body.appendChild(newScript);
                } else {
                    newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                }
                oldScript.remove();
            });
        })
        .catch(err => {
            console.error('Gagal memuat edit_profile.php:', err);
            alert('Gagal membuka Edit Profile.');
        });
}

</script>