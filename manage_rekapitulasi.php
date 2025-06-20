<!-- Flow FE -->
<!-- ADMIN -->
<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login");
    exit();
}

// Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination setup
$defaultLimit = 8; // Default jumlah data per halaman
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : $defaultLimit; // Ambil limit dari URL, default 10
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil parameter order dari URL, default DESC
$order = isset($_GET['order']) ? ($_GET['order'] === 'DESC' ? 'DESC' : 'ASC') : 'DESC';

// Query untuk menghitung total data
$totalQuery = "SELECT COUNT(*) as total FROM registrations
                JOIN users ON registrations.user_id = users.id
                WHERE registrations.status = 'Terdaftar'
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
                OR registrations.nomor_sertifikat LIKE '%$search%')";
$totalResult = $conn->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query untuk mengambil data dengan pagination dan urutan
$query = "SELECT registrations.*, users.username FROM registrations
            JOIN users ON registrations.user_id = users.id
            WHERE registrations.status = 'Terdaftar'
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

    <link rel="stylesheet" href="css/hak_cipta.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">
</head>

<div id="hak_cipta-page">
    <h2>Rekapitulasi Hak Cipta Terdaftar</h2>

    <!-- Form Pencarian -->
    <form method="GET" id="search-form" class="search-form">
        <div class="search-group">
            <input type="text" spellcheck="false" name="search" class="input-field-search"
                placeholder="Cari Data Hak Cipta" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-info">Cari</button>
        </div>
    </form>

    <div class="table-wrapper">
        <table id="hak_cipta-table">
            <thead>
                <tr>
                    <th>Username Pemilik</th>
                    <th>Nomor Pengajuan</th>
                    <th>
                        <div class="sortable-header">
                            <a href="javascript:void(0);"
                                onclick="loadContent('manage_rekapitulasi.php?page=<?= $page; ?>&order=<?= $order === 'ASC' ? 'DESC' : 'ASC'; ?>')"
                                class="sort-link <?= $order === 'ASC' ? 'active-order' : ''; ?>"
                                title="<?= $order === 'ASC' ? 'Urutkan Dari Yang Terbaru' : 'Urutkan Dari Yang Terlama'; ?>">
                                Tanggal Pengajuan
                            </a>
                            <div class="sort-buttons">
                                <a href="javascript:void(0);"
                                    onclick="loadContent('manage_rekapitulasi.php?page=<?= $page; ?>&order=ASC')"
                                    id="sort-asc"
                                    class="<?= $order === 'ASC' ? 'active-order' : ''; ?>"
                                    title="Urutkan Dari Yang Terlama">&#9650;</a>
                                <a href="javascript:void(0);"
                                    onclick="loadContent('manage_rekapitulasi.php?page=<?= $page; ?>&order=DESC')"
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
                    <th>Sertifikat</th>
                    <th>Edit Sertifikat</th>
                    <th>Nomor Sertifikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr id="row-<?= $row['id'] ?>">
                        <td>
                            <a href="javascript:void(0)" onclick="showProfile(<?php echo $row['user_id']; ?>)"
                                class="profile-link">
                                <?php echo htmlspecialchars($row['username']); ?>
                            </a>
                        </td>
                        <td class="aksi-buttons">
                            <input type="text" spellcheck="false" id="nomor_pengajuan_<?= $row['id'] ?>"
                                value="<?= htmlspecialchars($row['nomor_pengajuan'] ?? '') ?>" class="input-field">
                            <button class="btn btn-safe edit-nomor-pengajuan-btn" data-id="<?= $row['id'] ?>">Edit &
                                Simpan</button>
                        </td>
                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                        <td><?= htmlspecialchars($row['judul_hak_cipta']); ?></td>
                        <td>
                            <button type="button" class="btn btn-info"
                                onclick="openDetailCiptaanModal(<?= $row['id'] ?>)">Lihat</button>
                        </td>
                        <td>
                            <button onclick="openModal('<?php echo $row['id']; ?>')" class="btn btn-info">Detail
                                Pencipta</button>
                        </td>
                        <td>
                            <?php
                            $reg_id = $row['id'];
                            $files = $conn->query("SELECT file_name, file_path FROM documents WHERE registration_id = '$reg_id'");
                            while ($file = $files->fetch_assoc()) {
                                echo '<a href="' . htmlspecialchars($file['file_path']) . '" download="' . htmlspecialchars($file['file_name']) . '" class="btn btn-download">Download</a><br>';
                            }
                            ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= strtolower($row['status']) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td id="certificate-container-<?= $row['id'] ?>">
                            <?php if (!empty($row['certificate_path'])) { ?>
                                <a href="<?= $row['certificate_path'] ?>" class="btn btn-download" download>Download</a>
                            <?php } else { ?>
                                <span>Belum ada sertifikat</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="input-wrapper">
                                <div class="button-row">
                                    <div class="custom-file-container">
                                        <label for="edit_certificate_<?= $row['id'] ?>"
                                            class="custom-file-label btn btn-info">Pilih
                                            File</label>
                                        <input type="file" id="edit_certificate_<?= $row['id'] ?>" class="input-file"
                                            accept="image/*,.pdf,.doc,.docx,.zip,.rar,.7z,.tar,.gz" required>
                                    </div>
                                    <button type="button" class="btn btn-safe edit-certificate-btn"
                                        data-id="<?= $row['id'] ?>">Edit</button>
                                </div>
                                <span class="file-name" id="file-name-<?= $row['id'] ?>">Tidak ada file yang dipilih</span>
                            </div>
                        </td>
                        <td class="aksi-buttons">
                            <input type="text" spellcheck="false" id="nomor_sertifikat_<?= $row['id'] ?>"
                                value="<?= htmlspecialchars($row['nomor_sertifikat'] ?? '') ?>" class="input-field">
                            <button class="btn btn-safe edit-nomor-sertifikat-btn" data-id="<?= $row['id'] ?>">Edit &
                                Simpan</button>
                        </td>
                        <td>
                            <button class="btn btn-danger delete-btn" data-id="<?= $row['id']; ?>"
                                data-row="row-<?= $row['id']; ?>">
                                Hapus
                            </button>
                        </td>
                    </tr>
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