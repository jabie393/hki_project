<!-- Flow FE -->
<!-- USER -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM registrations WHERE user_id = '$user_id'");

?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="css/user.css">
</head>

<div id="user-page">
    <div class="download-container">
        <a href="services/download_template.php?doc_type=surat_pernyataan" class="download-btn">
            <button>Surat Pernyataan</button>
        </a>
        <a href="services/download_template.php?doc_type=surat_pengalihan_hak" class="download-btn">
            <button>Surat Pengalihan Hak</button>
        </a>
    </div>

    <h2>Form Pendaftaran HKI</h2>
    <form action="services/submit_hki.php" method="POST" enctype="multipart/form-data">
        <!--Jenis Permohonan-->
        <label>Jenis Permohonan:</label><br>
        <select name="jenis_permohonan" required>
            <option value="Usaha Mikro Kecil">Usaha Mikro Kecil</option>
            <option value="Umum">Umum</option>
            <option value="Lembaga Pendidikan">Lembaga Pendidikan</option>
            <option value="Lembaga Litbang Pemerintah">Lembaga Litbang Pemerintah</option>
        </select><br><br>
        <!--Jenis Hak Cipta-->
        <label>Jenis Hak Cipta:</label><br>
        <select name="jenis_hak_cipta" id="jenis_hak_cipta" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="Karya Tulis">Karya Tulis</option>
            <option value="Karya Seni">Karya Seni</option>
            <option value="Komposisi Musik">Komposisi Musik</option>
            <option value="Karya Audio Visual">Karya Audio Visual</option>
            <option value="Karya Fotografi">Karya Fotografi</option>
            <option value="Karya Drama & Koreografi">Karya Drama & Koreografi</option>
            <option value="Karya Rekaman">Karya Rekaman</option>
            <option value="Karya Lainnya">Karya Lainnya</option>
        </select><br><br>
        <!--Sub Jenis Hak Cipta-->
        <label>Sub Jenis Hak Cipta:</label><br>
        <select name="sub_jenis_hak_cipta" id="sub_jenis_hak_cipta" required>
            <option value="">-- Pilih Sub Jenis --</option>
        </select><br><br>
        <!--Tanggal Pertama Kali Diumumkan-->
        <label>Tanggal Pertama Kali Diumumkan:</label><br>
        <input type="date" name="tanggal_pengumuman" required><br><br>

        <!--Judul Hak Cipta-->
        <label>Judul</label><br>
        <input type="text" name="judul" placeholder="Judul Hak Cipta" required /><br><br>
        <!--Deskripsi-->
        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" placeholder="Deskripsi" required></textarea><br><br>
        <!--Negara Pertama Kali Diumumkan-->
        <label>Negara Pertama Kali Diumumkan:</label><br>
        <select name="negara_pengumuman" id="nationality" required>
            <option value="">-- Pilih Negara --</option>
        </select><br><br>
        <!--Kota Pertama Kali Diumumkan-->
        <label>Kota/Kabupaten Pertama Kali Diumumkan:</label><br>
        <input type="text" name="kota_pengumuman" placeholder="Nama Kota/Kabupaten" required><br><br>
        <input type="file" name="dokumen" required /><br><br>
        <label>Pencipta:</label>
        <div id="pencipta-list">
            <div class="pencipta">
                <h4 class="pencipta-label">Pencipta 1</h4><br>

                <label>NIK:</label><br>
                <input type="text" name="nik[]" required><br><br>

                <label>Nama:</label><br>
                <input type="text" name="nama[]" required><br><br>

                <label>No. Telepon:</label><br>
                <input type="text" name="no_telepon[]" required><br><br>

                <label>Jenis Kelamin:</label><br>
                <select name="jenis_kelamin[]" required>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select><br><br>

                <label>Alamat:</label><br>
                <textarea name="alamat[]" required></textarea><br><br>

                <label>Negara:</label><br>
                <select name="negara[]" class="negara-select" required>
                    <option value="">-- Pilih Negara --</option>
                </select><br><br>

                <label>Provinsi:</label><br>
                <input type="text" name="provinsi[]" required><br><br>

                <label>Kota/Kabupaten:</label><br>
                <input type="text" name="kota[]" required><br><br>

                <label>Kecamatan:</label><br>
                <input type="text" name="kecamatan[]" required><br><br>

                <label>Kelurahan:</label><br>
                <input type="text" name="kelurahan[]" required><br><br>

                <label>Kode Pos:</label><br>
                <input type="text" name="kode_pos[]" required><br><br>
            </div>
        </div>

        <br><button type="button" id="addPencipta">Tambah Pencipta</button><br><br><br>

        <button type="submit">Kirim</button><br><br>
    </form>
</div>
<script src="js/user.js"></script>
<script src="js/ajax.js"></script>
