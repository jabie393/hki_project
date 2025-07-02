<!-- Flow FE -->
<!-- ADMIN & USER -->
<?php
include 'config/config.php';
include_once 'helpers/profile_helper.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'")->fetch_assoc();
$consultation_number = $conn->query("SELECT admin_number, consult_phone_code FROM consultation_number LIMIT 1")->fetch_assoc();
$user_role = $_SESSION['role'] ?? 'user';
$isAdmin = ($user_role === 'admin');

// helper
$profile_picture = getProfilePicture($user_id);
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Cropper -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="css/edit_profile.css" />
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">
    <link rel="stylesheet" href="css/select2.css">
</head>

<div id="edit_profile-page">
    <form id="profileForm" method="post">
        <h2>Edit Profil</h2>

        <div>
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" spellcheck="false" name="nama_lengkap" id="nama_lengkap"
                value="<?= $profile['nama_lengkap'] ?? '' ?>">
        </div>

        <div>
            <label for="no_ktp">Nomor KTP:</label>
            <input type="number" spellcheck="false" name="no_ktp" id="no_ktp" value="<?= $profile['no_ktp'] ?? '' ?>">
        </div>

        <div>
            <label for="telephone">No. Telepon:</label>
            <div class="phone-input-wrapper">
                <select id="phone_code" name="phone_code"
                    data-selected="<?= $profile['phone_code'] ?? '+62' ?>"></select>
                <input type="tel" spellcheck="false" name="telephone" id="telephone"
                    value="<?= $profile['telephone'] ?? '' ?>">
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <div>
                <label for="consultation_number">No Konsultasi:</label>
                <div class="phone-input-wrapper">
                    <select id="consult_phone_code" name="consult_phone_code"
                        data-selected="<?= $consultation_number['consult_phone_code'] ?? '+62' ?>"></select>
                    <input type="tel" spellcheck="false" name="consultation_number" id="consultation_number"
                        value="<?= $consultation_number['admin_number'] ?? '' ?>">
                </div>
            </div>
        <?php endif; ?>

        <div>
            <label for="birth_date">Tanggal Lahir:</label>
            <input type="date" name="birth_date" id="birth_date" value="<?= $profile['birth_date'] ?? '' ?>">
        </div>

        <div>
            <label for="gender">Jenis Kelamin:</label>
            <select name="gender" id="gender">
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki" <?= isset($profile['gender']) && $profile['gender'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= isset($profile['gender']) && $profile['gender'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="nationality">Kewarganegaraan:</label>
            <select name="nationality" id="nationality_profile" data-selected="<?= $profile['nationality'] ?? '' ?>">
                <option value="">-- Pilih Negara --</option>
            </select>
        </div>

        <?php if (!$isAdmin): ?>
            <div>
                <label for="type_of_applicant">Tipe Pengaju:</label>
                <select name="type_of_applicant" id="type_of_applicant" class="auto-search">
                    <option value="">-- Pilih Tipe Pengaju --</option>
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
                <small style="color: grey; font-size: 0.8rem;"><em><span style="color: red; font-size: 0.8em;">*
                        </span>Ukuran maksimal foto 5MB</em></small>
            </label>
            <input type="file" id="profilePictureInput" accept="image/*">
        </div>

        <div>
            <label>Preview Foto yang Dipilih:</label>
            <img id="previewCroppedCircle" src="#" style="display: none;" />
        </div>

        <input type="hidden" name="cropped_image" id="croppedImageInput">
        <button class="btn" type="submit">Simpan Profil</button>

        <div>
            <label>Foto Saat Ini:</label>
            <img id="profilePagePic" class="profilePic" src="<?= $profile_picture ?>" width="100" height="100"
                alt="Foto Profil" onerror="this.src='assets/image/default-avatar.png'">
        </div>
    </form>

    <!-- Modal Cropper -->
    <div id="modal-page">
        <div id="cropperModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Crop Foto Profil</h2>
                    <button class="close" id="closeCropperModal">&times;</button>
                </div>
                <div class="modal-cropper-content"><img id="modalPreviewImage" /></div>
                <div class="modal-footer">
                    <button class="btn" id="confirmCropButton">Gunakan Foto Ini</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/edit_profile.js"></script>