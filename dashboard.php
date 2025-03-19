<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
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
</head>
<body>
<h2>Form Pendaftaran HKI</h2>
<form action="submit_hki.php" method="POST" enctype="multipart/form-data">
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
        <option value="">Pilih Jenis</option>
        <option value="Ciptaan Seni">Ciptaan Seni</option>
        <option value="Ciptaan Sastra">Ciptaan Sastra</option>
        <option value="Ciptaan Ilmiah">Ciptaan Ilmiah</option>
    </select><br><br>
    <!--Sub Jenis Hak Cipta-->
    <label>Sub Jenis Hak Cipta:</label><br>
    <select name="sub_jenis_hak_cipta" id="sub_jenis_hak_cipta" required>
        <option value="">Pilih Sub Jenis</option>
    </select><br><br>
    <!--Tanggal Pertama Kali Diumumkan-->
    <label>Tanggal Pertama Kali Diumumkan:</label><br>
    <input type="date" name="tanggal_pengumuman" required><br><br>

    <!--Judul Hak Cipta-->
    <label>Judul</label><br>
    <input type="text" name="judul" placeholder="Judul Hak Cipta" required /><br><br>
    <!--Deskripsi-->
    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" placeholder="Deskripsi"></textarea><br><br>
    <!--Negara Pertama Kali Diumumkan-->
    <label>Negara Pertama Kali Diumumkan:</label><br>
    <select name="negara_pengumuman" id="nationality" required>
    <option value="">-- Pilih Negara --</option>
    </select><br><br>
    <!--Kota Pertama Kali Diumumkan-->
    <label>Kota Pertama Kali Diumumkan:</label><br>
    <input type="text" name="kota_pengumuman" placeholder="Nama Kota/Kabupaten" required><br><br>
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
        <input type="text" name="provinsi[]"><br><br>

        <label>Kota/Kabupaten:</label><br>
        <input type="text" name="kota[]"><br><br>

        <label>Kecamatan:</label><br>
        <input type="text" name="kecamatan[]"><br><br>

        <label>Kelurahan:</label><br>
        <input type="text" name="kelurahan[]"><br><br>

        <label>Kode Pos:</label><br>
        <input type="text" name="kode_pos[]"><br><br>
    </div>
</div>

<br><button type="button" id="addPencipta">Tambah Pencipta</button><br><br><br>



    <input type="file" name="dokumen" required /><br><br>
    <button type="submit">Kirim</button><br><br>
</form>
<script>
    document.getElementById('jenis_hak_cipta').addEventListener('change', function() {
        var jenis = this.value;
        var subJenis = document.getElementById('sub_jenis_hak_cipta');
        subJenis.innerHTML = '';

        if (jenis === "Ciptaan Seni") {
            subJenis.innerHTML = '<option value="Lukisan">Lukisan</option><option value="Patung">Patung</option>';
        } else if (jenis === "Ciptaan Sastra") {
            subJenis.innerHTML = '<option value="Novel">Novel</option><option value="Puisi">Puisi</option>';
        } else if (jenis === "Ciptaan Ilmiah") {
            subJenis.innerHTML = '<option value="Penelitian">Penelitian</option><option value="Artikel Ilmiah">Artikel Ilmiah</option>';
        }
    });

// Tambahkan pencipta baru
document.getElementById("addPencipta").addEventListener("click", function() {
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
        deleteButton.onclick = function() {
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
document.addEventListener("click", function(event) {
    if (event.target.classList.contains("hapusPencipta")) {
        let penciptaDiv = event.target.closest(".pencipta");
        penciptaDiv.remove();
        updatePenciptaLabels();
    }
});
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
    <a href="status_pengajuan.php">Lihat Status Pengajuan</a>
</div>
<div>
    <a href="profile.php">Lengkapi Profil</a>
</div>
<div>
    <a href="logout.php">Logout</a>
</div>

</body>
</html>