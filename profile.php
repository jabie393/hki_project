<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Form Profile</h2>
    <form action="submit_profile.php" method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required value="<?= $profile['nama_lengkap'] ?? '' ?>"> <br>
        <input type="text" name="no_ktp" placeholder="No KTP" required value="<?= $profile['no_ktp'] ?? '' ?>"> <br>
        <input type="text" name="telephone" placeholder="No Telepon" required value="<?= $profile['telephone'] ?? '' ?>"> <br>
        <input type="date" name="birth_date" required value="<?= $profile['birth_date'] ?? '' ?>"> <br>
        
        <label>Jenis Kelamin:</label>
        <select name="gender" required>
            <option value="Laki-laki" <?= isset($profile['gender']) && $profile['gender'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="Perempuan" <?= isset($profile['gender']) && $profile['gender'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
        </select> <br>

        <label>Kewarganegaraan:</label>
        <select name="nationality" id="nationality" required>
        <option value="">-- Pilih Negara --</option>
        </select> <br>

        <label>Tipe Pemohon:</label>
        <select name="type_of_applicant" required>
            <option value="">-- Tipe Pemohon --</option>
            <option value="Kementerian dan Lembaga" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Kementerian dan Lembaga' ? 'selected' : '' ?>>Kementerian dan Lembaga</option>
            <option value="Pemerintahan Daerah" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Pemerintahan Daerah' ? 'selected' : '' ?>>Pemerintahan Daerah</option>
            <option value="Lembaga Pendidikan" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Lembaga Pendidikan' ? 'selected' : '' ?>>Lembaga Pendidikan</option>
            <option value="Lembaga Penelitian dan Pengembangan" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Lembaga Penelitian dan Pengembangan' ? 'selected' : '' ?>>Lembaga Penelitian dan Pengembangan</option>
            <option value="Kantor Wilayah Kementerian Hukum dan HAM" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Kantor Wilayah Kementerian Hukum dan HAM' ? 'selected' : '' ?>>Kantor Wilayah Kementerian Hukum dan HAM</option>
            <option value="Sentra Hak Kekayaan Intelektual" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Sentra Hak Kekayaan Intelektual' ? 'selected' : '' ?>>Sentra Hak Kekayaan Intelektual</option>
            <option value="Konsultan Hak Kekayaan Intelektual" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Konsultan Hak Kekayaan Intelektual' ? 'selected' : '' ?>>Konsultan Hak Kekayaan Intelektual</option>
            <option value="Usaha Mikro, Kecil dan Menengah" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Usaha Mikro, Kecil dan Menengah' ? 'selected' : '' ?>>Usaha Mikro, Kecil dan Menengah</option>
            <option value="Institusi lain" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Institusi lain' ? 'selected' : '' ?>>Institusi lain</option>
            <option value="Badan Hukum" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Badan Hukum' ? 'selected' : '' ?>>Badan Hukum</option>
            <option value="Perorangan" <?= isset($profile['type_of_applicant']) && $profile['type_of_applicant'] == 'Perorangan' ? 'selected' : '' ?>>Perorangan</option>
        </select>

        <button type="submit">Simpan</button>
    </form>

    <br>
    <button onclick="kembali()">Kembali</button>

<script>
    function kembali() {
        window.history.back();
    }
</script>

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

</body>
</html>
