<!-- Flow FE -->
<!-- ALL -->
<?php
include 'config/config.php';

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query hanya mengambil data dari tabel registrations
$query = "SELECT id, nomor_permohonan, jenis_hak_cipta, tanggal_pengumuman, judul_hak_cipta, deskripsi, negara_pengumuman, kota_pengumuman, nomor_sertifikat, status
          FROM registrations 
          WHERE status = 'Terdaftar' 
          AND (nomor_permohonan LIKE '%$search%' 
          OR judul_hak_cipta LIKE '%$search%'
          OR jenis_hak_cipta LIKE '%$search%' 
          OR tanggal_pengumuman LIKE '%$search%'
          OR negara_pengumuman LIKE '%$search%'
          OR kota_pengumuman LIKE '%$search%'
          OR nomor_sertifikat LIKE '%$search%')";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Hak Cipta</title>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/pengajuan.css">
    <link rel="stylesheet" href="css/modal.css">
</head>

<body>

<div id="admin-section">
    <h2>Rekapitulasi Hak Cipta</h2>

    <!-- Form Pencarian -->
    <form method="GET" class="search-form">
        <input type="text" name="search" class="input-field" placeholder="Cari Data Hak Cipta"
            value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-info">Cari</button>
    </form>

    <div class="table-wrapper">
        <table id="admin-hki-table">
            <thead>
                <tr>
                    <th>Nomor Permohonan</th>
                    <th>Jenis Ciptaan</th>
                    <th>Tanggal Pengumuman</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Pencipta</th>
                    <th>Pemegang Hak Cipta</th>
                    <th>Tempat</th>
                    <th>Nomor Sertifikat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nomor_permohonan'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['jenis_hak_cipta']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pengumuman']); ?></td>
                        <td><?= htmlspecialchars($row['judul_hak_cipta']); ?></td>
                        <td>
                            <button onclick="openDescriptionModal('<?= htmlspecialchars($row['deskripsi']); ?>')" class="btn btn-info">
                                Lihat
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-info" onclick="showCreator(<?= $row['id']; ?>)">Detail Pencipta</button>
                        </td>
                        <td>Universitas Raden Rahmat Malang</td>
                        <td>
                            <div><strong>Negara:</strong> <?= htmlspecialchars($row['negara_pengumuman']); ?></div>
                            <div><strong>Kota:</strong> <?= htmlspecialchars($row['kota_pengumuman']); ?></div>
                        </td>
                        <td><?= htmlspecialchars($row['nomor_sertifikat'] ?? '-'); ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($row['status']) ?>">
                                <?= htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Modal untuk Deskripsi -->
    <div id="modal-page">
    <div id="descriptionModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Deskripsi Ciptaan</h2>
                <button class="close" onclick="closeDescriptionModal()">&times;</button>
            </div>
            <div id="descriptionDetails"></div>
        </div>
    </div>
</div>

    <!-- Modal untuk Detail Pencipta -->
    <div id="modal-page">
    <div id="creatorModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Pencipta</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div id="creatorDetails"></div>
        </div>
    </div>
</div>

<script src="js/hki.js"></script>

</body>

</html>