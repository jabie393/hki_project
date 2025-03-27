<?php
include 'config/config.php';

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query hanya mengambil data dari tabel registrations
$query = "SELECT id, nomor_permohonan, jenis_permohonan, jenis_hak_cipta, sub_jenis_hak_cipta, tanggal_pengumuman, negara_pengumuman, kota_pengumuman, status
          FROM registrations 
          WHERE status = 'Terdaftar' 
          AND (nomor_permohonan LIKE '%$search%' 
          OR jenis_permohonan LIKE '%$search%' 
          OR jenis_hak_cipta LIKE '%$search%' 
          OR sub_jenis_hak_cipta LIKE '%$search%'
          OR tanggal_pengumuman LIKE '%$search%'
          OR negara_pengumuman LIKE '%$search%'
          OR kota_pengumuman LIKE '%$search%')";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Hak Cipta</title>
    <link rel="stylesheet" href="css/pengajuan.css">
    <link rel="stylesheet" href="css/modal.css">
</head>
<body>

<h2>Rekapitulasi Hak Cipta</h2>

<!-- Form Pencarian -->
<form method="GET">
    <input type="text" name="search" placeholder="Cari Data Hak Cipta" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Cari</button>
</form>

<table>
    <tr>
        <th>Nomor Permohonan</th>
        <th>Jenis Permohonan</th>
        <th>Jenis Ciptaan</th>
        <th>Sub Jenis Ciptaan</th>
        <th>Tanggal Pengumuman</th>
        <th>Pencipta</th>
        <th>Pemegang Hak Cipta</th>
        <th>Negara</th>
        <th>Kota</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nomor_permohonan'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['jenis_permohonan']); ?></td>
            <td><?php echo htmlspecialchars($row['jenis_hak_cipta']); ?></td>
            <td><?php echo htmlspecialchars($row['sub_jenis_hak_cipta']); ?></td>
            <td><?php echo htmlspecialchars($row['tanggal_pengumuman']); ?></td>
            <td><button class="btn btn-info" onclick="showCreator(<?php echo $row['id']; ?>)">Detail Pencipta</button></td>
            <td>Universitas Raden Rahmat Malang</td>
            <td><?php echo htmlspecialchars($row['negara_pengumuman']); ?></td>
            <td><?php echo htmlspecialchars($row['kota_pengumuman']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
        </tr>
    <?php } ?>
</table>

<br>
<button class="btn btn-secondary" onclick="history.back()">Kembali</button>

<!-- Modal untuk Detail Pencipta -->
<div id="creatorModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detail Pencipta</h2>
            <button class="close" onclick="closeModal()">&times;</button>
        </div>
        <div id="creatorDetails"></div>
    </div>
</div>

<script>
function showCreator(id) {
    fetch(`rekapitulasi_creator_details.php?id=${id}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById("creatorDetails").innerHTML = data;
            document.getElementById("creatorModal").style.display = "flex";
        });
}

function closeModal() {
    document.getElementById("creatorModal").style.display = "none";
}
</script>

</body>
</html>