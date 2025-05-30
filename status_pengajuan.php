<!-- ADMIN & USER -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination setup
$defaultLimit = 5; // Default jumlah data per halaman
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : $defaultLimit; // Ambil limit dari URL, default 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM registrations 
               WHERE user_id = ? 
               AND (nomor_permohonan LIKE ? 
               OR jenis_permohonan LIKE ? 
               OR jenis_hak_cipta LIKE ? 
               OR sub_jenis_hak_cipta LIKE ? 
               OR judul_hak_cipta LIKE ?)";
$stmtTotal = $conn->prepare($totalQuery);
$searchTerm = "%$search%";
$stmtTotal->bind_param("ssssss", $user_id, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result();
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query untuk mengambil data dengan pagination
$query = "SELECT * FROM registrations 
          WHERE user_id = ? 
          AND (nomor_permohonan LIKE ? 
          OR jenis_permohonan LIKE ? 
          OR jenis_hak_cipta LIKE ? 
          OR sub_jenis_hak_cipta LIKE ? 
          OR judul_hak_cipta LIKE ?)
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssii", $user_id, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/hki.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">
</head>

<div id="hki-page">

    <h2>Status Pengajuan</h2>

    <!-- Form Pencarian -->
    <form method="GET" id="search-form" class="search-form">
        <div class="search-group">
            <input type="text" name="search" class="input-field-search" placeholder="Cari Data Pengajuan"
                value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-info">Cari</button>
        </div>
    </form>

    <div class="table-wrapper">
        <table id="hki-table">
            <tr>
                <th>Nomor Permohonan</th>
                <th>Jenis Permohonan</th>
                <th>Jenis Ciptaan</th>
                <th>Sub Jenis Ciptaan</th>
                <th>Tanggal Pengumuman</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Tempat Pengumuman</th>
                <th>Pencipta</th>
                <th>Status</th>
                <th>Sertifikat</th>
                <th>Nomor Sertifikat</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nomor_permohonan'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['jenis_permohonan']); ?></td>
                    <td><?php echo htmlspecialchars($row['jenis_hak_cipta']); ?></td>
                    <td><?php echo htmlspecialchars($row['sub_jenis_hak_cipta']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal_pengumuman']); ?></td>
                    <td><?php echo htmlspecialchars($row['judul_hak_cipta']); ?></td>
                    <td>
                        <button onclick="openDescriptionModal('<?php echo htmlspecialchars($row['deskripsi']); ?>')"
                            class="btn btn-info">
                            Lihat
                        </button>
                    </td>
                    <td>
                        <div><strong>Negara:</strong> <?php echo htmlspecialchars($row['negara_pengumuman']); ?></div>
                        <div><strong>Kota:</strong> <?php echo htmlspecialchars($row['kota_pengumuman']); ?></div>
                    </td>
                    <td>
                        <button type="button" onclick="openModal('<?= $row['id'] ?>')" class="btn btn-info">Detail
                            Pencipta</button>
                    </td>
                    </td>
                    <td><span
                            class="badge badge-<?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                    </td>
                    <td>
                        <?php if (!empty($row['certificate_path'])) { ?>
                            <a href="<?= $row['certificate_path'] ?>" class="btn btn-download" download>Download</a>
                        <?php } else { ?>
                            <span>Belum tersedia</span>
                        <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['nomor_sertifikat'] ?? '-'); ?></td>
                    <td>
                        <?php if ($row['status'] == 'Pending') { ?>
                            <button class="btn btn-danger cancel-btn" data-id="<?= $row['id'] ?>">Batalkan</button>
                        <?php } else { ?>
                            <span style="color: gray;">Tidak bisa dibatalkan</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="javascript:void(0);" class="page-link prev"
                onclick="loadPage(<?= $page - 1; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>')">‹</a>
        <?php endif; ?>

        <!-- Always show first page -->
        <a href="javascript:void(0);" class="page-link <?= $page == 1 ? 'active' : ''; ?>"
            onclick="loadPage(1, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>')">1</a>

        <!-- Middle pages -->
        <?php
        $start = max(2, $page - 1);
        $end = min($totalPages - 1, $page + 1);

        if ($start > 2) {
            echo '<span class="ellipsis">...</span>';
        }

        for ($i = $start; $i <= $end; $i++): ?>
            <a href="javascript:void(0);" class="page-link <?= $i == $page ? 'active' : ''; ?>"
                onclick="loadPage(<?= $i; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>')">
                <?= $i; ?>
            </a>
        <?php endfor; ?>

        <!-- Always show last page if more than one page -->
        <?php if ($totalPages > 1): ?>
            <?php if ($end < $totalPages - 1): ?>
                <span class="ellipsis">...</span>
            <?php endif; ?>
            <a href="javascript:void(0);" class="page-link <?= $page == $totalPages ? 'active' : ''; ?>"
                onclick="loadPage(<?= $totalPages; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>')">
                <?= $totalPages; ?>
            </a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="javascript:void(0);" class="page-link next"
                onclick="loadPage(<?= $page + 1; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>')">›</a>
        <?php endif; ?>
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
</div>