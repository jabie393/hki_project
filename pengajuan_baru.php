<!-- Flow FE -->
<!-- USER -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
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

    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/pengajuan_baru.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">
    <link rel="stylesheet" href="css/select2.css">
</head>

<div id="pengajuan_baru-page">
    <div class="download-container">
        <a href="services/download_template.php?doc_type=surat_pernyataan" class="download-btn">
            <button>Surat Pernyataan</button>
        </a>
        <a href="services/download_template.php?doc_type=surat_pengalihan_hak" class="download-btn">
            <button>Surat Pengalihan Hak</button>
        </a>
    </div>

    <h2>Form Pengajuan Hak Cipta</h2>
    <form id="form-hki" enctype="multipart/form-data">
        <!--Jenis Pengajuan-->
        <div class="form-group">
            <label>Jenis Pengajuan:</label>
            <select name="jenis_pengajuan" id="jenis_pengajuan" class="auto-search" required
                oninvalid="this.setCustomValidity('Silakan pilih jenis pengajuan terlebih dahulu.')"
                oninput="this.setCustomValidity('')">
                <option value="">-- Pilih Jenis Pengajuan --</option>
                <option value="Usaha Mikro Kecil">Usaha Mikro Kecil</option>
                <option value="Umum">Umum</option>
                <option value="Lembaga Pendidikan">Lembaga Pendidikan</option>
                <option value="Lembaga Litbang Pemerintah">Lembaga Litbang Pemerintah</option>
            </select>
        </div>
        <!--Jenis Hak Cipta-->
        <div class="form-group">
            <label>Jenis Hak Cipta:</label>
            <select name="jenis_hak_cipta" id="jenis_hak_cipta" class="auto-search" required
                oninvalid="this.setCustomValidity('Silakan pilih jenis hak cipta terlebih dahulu.')"
                oninput="this.setCustomValidity('')">
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
            <select name="sub_jenis_hak_cipta" id="sub_jenis_hak_cipta" class="auto-search" required
                oninvalid="this.setCustomValidity('Silakan pilih sub jenis hak cipta terlebih dahulu.')"
                oninput="this.setCustomValidity('')">
                <option value="">-- Pilih Sub Jenis --</option>
            </select>
        </div>
        <!--Tanggal Pertama Kali Diumumkan-->
        <div class="form-group">
            <label>Tanggal Pertama Kali Diumumkan:</label>
            <input type="date" onclick="this.showPicker()" id="tanggal_pengumuman" class="custom-date"
                name="tanggal_pengumuman" required
                oninvalid="this.setCustomValidity('Silakan isi tanggal pengumuman terlebih dahulu.')"
                oninput="this.setCustomValidity('')" />
        </div>
        <!--Judul Hak Cipta-->
        <div class="form-group">
            <label>Judul</label>
            <input type="text" spellcheck="false" name="judul" placeholder="Judul Hak Cipta" required
                oninvalid="this.setCustomValidity('Silakan isi judul hak cipta terlebih dahulu.')"
                oninput="this.setCustomValidity('')" />
        </div>
        <!--Deskripsi-->
        <div class="form-group">
            <label>Deskripsi:</label>
            <textarea name="deskripsi" placeholder="Deskripsi" required
                oninvalid="this.setCustomValidity('Silakan deskripsikan hak cipta terlebih dahulu.')"
                oninput="this.setCustomValidity('')"></textarea>
        </div>
        <!--Negara Pertama Kali Diumumkan-->
        <div class="form-group">
            <label for="nationality">Negara Pertama Kali Diumumkan:</label>
            <select name="negara_pengumuman" id="nationality" required
                oninvalid="this.setCustomValidity('Silakan pilih negara terlebih dahulu.')"
                oninput="this.setCustomValidity('')">
                <option value="">-- Pilih Negara --</option>
            </select>
        </div>
        <!--Kota Pertama Kali Diumumkan-->
        <div class="form-group">
            <label>Kota/Kabupaten Pertama Kali Diumumkan:</label>
            <div id="kota-container">
                <select id="kota_pengumuman" name="kota_pengumuman" class="form-select auto-search"
                    style="display: none;" required
                    oninvalid="this.setCustomValidity('Silakan pilih kota terlebih dahulu.')"
                    oninput="this.setCustomValidity('')">
                    <option value="">-- Pilih Kota/Kabupaten --</option>
                </select>
                <input type="text" spellcheck="false" name="kota_pengumuman" id="kota_pengumuman_input"
                    placeholder="City (Manual Input)" style="display: none;" required
                    oninvalid="this.setCustomValidity('Please fill in the city name first.')"
                    oninput="this.setCustomValidity('')">
            </div>
        </div>
        <!--Lampiran-->
        <div class="form-group">
            <label>Lampiran Dokumen:</label>
            <label for="fileInput" class="custom-file-label">📁 Pilih Dokumen</label>
            <input type="file" name="dokumen" id="fileInput" class="custom-file-input"
                accept=".pdf,.doc,.docx,.zip,.rar,.7z,.tar,.gz" required
                oninvalid="this.setCustomValidity('Belum ada dokumen.')" oninput="this.setCustomValidity('')" />
            <span id="file-name" class="file-name">Belum ada dokumen</span><br>
            <small><em><span style="color: red; font-size: 0.8em;">* </span>Ukuran maksimal dokumen 30MB</em></small>
        </div>
        <!--List Pencipta-->
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

<script src="js/pengajuan_baru.js"></script>
<script src="js/form_pencipta.js"></script>