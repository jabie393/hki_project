<!-- Flow FE -->
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

    // Ambil data user (misal: tabel 'users' punya kolom 'role' atau 'level')
    $user_data = $conn->query("SELECT * FROM users WHERE id = '$user_id'")->fetch_assoc();
    $isAdmin = (isset($user_data['role']) && $user_data['role'] === 'admin'); // Sesuaikan fieldnya

    // Ambil data profil
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

    <head>
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        <!-- Font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="css/modal.css">
        <style>
            .verified-icon {
                color: #007bff;
                /* Warna biru seperti centang verified */
                margin-left: 8px;
            }
        </style>
    </head>
    <div id="modal-page">
        <div class="modal-container">
            <div class="profile-center">
                <img src="<?= $profile_picture ?>" class="profile-img" alt="Foto Profil">
            </div>

            <h2 class="text-center">
                <?= $nama_lengkap ?>
                <?php if ($isAdmin): ?>
                    <i class="fas fa-check-circle verified-icon" title="Admin"></i>
                <?php endif; ?>
            </h2>

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
            <p class="profile-row">
                <span class="profile-label">Tipe Pemohon:</span>
                <span class="profile-value"><?= $type_of_applicant ?></span>
            </p>
        </div>
        <?php
} else {
    echo "<p>Data profil tidak ditemukan.</p>";
}
?>
</div>