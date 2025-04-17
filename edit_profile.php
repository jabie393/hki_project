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
$user_role = $_SESSION['role'] ?? 'user';
$isAdmin = ($user_role === 'admin');
$profile_picture = file_exists("uploads/users/$user_id/profile/profile.jpg")
    ? "uploads/users/$user_id/profile/profile.jpg"
    : "assets/image/default-avatar.png";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Form Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="css/edit_profile.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>

<body>
    <main>
        <form id="profileForm" action="services/submit_profile.php" method="POST" enctype="multipart/form-data">
            <h2>Formulir Profil</h2>

            <div>
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= $profile['nama_lengkap'] ?? '' ?>">
            </div>

            <div>
                <label for="no_ktp">Nomor KTP:</label>
                <input type="text" name="no_ktp" id="no_ktp" value="<?= $profile['no_ktp'] ?? '' ?>">
            </div>

            <div>
                <label for="telephone">No. Telepon:</label>
                <input type="text" name="telephone" id="telephone" value="<?= $profile['telephone'] ?? '' ?>">
            </div>

            <div>
                <label for="birth_date">Tanggal Lahir:</label>
                <input type="date" name="birth_date" id="birth_date" value="<?= $profile['birth_date'] ?? '' ?>">
            </div>

            <div>
                <label for="gender">Jenis Kelamin:</label>
                <select name="gender" id="gender">
                    <option value="Laki-laki" <?= isset($profile['gender']) && $profile['gender'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= isset($profile['gender']) && $profile['gender'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <div>
                <label for="nationality">Kewarganegaraan:</label>
                <select name="nationality" id="nationality">
                    <option value="">-- Pilih Negara --</option>
                </select>
            </div>

            <?php if (!$isAdmin): ?>
                <div>
                    <label for="type_of_applicant">Tipe Pemohon:</label>
                    <select name="type_of_applicant" id="type_of_applicant">
                        <option value="">-- Pilih Tipe --</option>
                        <?php
                        $applicant_types = [
                            "Kementerian dan Lembaga",
                            "Pemerintahan Daerah",
                            "Lembaga Pendidikan",
                            "Lembaga Penelitian dan Pengembangan",
                            "Kantor Wilayah Kementerian Hukum dan HAM",
                            "Sentra Hak Kekayaan Intelektual",
                            "Konsultan Hak Kekayaan Intelektual",
                            "Usaha Mikro, Kecil dan Menengah",
                            "Institusi lain",
                            "Badan Hukum",
                            "Perorangan"
                        ];
                        foreach ($applicant_types as $type) {
                            $selected = isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == $type ? 'selected' : '';
                            echo "<option value=\"$type\" $selected>$type</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="type_of_applicant" value="Admin">
            <?php endif; ?>

            <div class="profile-picture-input">
                <label for="profilePictureInput" class="upload-box">
                    <span class="upload-icon">🖼️</span>
                    <span id="upload-text">Klik untuk memilih foto profil</span>
                </label>
                <input type="file" id="profilePictureInput" accept="image/*">
            </div>

            <div>
                <label>Preview Foto yang Dipilih:</label><br>
                <img id="previewCroppedCircle" src="#" style="display: none;" />
            </div>

            <input type="hidden" name="cropped_image" id="croppedImageInput">
            <button type="submit">Simpan Profil</button>

            <div>
                <label>Foto Saat Ini:</label><br>
                <img src="<?= $profile_picture ?>" width="100" height="100" alt="Foto Profil">
            </div>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="status_pengajuan.php">📄 Status Pengajuan</a>
            <a href="profile.php">👤 Profil</a>
        </div>
    </main>

    <!-- Modal Cropper -->
    <div id="cropperModal" class="modal-cropper">
        <div class="modal-content-cropper">
            <span class="close-cropper" id="closeCropperModal">&times;</span>
            <h3>Crop Foto Profil</h3>
            <div><img id="modalPreviewImage" /></div>
            <br>
            <button id="confirmCropButton">Gunakan Foto Ini</button>
        </div>
    </div>
    <script src="js/edit_profile.js"></script>
</body>

</html>