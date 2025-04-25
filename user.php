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

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/modal.css">
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
    <form id="form-hki" enctype="multipart/form-data">
        <!--Jenis Permohonan-->
        <div class="form-group">
            <label>Jenis Permohonan:</label>
            <select name="jenis_permohonan" required>
                <option value="Usaha Mikro Kecil">Usaha Mikro Kecil</option>
                <option value="Umum">Umum</option>
                <option value="Lembaga Pendidikan">Lembaga Pendidikan</option>
                <option value="Lembaga Litbang Pemerintah">Lembaga Litbang Pemerintah</option>
            </select>
        </div>
        <!--Jenis Hak Cipta-->
        <div class="form-group">
            <label>Jenis Hak Cipta:</label>
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
            </select>
        </div>
        <!--Sub Jenis Hak Cipta-->
        <div class="form-group">
            <label>Sub Jenis Hak Cipta:</label>
            <select name="sub_jenis_hak_cipta" id="sub_jenis_hak_cipta" required>
                <option value="">-- Pilih Sub Jenis --</option>
            </select>
        </div>
        <!--Tanggal Pertama Kali Diumumkan-->
        <div class="form-group">
            <label>Tanggal Pertama Kali Diumumkan:</label>
            <input type="date" name="tanggal_pengumuman" required>
        </div>
        <!--Judul Hak Cipta-->
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="judul" placeholder="Judul Hak Cipta" required />
        </div>
        <!--Deskripsi-->
        <div class="form-group">
            <label>Deskripsi:</label>
            <textarea name="deskripsi" placeholder="Deskripsi" required></textarea>
        </div>
        <!--Negara Pertama Kali Diumumkan-->
        <div class="form-group">
            <label>Negara Pertama Kali Diumumkan:</label>
            <select name="negara_pengumuman" id="nationality" required>
                <option value="">-- Pilih Negara --</option>
            </select>
        </div>
        <!--Kota Pertama Kali Diumumkan-->
        <div class="form-group">
            <label>Kota/Kabupaten Pertama Kali Diumumkan:</label>
            <input type="text" name="kota_pengumuman" placeholder="Nama Kota/Kabupaten" required>
        </div>
        <div class="form-group">
            <input type="file" name="dokumen" required />
        </div>
        <label>Pencipta:</label>
        <div id="pencipta-list" class="pencipta-container"></div>
        <button type="button" id="addPencipta">Tambah Pencipta</button>
        <!-- input tersembunyi -->
        <div id="pencipta-hidden-inputs"></div>
        <button type="submit">Kirim</button>
    </form>
</div>
<!-- Modal Tambah Pencipta -->
<div id="modal-page">
    <div id="modalPencipta" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Pencipta</h2>
                <button class="close close-modal">&times;</button>
            </div>
            <form id="modalFormPencipta">
                <?php include 'widgets/form_pencipta.php'; ?>
                <button class="btn" type="submit">Tambahkan</button>
            </form>
        </div>
    </div>
</div>


<script src="js/user.js"></script>
<script src="js/ajax.js"></script>