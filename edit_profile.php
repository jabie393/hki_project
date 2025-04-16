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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Profile</title>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/edit_profile.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>

<body>

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
            <label for="profilePictureInput">Unggah Foto Profil:</label>
            <input type="file" id="profilePictureInput" accept="image/*">
        </div>

        <div>
            <label>Preview & Crop Foto:</label>
            <div style="width: 100%; height: 300px; overflow: hidden;">
                <img id="previewImage" style="max-width: 100%; max-height: 300px;">
            </div>
        </div>

        <input type="hidden" name="cropped_image" id="croppedImageInput">
        <button type="button" id="cropButton">Crop & Simpan Foto</button>

        <div>
            <label>Foto Saat Ini:</label><br>
            <img src="<?= $profile_picture ?>" width="100" height="100" alt="Foto Profil">
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

        <button type="submit">Simpan Profil</button>
    </form>

    <div style="text-align: center;">
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="status_pengajuan.php">📄 Status Pengajuan</a>
        <a href="profile.php">👤 Profil</a>
    </div>

    <script>
        // Load negara dari API
        fetch('https://restcountries.com/v3.1/all')
            .then(response => response.json())
            .then(data => {
                const select = document.querySelector("select[name='nationality']");
                data.sort((a, b) => a.name.common.localeCompare(b.name.common));
                data.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.name.common;
                    option.textContent = country.name.common;
                    select.appendChild(option);
                });

                // Set nilai default dari database jika ada
                const currentValue = "<?= $profile['nationality'] ?? '' ?>";
                if (currentValue) select.value = currentValue;
            })
            .catch(error => console.error("Gagal memuat data negara:", error));
    </script>

    <script>
        // Cropper.js
        document.addEventListener("DOMContentLoaded", function () {
            let cropper;
            const input = document.getElementById('profilePictureInput');
            const previewImage = document.getElementById('previewImage');
            const croppedImageInput = document.getElementById('croppedImageInput');
            const cropButton = document.getElementById('cropButton');
            const form = document.getElementById('profileForm');

            input.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImage.src = e.target.result;
                        if (cropper) cropper.destroy();
                        cropper = new Cropper(previewImage, {
                            aspectRatio: 1,
                            viewMode: 1,
                            autoCropArea: 1
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            cropButton.addEventListener('click', function () {
                if (!cropper) {
                    alert("Silakan pilih gambar terlebih dahulu!");
                    return;
                }

                const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
                if (canvas) {
                    croppedImageInput.value = canvas.toDataURL('image/jpeg');
                    form.submit();
                } else {
                    alert("Gagal melakukan crop. Silakan coba lagi!");
                }
            });
        });
    </script>

</body>

</html>