<?php
include 'config/config.php';

// Ambil total hak cipta terdaftar
$totalRegisteredQuery = "SELECT COUNT(*) as total FROM registrations WHERE status = 'Terdaftar'";
$totalRegisteredResult = $conn->query($totalRegisteredQuery);
$totalRegistered = $totalRegisteredResult->fetch_assoc()['total'];

// Ambil data untuk grafik bar (jumlah hak cipta terdaftar per tahun)
$chartTahunQuery = "SELECT YEAR(created_at) as tahun, COUNT(*) as jumlah
                    FROM registrations
                    WHERE status = 'Terdaftar'
                    GROUP BY YEAR(created_at)";
$chartTahunResult = $conn->query($chartTahunQuery);
$chartTahunLabels = [];
$chartTahunData = [];
while ($row = $chartTahunResult->fetch_assoc()) {
    $chartTahunLabels[] = $row['tahun'];
    $chartTahunData[] = $row['jumlah'];
}

// Ambil data untuk grafik pie (distribusi jenis hak cipta)
$chartJenisQuery = "SELECT jenis_hak_cipta, COUNT(*) as jumlah FROM registrations GROUP BY jenis_hak_cipta";
$chartJenisResult = $conn->query($chartJenisQuery);
$chartJenisLabels = [];
$chartJenisData = [];
while ($row = $chartJenisResult->fetch_assoc()) {
    $chartJenisLabels[] = $row['jenis_hak_cipta'];
    $chartJenisData[] = $row['jumlah'];
}

// Siapkan data untuk chart
$chartData = [
    'tahunLabels' => $chartTahunLabels,
    'tahunData' => $chartTahunData,
    'jenisLabels' => $chartJenisLabels,
    'jenisData' => $chartJenisData
];

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination setup
$defaultLimit = 5; // Default untuk laptop
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : $defaultLimit; // Ambil limit dari URL, default 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil parameter order dari URL, default DESC
$order = isset($_GET['order']) ? ($_GET['order'] === 'DESC' ? 'DESC' : 'ASC') : 'DESC';

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM registrations
                WHERE status = 'Terdaftar'
                AND (nomor_pengajuan LIKE '%$search%'
                OR judul_hak_cipta LIKE '%$search%'
                OR jenis_hak_cipta LIKE '%$search%'
                OR tanggal_pengumuman LIKE '%$search%'
                OR created_at LIKE '%$search%'
                OR negara_pengumuman LIKE '%$search%'
                OR kota_pengumuman LIKE '%$search%'
                OR nomor_sertifikat LIKE '%$search%')";
$totalResult = $conn->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query untuk mengambil data dengan pagination dan urutan
$query = "SELECT id, nomor_pengajuan, jenis_hak_cipta, tanggal_pengumuman, created_at, judul_hak_cipta, deskripsi, negara_pengumuman, kota_pengumuman, nomor_sertifikat, status
            FROM registrations
            WHERE status = 'Terdaftar'
            AND (nomor_pengajuan LIKE '%$search%'
            OR judul_hak_cipta LIKE '%$search%'
            OR jenis_hak_cipta LIKE '%$search%'
            OR tanggal_pengumuman LIKE '%$search%'
            OR created_at LIKE '%$search%'
            OR negara_pengumuman LIKE '%$search%'
            OR kota_pengumuman LIKE '%$search%'
            OR nomor_sertifikat LIKE '%$search%')
            ORDER BY created_at $order
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    <div id="hak_cipta-page">
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
            <table id="hak_cipta-table">
                <thead>
                    <tr>
                        <th>Nomor Pengajuan</th>
                        <th>Jenis Ciptaan</th>
                        <th>
                            <div class="sortable-header">
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['order' => $order === 'ASC' ? 'DESC' : 'ASC'])); ?>"
                                    class="sort-link <?= $order === 'ASC' ? 'active-order' : ''; ?>"
                                    title="<?= $order === 'ASC' ? 'Urutkan Dari Yang Terbaru' : 'Urutkan Dari Yang Terlama'; ?>">
                                    Tanggal Pengajuan
                                </a>
                                <div class="sort-buttons">
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['order' => 'ASC'])); ?>"
                                        id="sort-asc"
                                        class="<?= $order === 'ASC' ? 'active-order' : ''; ?>"
                                        title="Urutkan Dari Yang Terlama">&#9650;</a>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['order' => 'DESC'])); ?>"
                                        class="<?= $order === 'DESC' ? 'active-order' : ''; ?>"
                                        title="Urutkan Dari Yang Terbaru">&#9660;</a>
                                </div>
                            </div>
                        </th>
                        <th>Judul</th>
                        <th>Ciptaan</th>
                        <th>Pencipta</th>
                        <th>Pemegang Hak Cipta</th>
                        <th>Nomor Sertifikat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nomor_pengajuan'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($row['jenis_hak_cipta']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                            <td><?= htmlspecialchars($row['judul_hak_cipta']); ?></td>
                            <td>
                                <button type="button" class="btn btn-info"
                                    onclick="openDetailCiptaanModal(<?= $row['id'] ?>)">Detail Ciptaan</button>
                            </td>
                            <td>
                                <button class="btn btn-info" onclick="showCreator(<?= $row['id']; ?>)">Detail
                                    Pencipta</button>
                            </td>
                            <td>Universitas Raden Rahmat Malang</td>
                            <td><?= htmlspecialchars($row['nomor_sertifikat'] ?? '-'); ?></td>
                            <td class="status-td">
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
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $page - 1; ?>&limit=<?= $limit; ?>&order=<?= $order; ?>"
                    class="page-link prev">&laquo;</a>
            <?php endif; ?>

            <!-- Always show first page -->
            <a href="?search=<?= htmlspecialchars($search); ?>&page=1&limit=<?= $limit; ?>&order=<?= $order; ?>"
                class="page-link <?= $page == 1 ? 'active' : ''; ?>">1</a>

            <!-- Middle pages -->
            <?php
            $start = max(2, $page - 1);
            $end = min($totalPages - 1, $page + 1);

            if ($start > 2) {
                echo '<span class="ellipsis">...</span>';
            }

            for ($i = $start; $i <= $end; $i++): ?>
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $i; ?>&limit=<?= $limit; ?>&order=<?= $order; ?>"
                    class="page-link <?= $i == $page ? 'active' : ''; ?>">
                    <?= $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Always show last page if more than one page -->
            <?php if ($totalPages > 1): ?>
                <?php if ($end < $totalPages - 1): ?>
                    <span class="ellipsis">...</span>
                <?php endif; ?>
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $totalPages; ?>&limit=<?= $limit; ?>&order=<?= $order; ?>"
                    class="page-link <?= $page == $totalPages ? 'active' : ''; ?>">
                    <?= $totalPages; ?>
                </a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?search=<?= htmlspecialchars($search); ?>&page=<?= $page + 1; ?>&limit=<?= $limit; ?>&order=<?= $order; ?>"
                    class="page-link next">&raquo;</a>
            <?php endif; ?>
        </div>

        <div class="layout">
            <!-- Kiri -->
            <div class="left">
                <div class="box stat-card-left">
                    <h1 id="registeredCount"><?php echo $totalRegistered; ?></h1>
                    <p>Total Hak Cipta Terdaftar</p>
                </div>
                <div class="box">
                    <h3>Hak Cipta Terdaftar per Tahun</h3>
                    <canvas id="chartTahun"></canvas>
                </div>
            </div>
            <!-- Kanan -->
            <div class="right">
                <!-- Pie Chart -->
                <div class="box">
                    <h3>Distribusi Jenis Hak Cipta</h3>
                    <canvas id="chartJenis"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Detail Ciptaan -->
    <div id="modal-page">
        <div id="detailCiptaanModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Detail Ciptaan</h2>
                    <button class="close" onclick="closeDetailCiptaanModal()">&times;</button>
                </div>
                <div id="detailCiptaanDetails"></div>
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
    <script src="js/rekapitulasi.js"></script>
    <script src="js/hak_cipta.js"></script>
    <script src="js/admin.js"></script>
    <script>
        // Kirim data PHP ke JavaScript
        const chartData = <?php echo json_encode($chartData); ?>;
    </script>
</body>

</html>