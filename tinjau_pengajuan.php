<!-- Flow FE -->
<!-- ADMIN -->
<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login");
    exit();
}

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination setup
$defaultLimit = 5; // Default jumlah data per halaman
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : $defaultLimit; // Ambil limit dari URL, default 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil parameter order dari URL, default DESC
$order = isset($_GET['order']) ? ($_GET['order'] === 'DESC' ? 'DESC' : 'ASC') : 'DESC';

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM registrations
                JOIN users ON registrations.user_id = users.id
                WHERE registrations.status NOT IN ('Terdaftar', 'Ditolak')
                AND (users.username LIKE '%$search%'
                OR registrations.jenis_pengajuan LIKE '%$search%'
                OR registrations.jenis_hak_cipta LIKE '%$search%'
                OR registrations.sub_jenis_hak_cipta LIKE '%$search%'
                OR registrations.tanggal_pengumuman LIKE '%$search%'
                OR registrations.created_at LIKE '%$search%'
                OR registrations.judul_hak_cipta LIKE '%$search%'
                OR registrations.negara_pengumuman LIKE '%$search%'
                OR registrations.kota_pengumuman LIKE '%$search%')";
$totalResult = $conn->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query untuk mengambil data dengan pagination dan urutan
$query = "SELECT registrations.*, users.username FROM registrations
            JOIN users ON registrations.user_id = users.id
            WHERE registrations.status NOT IN ('Terdaftar', 'Ditolak')
            AND (users.username LIKE '%$search%'
            OR registrations.nomor_pengajuan LIKE '%$search%'
            OR registrations.jenis_pengajuan LIKE '%$search%'
            OR registrations.jenis_hak_cipta LIKE '%$search%'
            OR registrations.sub_jenis_hak_cipta LIKE '%$search%'
            OR registrations.tanggal_pengumuman LIKE '%$search%'
            OR registrations.created_at LIKE '%$search%'
            OR registrations.judul_hak_cipta LIKE '%$search%'
            OR registrations.negara_pengumuman LIKE '%$search%'
            OR registrations.kota_pengumuman LIKE '%$search%'
            OR registrations.nomor_sertifikat LIKE '%$search%')
            ORDER BY created_at $order
            LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/hak_cipta.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">
</head>

<!-- tinjau_pengajuan.php (HTML Table Layout) -->
<div id="hak_cipta-page">
    <h2>Daftar Pengajuan Hak Cipta</h2>

    <!-- Form Pencarian -->
    <form method="GET" id="search-form" class="search-form">
        <div class="search-group">
            <input type="text" spellcheck="false" name="search" class="input-field-search"
                placeholder="Cari Data Pengajuan" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-info">Cari</button>
        </div>
    </form>

    <div class="table-wrapper">
        <table id="hak_cipta-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>
                        <div class="sortable-header">
                            <a href="javascript:void(0);"
                                onclick="loadContent('tinjau_pengajuan.php?page=<?= $page; ?>&order=<?= $order === 'ASC' ? 'DESC' : 'ASC'; ?>')"
                                class="sort-link <?= $order === 'ASC' ? 'active-order' : ''; ?>"
                                title="<?= $order === 'ASC' ? 'Urutkan Dari Yang Terbaru' : 'Urutkan Dari Yang Terlama'; ?>">
                                Tanggal Pengajuan
                            </a>
                            <div class="sort-buttons">
                                <a href="javascript:void(0);"
                                    onclick="loadContent('tinjau_pengajuan.php?page=<?= $page; ?>&order=ASC')"
                                    id="sort-asc" class="<?= $order === 'ASC' ? 'active-order' : ''; ?>"
                                    title="Urutkan Dari Yang Terlama">&#9650;</a>
                                <a href="javascript:void(0);"
                                    onclick="loadContent('tinjau_pengajuan.php?page=<?= $page; ?>&order=DESC')"
                                    class="<?= $order === 'DESC' ? 'active-order' : ''; ?>"
                                    title="Urutkan Dari Yang Terbaru">&#9660;</a>
                            </div>
                        </div>
                    </th>
                    <th>Judul</th>
                    <th>Detail Ciptaan</th>
                    <th>Pencipta</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Nomor Hak Cipta</th>
                    <th>Sertifikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <form id="form_<?= $row['id'] ?>" enctype="multipart/form-data">
                        <tr id="row_<?= $row['id'] ?>">
                            <td><a href="javascript:void(0)" onclick="showProfile(<?= $row['user_id'] ?>)"
                                    class="profile-link"><?= htmlspecialchars($row['username']) ?></a></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                            <td><?= htmlspecialchars($row['judul_hak_cipta']) ?></td>
                            <td>
                                <button type="button" class="margin btn btn-info"
                                    onclick="openDetailCiptaanModal(<?= $row['id'] ?>)">Lihat</button>
                            </td>
                            <td><button type="button" onclick="openModal('<?= $row['id'] ?>')" class="btn btn-info">Detail
                                    Pencipta</button></td>
                            <td>
                                <?php
                                $reg_id = $row['id'];
                                $files = $conn->query("SELECT file_name, file_path FROM documents WHERE registration_id = '$reg_id'");
                                while ($file = $files->fetch_assoc()) {
                                    echo '<a href="' . htmlspecialchars($file['file_path']) . '" download="' . htmlspecialchars($file['file_name']) . '" class="btn btn-download">Download</a><br>';
                                }
                                ?>
                            </td>
                            <td class="status-td"><span
                                    class="badge badge-<?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                            </td>
                            <td class="nomor-cell">
                                <div class="nomor-fields">
                                    <label class="file-label">Nomor Pengajuan</label>
                                    <input type="text" spellcheck="false" name="nomor_pengajuan"
                                        id="nomor_pengajuan_<?= $row['id'] ?>"
                                        value="<?= htmlspecialchars($row['nomor_pengajuan'] ?? '') ?>" class="input-field"
                                        placeholder="Opsional">
                                    <label class="file-label">Nomor Sertifikat</label>
                                    <input type="text" spellcheck="false" name="nomor_sertifikat"
                                        id="nomor_sertifikat_<?= $row['id'] ?>"
                                        value="<?= htmlspecialchars($row['nomor_sertifikat'] ?? '') ?>" class="input-field"
                                        placeholder="Opsional">
                                </div>
                            </td>
                            <td>
                                <div class="input-wrapper">
                                    <label for="certificate_<?= $row['id'] ?>" class="file-label">Sertifikat
                                        (Opsional)</label>
                                    <div class="button-row">
                                        <div class="custom-file-container">
                                            <label for="certificate_<?= $row['id'] ?>"
                                                class="custom-file-label btn btn-info">Pilih
                                                File</label>
                                            <input type="file" id="certificate_<?= $row['id'] ?>" name="certificate"
                                                class="input-file" accept="image/*,.pdf,.doc,.docx,.zip,.rar,.7z,.tar,.gz">
                                        </div>
                                    </div>
                                    <span class="file-name" id="file-name-<?= $row['id'] ?>">Tidak ada file yang
                                        dipilih</span>
                                </div>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Pending') { ?>
                                    <div class="action-dropdown">
                                        <button type="button" onclick="toggleDropdown(this)" class="btn action-button">
                                            Aksi
                                        </button>
                                        <div class="dropdown-menu">
                                            <button type="button" id="reject-btn" class="top-btn red-btn"
                                                data-id="<?= $row['id'] ?>" data-row="row_<?= $row['id'] ?>">
                                                <i class="bx bxs-trash"></i> Tolak
                                            </button>
                                            <button id="review-btn" class="blue-btn" data-id="<?= $row['id'] ?>"
                                                data-row="row_<?= $row['id'] ?>">
                                                <i class="bx bx-search-alt-2"></i> Tinjau
                                            </button>
                                            <button type="button" id="approve-btn" class="bottom-btn green-btn"
                                                data-id="<?= $row['id'] ?>" data-form="form_<?= $row['id'] ?>"
                                                data-user="<?= $row['user_id'] ?>">
                                                <i class="bx bx-check"></i>
                                                Setujui
                                            </button>
                                        <?php } else if ($row['status'] == 'Ditinjau') { ?>
                                                <div class="action-dropdown">
                                                    <button type="button" onclick="toggleDropdown(this)" class="btn action-button">
                                                        Aksi
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button type="button" id="reject-btn" class="top-btn red-btn"
                                                            data-id="<?= $row['id'] ?>" data-row="row_<?= $row['id'] ?>">
                                                            <i class="bx bxs-trash"></i> Tolak
                                                        </button>
                                                        <button id="update_review-btn" class="amber-btn blue-btn"
                                                            data-id="<?= $row['id'] ?>" data-row="row_<?= $row['id'] ?>">
                                                            <i class="bx bx-refresh"></i> Perbarui
                                                        </button>
                                                        <button type="button" id="approve-btn" class="bottom-btn green-btn"
                                                            data-id="<?= $row['id'] ?>" data-form="form_<?= $row['id'] ?>"
                                                            data-user="<?= $row['user_id'] ?>">
                                                            <i class="bx bx-check"></i>
                                                            Setujui
                                                        </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                            </td>
                        </tr>
                    </form>
                <?php } ?>
            </tbody>
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
</div>
</div>


<!-- Modal untuk Profil User -->
<div id="modal-page">
    <div id="profileModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Profil Pengguna</h2>
                <button class="close" onclick="closeProfileModal()">&times;</button>
            </div>
            <div id="profileDetails"></div>
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

<script src="js/hak_cipta.js"></script>
<script src="js/actions.js"></script>