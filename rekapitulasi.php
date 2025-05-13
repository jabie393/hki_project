<!-- Flow FE -->
<!-- ALL -->
<?php
include 'config/config.php';

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination setup
$defaultLimit = 5; // Default untuk laptop
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : $defaultLimit; // Ambil limit dari URL, default 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM registrations 
               WHERE status = 'Terdaftar' 
               AND (nomor_permohonan LIKE '%$search%' 
               OR judul_hak_cipta LIKE '%$search%'
               OR jenis_hak_cipta LIKE '%$search%' 
               OR tanggal_pengumuman LIKE '%$search%'
               OR negara_pengumuman LIKE '%$search%'
               OR kota_pengumuman LIKE '%$search%'
               OR nomor_sertifikat LIKE '%$search%')";
$totalResult = $conn->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query untuk mengambil data dengan pagination
$query = "SELECT id, nomor_permohonan, jenis_hak_cipta, tanggal_pengumuman, judul_hak_cipta, deskripsi, negara_pengumuman, kota_pengumuman, nomor_sertifikat, status
          FROM registrations 
          WHERE status = 'Terdaftar' 
          AND (nomor_permohonan LIKE '%$search%' 
          OR judul_hak_cipta LIKE '%$search%'
          OR jenis_hak_cipta LIKE '%$search%' 
          OR tanggal_pengumuman LIKE '%$search%'
          OR negara_pengumuman LIKE '%$search%'
          OR kota_pengumuman LIKE '%$search%'
          OR nomor_sertifikat LIKE '%$search%')
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Hak Cipta</title>
    <link rel="shortcut icon" href="assets/icon/fcompany.png" type="image/x-icon">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Css -->
    <link rel="stylesheet" href="css/rekapitulasi.css">
    <link rel="stylesheet" href="css/header&footer.css">
    <link rel="stylesheet" href="css/modal.css">
</head>

<body>
    <!-- Background Image -->
    <div class="background-image">
        <img src="assets/image/bg.png" alt="Background image of a university building">
    </div>

    <div class="header"></div>

    <div id="hki-page">
        <div class="search-header">
            <h2>Rekapitulasi Hak Cipta</h2>

            <!-- Form Pencarian -->
            <form method="GET" class="search-form">
                <div class="search-group">
                    <input type="text" name="search" class="input-field" placeholder="Cari Data Hak Cipta"
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-info">Cari</button>
                </div>
            </form>
        </div>

        <!-- Tabel -->
        <div class="table-wrapper">
            <table id="hki-table">
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
                                <button onclick="openDescriptionModal('<?= htmlspecialchars($row['deskripsi']); ?>')"
                                    class="btn btn-info">Lihat</button>
                            </td>
                            <td>
                                <button class="btn btn-info" onclick="showCreator(<?= $row['id']; ?>)">Detail
                                    Pencipta</button>
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

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $page - 1; ?>&limit=<?= $limit; ?>"
                    class="page-link prev">‹</a>
            <?php endif; ?>

            <!-- Always show first page -->
            <a href="?search=<?= htmlspecialchars($search); ?>&page=1&limit=<?= $limit; ?>"
                class="page-link <?= $page == 1 ? 'active' : ''; ?>">1</a>

            <!-- Middle pages -->
            <?php
            $start = max(2, $page - 1);
            $end = min($totalPages - 1, $page + 1);

            if ($start > 2) {
                echo '<span class="ellipsis">...</span>';
            }

            for ($i = $start; $i <= $end; $i++): ?>
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $i; ?>&limit=<?= $limit; ?>"
                    class="page-link <?= $i == $page ? 'active' : ''; ?>">
                    <?= $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Always show last page if more than one page -->
            <?php if ($totalPages > 1): ?>
                <?php if ($end < $totalPages - 1): ?>
                    <span class="ellipsis">...</span>
                <?php endif; ?>
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $totalPages; ?>&limit=<?= $limit; ?>"
                    class="page-link <?= $page == $totalPages ? 'active' : ''; ?>">
                    <?= $totalPages; ?>
                </a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $page + 1; ?>&limit=<?= $limit; ?>"
                    class="page-link next">›</a>
            <?php endif; ?>
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

    <div class="footer"></div>
    <script src="js/index.js"></script>
    <script src="js/hki.js"></script>
</body>

</html>