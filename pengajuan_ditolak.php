<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil nomor admin untuk konsultasi banding
$adminQuery = mysqli_query($conn, "SELECT admin_number, consult_phone_code FROM consultation_number LIMIT 1");
$adminData = mysqli_fetch_assoc($adminQuery);
$consultPhoneCode = $adminData['consult_phone_code'];
$adminNumber = $consultPhoneCode . $adminData['admin_number'];

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$defaultLimit = 10;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : $defaultLimit;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil parameter order dari URL, default DESC
$order = isset($_GET['order']) ? ($_GET['order'] === 'DESC' ? 'DESC' : 'ASC') : 'DESC';

// Query untuk menghitung total data dengan prepared statement
$totalQuery = "SELECT COUNT(*) as total FROM registrations
                WHERE user_id = ?
                AND status = 'Ditolak'
                AND (
                    nomor_pengajuan LIKE ? OR
                    jenis_pengajuan LIKE ? OR
                    jenis_hak_cipta LIKE ? OR
                    sub_jenis_hak_cipta LIKE ? OR
                    tanggal_pengumuman LIKE ? OR
                    created_at LIKE ? OR
                    judul_hak_cipta LIKE ? OR
                    negara_pengumuman LIKE ? OR
                    kota_pengumuman LIKE ?
                )";
$stmt = $conn->prepare($totalQuery);
$likeSearch = "%$search%";
$stmt->bind_param(
    "ssssssssss",
    $user_id,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch
);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);
$stmt->close();

// Query untuk mengambil data dengan prepared statement, urutan dan pagination
$query = "SELECT * FROM registrations
            WHERE user_id = ?
            AND status = 'Ditolak'
            AND (
                nomor_pengajuan LIKE ? OR
                jenis_pengajuan LIKE ? OR
                jenis_hak_cipta LIKE ? OR
                sub_jenis_hak_cipta LIKE ? OR
                tanggal_pengumuman LIKE ? OR
                created_at LIKE ? OR
                judul_hak_cipta LIKE ? OR
                negara_pengumuman LIKE ? OR
                kota_pengumuman LIKE ?
            )
            ORDER BY created_at $order
            LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param(
    "ssssssssssii",
    $user_id,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $likeSearch,
    $limit,
    $offset
);
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
    <link rel="stylesheet" href="css/hak_cipta.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">
</head>

<div id="hak_cipta-page">

    <h2>Pengajuan Hak Cipta Ditolak</h2>

    <!-- Form Pencarian -->
    <form method="GET" id="search-form" class="search-form">
        <div class="search-group">
            <input type="text" name="search" class="input-field-search" placeholder="Cari Data Pengajuan"
                value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-info">Cari</button>
        </div>
    </form>

    <div class="table-wrapper">
        <table id="hak_cipta-table">
            <tr>
                <th>Nomor Pengajuan</th>
                <th>
                    <div class="sortable-header">
                        <a href="javascript:void(0);"
                            onclick="loadContent('pengajuan_ditolak.php?page=<?= $page; ?>&order=<?= $order === 'ASC' ? 'DESC' : 'ASC'; ?>')"
                            class="sort-link <?= $order === 'ASC' ? 'active-order' : ''; ?>"
                            title="<?= $order === 'ASC' ? 'Urutkan Dari Yang Terbaru' : 'Urutkan Dari Yang Terlama'; ?>">
                            Tanggal Pengajuan
                        </a>
                        <div class="sort-buttons">
                            <a href="javascript:void(0);"
                                onclick="loadContent('pengajuan_ditolak.php?page=<?= $page; ?>&order=ASC')"
                                id="sort-asc" class="<?= $order === 'ASC' ? 'active-order' : ''; ?>"
                                title="Urutkan Dari Yang Terlama">&#9650;</a>
                            <a href="javascript:void(0);"
                                onclick="loadContent('pengajuan_ditolak.php?page=<?= $page; ?>&order=DESC')"
                                class="<?= $order === 'DESC' ? 'active-order' : ''; ?>"
                                title="Urutkan Dari Yang Terbaru">&#9660;</a>
                        </div>
                    </div>
                </th>
                <th>Judul</th>
                <th>Detail Ciptaan</th>
                <th>Pencipta</th>
                <th>Status</th>
                <th>File Penolakan</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <?php
                $noPengajuan = !empty($row['nomor_pengajuan']) ? urlencode($row['nomor_pengajuan']) : '–';
                $judul = urlencode($row['judul_hak_cipta']);
                $pesanBanding = "Yth. Admin LPPM UNIRA Malang,%0A%0ASaya ingin mengajukan banding atas Hak Cipta saya yang saat ini berstatus Ditolak.%0A%0A- Judul: $judul%0A- No. Pengajuan: $noPengajuan%0A- Dengan alasan: %0A%0AMohon pertimbangannya kembali.%0A%0ATerima kasih.";
                ?>
                <tr id="row_<?= $row['id'] ?>">
                    <td><?php echo htmlspecialchars($row['nomor_pengajuan'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                    <td><?php echo htmlspecialchars($row['judul_hak_cipta']); ?></td>
                    <td>
                        <button type="button" class="btn btn-info"
                            onclick="openDetailCiptaanModal(<?= $row['id'] ?>)">Lihat</button>
                    </td>
                    <td>
                        <button type="button" onclick="openModal('<?= $row['id'] ?>')" class="btn btn-info">Detail
                            Pencipta</button>
                    </td>
                    <td class="status-td">
                        <?php
                        $rejected_at = new DateTime($row['rejected_at']);
                        $now = new DateTime();
                        $interval = $now->diff($rejected_at);
                        $days_passed = $interval->days;
                        $days_left = max(0, 7 - $days_passed);
                        ?>
                        <span class="badge badge-ditolak badge-countdown status-tooltip"
                            data-tooltip="<?= $days_left > 0 ? 'Akan dihapus otomatis dalam ' . $days_left . ' hari' : 'Menunggu penghapusan otomatis' ?>">
                            Ditolak
                            <?php if ($days_left > 0): ?>
                                <i class='bx bxs-hourglass'></i> <?= $days_left ?>d
                            <?php endif; ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($row['rejection_path'])) { ?>
                            <a href="<?= $row['rejection_path'] ?>" class="btn btn-download" download>Download</a>
                        <?php } else { ?>
                            <span>Belum tersedia</span>
                        <?php } ?>
                    </td>
                    <td class="action-td">
                        <div class="action-dropdown">
                            <button type="button" onclick="toggleDropdown(this)" class="btn action-button">
                                Aksi
                            </button>
                            <div class="dropdown-menu sp">
                                <button id="delete-btn" class="top-btn red-btn" data-id="<?= $row['id'] ?>"
                                    data-row="row_<?= $row['id'] ?>"><i class="bx bxs-trash"></i> Hapus</button>
                                <a href="https://wa.me/<?= $adminNumber ?>?text=<?= $pesanBanding ?>" target="_blank"
                                    class="button bottom-btn yellow-btn">
                                    <i class='bx bx-conversation'></i> Banding
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="javascript:void(0);" class="page-link prev"
                onclick="loadPage(<?= $page - 1; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>', '<?= $order; ?>')">&laquo;</a>
        <?php endif; ?>

        <!-- Always show first page -->
        <a href="javascript:void(0);" class="page-link <?= $page == 1 ? 'active' : ''; ?>"
            onclick="loadPage(1, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>', '<?= $order; ?>')">1</a>

        <!-- Middle pages -->
        <?php
        $start = max(2, $page - 1);
        $end = min($totalPages - 1, $page + 1);

        if ($start > 2) {
            echo '<span class="ellipsis">...</span>';
        }

        for ($i = $start; $i <= $end; $i++): ?>
            <a href="javascript:void(0);" class="page-link <?= $i == $page ? 'active' : ''; ?>"
                onclick="loadPage(<?= $i; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>', '<?= $order; ?>')">
                <?= $i; ?>
            </a>
        <?php endfor; ?>

        <!-- Always show last page if more than one page -->
        <?php if ($totalPages > 1): ?>
            <?php if ($end < $totalPages - 1): ?>
                <span class="ellipsis">...</span>
            <?php endif; ?>
            <a href="javascript:void(0);" class="page-link <?= $page == $totalPages ? 'active' : ''; ?>"
                onclick="loadPage(<?= $totalPages; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>', '<?= $order; ?>')">
                <?= $totalPages; ?>
            </a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="javascript:void(0);" class="page-link next"
                onclick="loadPage(<?= $page + 1; ?>, <?= $limit; ?>, '<?= htmlspecialchars($search); ?>', '<?= $order; ?>')">&raquo;</a>
        <?php endif; ?>
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

    <script src="js/hak_cipta.js"></script>
    <script src="js/actions.js"></script>
</div>