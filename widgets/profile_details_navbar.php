<?php
include '../config/config.php';
include_once '../helpers/profile_helper.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login");
    exit();
}

$role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'];

// Ambil data dari tabel 'users'
$user_query = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows === 0) {
    echo "<p>Pengguna tidak ditemukan.</p>";
    exit();
}

$user_data = $user_result->fetch_assoc();

// Ambil data dari tabel 'user_profile'
$profile_query = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$profile_query->bind_param("i", $user_id);
$profile_query->execute();
$profile_result = $profile_query->get_result();
$profile = $profile_result->fetch_assoc();

// Isi nilai default jika data tidak ada
$nama_lengkap = $profile['nama_lengkap'] ?? 'Nama?🤔';
$no_ktp = $profile['no_ktp'] ?? 'Belum diisi';
$phone_code = $profile['phone_code'] ?? '';
$telephone = $profile['telephone'] ?? 'Belum diisi';
$birth_date = $profile['birth_date'] ?? 'Belum diisi';
$gender = $profile['gender'] ?? 'Belum diisi';
$nationality = $profile['nationality'] ?? 'Belum diisi';
$type_of_applicant = $profile['type_of_applicant'] ?? 'Belum diisi';

// helper
$profile_picture = getProfilePicture($user_id);
?>

<div id="modal-page">
    <div>
        <div class="profile-center">
            <img src="<?= $profile_picture ?>" alt="Foto Profil" class="profile-img profilePic"
                onerror="this.src='assets/images/default-avatar.png'">
        </div>

        <p class="profile-row">
            <span class="profile-label">Username:</span>
            <span class="profile-value"><?= htmlspecialchars($user_data['username']) ?></span>
        </p>

        <p class="profile-row">
            <span class="profile-label">Email:</span>
            <span class="profile-value"><?= htmlspecialchars($user_data['email']) ?></span>
        </p>

        <hr>

        <p class="profile-row">
            <span class="profile-label">Nama Lengkap:</span>
            <span class="profile-value">
                <?= htmlspecialchars($nama_lengkap) ?>
                <?php if (isset($role) && $role === 'admin'): ?>
                    <i class="fas fa-check-circle" title="Admin"></i>
                <?php endif; ?>
            </span>
        </p>

        <p class="profile-row">
            <span class="profile-label">No KTP:</span>
            <span class="profile-value"><?= htmlspecialchars($no_ktp) ?></span>
        </p>

        <p class="profile-row">
            <span class="profile-label">Telepon:</span>
            <span class="profile-value"><?= htmlspecialchars($phone_code . ' ' . $telephone) ?></span>
        </p>

        <p class="profile-row">
            <span class="profile-label">Tanggal Lahir:</span>
            <span class="profile-value"><?= htmlspecialchars($birth_date) ?></span>
        </p>

        <p class="profile-row">
            <span class="profile-label">Jenis Kelamin:</span>
            <span class="profile-value"><?= htmlspecialchars($gender) ?></span>
        </p>

        <p class="profile-row">
            <span class="profile-label">Kewarganegaraan:</span>
            <span class="profile-value"><?= htmlspecialchars($nationality) ?></span>
        </p>

        <p class="profile-row">
            <span class="profile-label">Tipe Pengaju:</span>
            <span class="profile-value"><?= htmlspecialchars($type_of_applicant) ?></span>
        </p>
    </div>
</div>