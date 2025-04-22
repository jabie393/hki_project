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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
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
    <script>
        document.getElementById('jenis_hak_cipta').addEventListener('change', function () {
            var jenis = this.value;
            var subJenis = document.getElementById('sub_jenis_hak_cipta');
            subJenis.innerHTML = '';

            var options = {
                "Karya Tulis": [
                    "Karya Tulis Lainnya", "Naskah Drama / Pertunjukan", "Naskah Karya Siaran",
                    "Buku", "Karya Tulis", "Terjemahan", "Tafsir", "Saduran", "Bunga Rampai",
                    "Perwajahan Karya Tulis", "Naskah Film", "Karya Tulis (Artikel)",
                    "Karya Tulis (Skripsi)", "Karya Tulis (Disertasi)", "Karya Tulis (Tesis)",
                    "Naskah Karya Sinematografi", "Modul", "Novel", "e-Book", "Cerita Bergambar",
                    "Komik", "Buku Panduan / Petunjuk", "Atlas", "Buku Saku", "Buku Pelajaran",
                    "Diktat", "Buku Mewarnai", "Dongeng", "Majalah", "Kamus", "Ensiklopedia",
                    "Biografi", "Booklet", "Jurnal", "Makalah", "Proposal Penelitian",
                    "Resensi", "Resume / Ringkasan", "Sinopsis", "Karya Ilmiah", "Laporan Penelitian", "Puisi"
                ],
                "Karya Seni": [
                    "Seni Gambar", "Alat Peraga", "Pamflet", "Seni Rupa", "Seni Lukis",
                    "Seni Patung", "Seni Pahat", "Kaligrafi", "Seni Motif", "Arsitektur",
                    "Peta", "Brosur", "Seni Terapan", "Karya Seni Rupa", "Ukiran", "Kolase",
                    "Seni Ilustrasi", "Sketsa", "Diorama", "Karya Seni Batik", "Seni Songket",
                    "Motif Tenun Ikat", "Motif Tapis", "Motif Ulos", "Motif Sasirangan",
                    "Seni Motif Lainnya", "Flyer", "Leaflet", "Poster", "Banner", "Spanduk",
                    "Baliho", "Seni Umum"
                ],
                "Komposisi Musik": [
                    "Musik Karawitan", "Lagu (Musik Dengan Teks)", "Musik Tanpa Teks", "Aransemen", "Musik",
                    "Musik Klasik", "Musik Jazz", "Musik Gospel", "Musik Blues", "Musik Rhythm and Blues",
                    "Musik Funk", "Musik Rock", "Musik Metal", "Musik Elektronik", "Musik Ska, Reggae, Dub",
                    "Musik Hip Hop, Rap, Rapcore", "Musik Pop", "Musik Latin", "Musik Country", "Musk Dangdut"
                ],
                "Karya Audio Visual": [
                    "Film", "Karya Rekaman Video", "Kuliah", "Karya Siaran Media Televisi dan Film", "Karya Siaran Media Radio", "Karya Siaran Video",
                    "Karya Sinematografi", "Film Dokumenter", "Film Iklan", "Film Kartun", "Reportase", "Film Cerita", "Karya Siaran"
                ],
                "Karya Fotografi": [
                    "Karya Fotografi", "Potret"
                ],
                "Karya Drama & Koreografi": [
                    "Drama / Pertunjukan", "Pewayangan", "Pantomim", "Koreografi", "Seni Pertunjukan", "Tari (Sendra Tari)", "Drama Musikal",
                    "Ludruk", "Lenong", "Ketoprak", "Komedi / Lawak", "Seni Akrobat", "Opera", "Pentas Musik", "Sulap", "Sirkus"
                ],
                "Karya Rekaman": [
                    "Ceramah", "Pidato", "Karya Rekaman Suara atau Bunyi", "Khutbah"
                ],
                "Karya Lainnya": [
                    "Basis Data", "Kompilasi Ciptaan / Data", "Permainan Video", "Program Komputer"
                ]
            };

            if (options[jenis]) {
                subJenis.innerHTML = '<option value="">-- Pilih Sub Jenis --</option>' +
                    options[jenis].map(option => `<option value="${option}">${option}</option>`).join('');
            }
        });

        // Tambahkan pencipta baru
        document.getElementById("addPencipta").addEventListener("click", function () {
            let original = document.querySelector(".pencipta");
            let clone = original.cloneNode(true);

            // Reset input pada clone
            clone.querySelectorAll("input, select, textarea").forEach(input => input.value = "");

            // Perbarui label pencipta
            let penciptaList = document.getElementById("pencipta-list");
            let penciptaCount = penciptaList.children.length + 1;
            let label = clone.querySelector(".pencipta-label");

            if (!label) {
                label = document.createElement("h4");
                label.classList.add("pencipta-label");
                clone.insertBefore(label, clone.firstChild);
            }
            label.textContent = "Pencipta " + penciptaCount;

            // Tambahkan tombol hapus jika belum ada
            let deleteButton = clone.querySelector(".hapusPencipta");
            if (!deleteButton) {
                deleteButton = document.createElement("button");
                deleteButton.type = "button";
                deleteButton.classList.add("hapusPencipta");
                deleteButton.textContent = "Hapus";
                deleteButton.onclick = function () {
                    clone.remove();
                    updatePenciptaLabels();
                };
                clone.appendChild(deleteButton);
            }

            penciptaList.appendChild(clone);
            updatePenciptaLabels();
        });

        // Fungsi untuk memperbarui label pencipta dan tombol hapus
        function updatePenciptaLabels() {
            let penciptaDivs = document.querySelectorAll(".pencipta");
            penciptaDivs.forEach((div, index) => {
                let label = div.querySelector(".pencipta-label");
                if (!label) {
                    label = document.createElement("h4");
                    label.classList.add("pencipta-label");
                    div.insertBefore(label, div.firstChild);
                }
                label.textContent = "Pencipta " + (index + 1);
            });

            let deleteButtons = document.querySelectorAll(".hapusPencipta");
            if (penciptaDivs.length > 1) {
                deleteButtons.forEach(btn => btn.style.display = "inline-block");
            } else {
                deleteButtons.forEach(btn => btn.style.display = "none");
            }
        }

        // Hapus pencipta yang ada
        document.addEventListener("click", function (event) {
            if (event.target.classList.contains("hapusPencipta")) {
                let penciptaDiv = event.target.closest(".pencipta");
                penciptaDiv.remove();
                updatePenciptaLabels();
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("https://restcountries.com/v3.1/all")
                .then(response => response.json())
                .then(data => {
                    data.sort((a, b) => a.name.common.localeCompare(b.name.common)); // Urutkan abjad

                    let selects = document.querySelectorAll("#nationality, .negara-select");
                    selects.forEach(select => {
                        data.forEach(country => {
                            let option = document.createElement("option");
                            option.value = country.name.common;
                            option.textContent = country.name.common;
                            select.appendChild(option);
                        });
                    });
                })
                .catch(error => console.error("Gagal memuat data negara:", error));
        });
    </script>
    <div>
        <a href="profile.php">Profil</a> |
        <a href="status_pengajuan.php">Lihat Status Pengajuan</a> |
        <a href="update_account.php">Update Data Akun</a> |
        <a href="services/logout.php">Logout</a>
    </div>

</body>

</html>