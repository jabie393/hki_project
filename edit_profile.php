<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'")->fetch_assoc();
$user_role = $_SESSION['role'] ?? 'user'; // Ambil role dari session, default user

$isAdmin = ($user_role === 'admin'); // Periksa apakah user adalah admin
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Profile</title>
    <link rel="stylesheet" href="styles.css">

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>
<body>
    <h2>Form Profile</h2>
    <form id="profileForm" action="services/submit_profile.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="<?= $profile['nama_lengkap'] ?? '' ?>"> <br>
        <input type="text" name="no_ktp" placeholder="No KTP" value="<?= $profile['no_ktp'] ?? '' ?>"> <br>
        <input type="text" name="telephone" placeholder="No Telepon" value="<?= $profile['telephone'] ?? '' ?>"> <br>
        <input type="date" name="birth_date" value="<?= $profile['birth_date'] ?? '' ?>"> <br>

        <label>Foto Profil:</label>
        <input type="file" id="profilePictureInput" accept="image/*"> <br>

        <!-- Tempat Preview dan Crop Gambar -->
        <div style="width: 300px; height: 300px; overflow: hidden;">
            <img id="previewImage" style="max-width: 100%;">
        </div>

        <!-- Input Hidden untuk Menyimpan Gambar yang Sudah Dicrop -->
        <input type="hidden" name="cropped_image" id="croppedImageInput">

        <button type="button" id="cropButton">Crop & Simpan</button>

        <?php
        $profile_picture = "uploads/users/$user_id/profile/profile.jpg";
        if (!file_exists($profile_picture)) {
            $profile_picture = "assets/image/default-avatar.png"; // Default jika belum ada foto
        }
        ?>
        <img src="<?= $profile_picture ?>" width="100" height="100"><br>

        <label>Jenis Kelamin:</label>
        <select name="gender">
            <option value="Laki-laki" <?= isset($profile['gender']) && $profile['gender'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="Perempuan" <?= isset($profile['gender']) && $profile['gender'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
        </select> <br>

        <label>Kewarganegaraan:</label>
        <select name="nationality" id="nationality">
            <option value="">-- Pilih Negara --</option>
        </select> <br>

        <!-- Sembunyikan "Tipe Pemohon" jika user adalah admin -->
        <?php if ($_SESSION['role'] !== 'admin') : ?>
    <label>Tipe Pemohon:</label>
    <select name="type_of_applicant">
        <option value="">-- Tipe Pemohon --</option>
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
    <?php else : ?>
        <input type="hidden" name="type_of_applicant" value="Admin">
    <?php endif; ?>


        <button type="submit">Simpan</button>
    </form>

    <br>
    <div>
        <a href="dashboard.php">Dashboard</a>
    </div>
    <div>
        <a href="status_pengajuan.php">Lihat Status Pengajuan</a>
    </div>
    <div>
        <a href="profile.php">Profil</a>
    </div>

<script>
fetch('https://restcountries.com/v3.1/all')
    .then(response => response.json())
    .then(data => {
        let select = document.querySelector("select[name='nationality']");
        data.sort((a, b) => a.name.common.localeCompare(b.name.common)); // Urutkan abjad
        data.forEach(country => {
            let option = document.createElement("option");
            option.value = country.name.common;
            option.textContent = country.name.common;
            select.appendChild(option);
        });
    })
    .catch(error => console.error("Gagal memuat data negara:", error));
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let cropper;
    const input = document.getElementById('profilePictureInput');
    const previewImage = document.getElementById('previewImage');
    const croppedImageInput = document.getElementById('croppedImageInput');
    const cropButton = document.getElementById('cropButton');
    const form = document.getElementById('profileForm');

    input.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                if (cropper) cropper.destroy(); // Hapus cropper lama jika ada
                cropper = new Cropper(previewImage, {
                    aspectRatio: 1, // Crop 1:1
                    viewMode: 1,
                    autoCropArea: 1
                });
            };
            reader.readAsDataURL(file);
        }
    });

    cropButton.addEventListener('click', function() {
        if (!cropper) {
            alert("Silakan pilih gambar terlebih dahulu!");
            return;
        }

        const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
        if (canvas) {
            croppedImageInput.value = canvas.toDataURL('image/jpeg'); // Simpan base64 ke input hidden
            form.submit(); // Kirim form
        } else {
            alert("Gagal melakukan crop. Silakan coba lagi!");
        }
    });
});
</script>

</body>
</html>