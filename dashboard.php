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
    <label>Nama Pencipta:</label>
    <div id="pencipta-container">
        <input type="text" name="pencipta[]" placeholder="Nama Pencipta" required />
    </div><br>
    <button type="button" onclick="tambahPencipta()">Tambah Pencipta</button><br><br>

    <input type="file" name="dokumen" required /><br><br>
    <button type="submit">Kirim</button>
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

    function tambahPencipta() {
        let container = document.getElementById('pencipta-container');
        let input = document.createElement("input");
        input.type = "text";
        input.name = "pencipta[]";
        input.placeholder = "Nama Pencipta";
        container.appendChild(input);
    }
</script>
<script>
    fetch('https://restcountries.com/v3.1/all')
        .then(response => response.json())
        .then(data => {
            let select = document.querySelector("select[name='negara_pengumuman']");
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


<h2>Status Pendaftaran</h2>
<table border="1">
    <tr>
        <th>Jenis Permohonan</th>
        <th>Jenis Ciptaan</th>
        <th>Sub Jenis Ciptaan</th>
        <th>Tanggal Pengumuman</th>
        <th>Judul</th>
        <th>Negara Pengumuman</th>
        <th>Kota Pengumuman</th>
        <th>Pencipta</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['jenis_permohonan']; ?></td>
            <td><?php echo $row['jenis_hak_cipta']; ?></td>
            <td><?php echo $row['sub_jenis_hak_cipta']; ?></td>
            <td><?php echo $row['tanggal_pengumuman']; ?></td>
            <td><?php echo $row['judul_hak_cipta']; ?></td>
            <td><?php echo $row['negara_pengumuman']; ?></td>
            <td><?php echo $row['kota_pengumuman']; ?></td>
            <td>
                <ul>
                    <?php foreach (json_decode($row['nama_pencipta'], true) as $pencipta) { ?>
                        <li><?php echo htmlspecialchars($pencipta); ?></li>
                    <?php } ?>
                </ul>
            </td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
                    <a href="cancel_hki.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin membatalkan?')">Batalkan</a>
                <?php } else { ?>
                    Tidak bisa dibatalkan
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>
<div>
    <a href="logout.php">Logout</a>
</div>
<div>
    <a href="profile.php">Lengkapi Profil</a>
</div>
