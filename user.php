<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query untuk menghitung hak cipta terdaftar (status 'Terdaftar')
$registeredQuery = "SELECT COUNT(*) as total FROM registrations 
                   WHERE user_id = ? AND status = 'Terdaftar'";
$stmtRegistered = $conn->prepare($registeredQuery);
$stmtRegistered->bind_param("s", $user_id);
$stmtRegistered->execute();
$registeredResult = $stmtRegistered->get_result();
$registeredCount = $registeredResult->fetch_assoc()['total'];

// Query untuk menghitung pasca hak cipta terdaftar (status 'Pending')
$postRegisteredQuery = "SELECT COUNT(*) as total FROM registrations 
                       WHERE user_id = ? AND status = 'Pending'";
$stmtPostRegistered = $conn->prepare($postRegisteredQuery);
$stmtPostRegistered->bind_param("s", $user_id);
$stmtPostRegistered->execute();
$postRegisteredResult = $stmtPostRegistered->get_result();
$postRegisteredCount = $postRegisteredResult->fetch_assoc()['total'];
?>

<head>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/alert.css">

</head>

<div id="user-page">
    <div class="container">
        <div class="card-container">
            <div class="card">
                <h1><?php echo htmlspecialchars($registeredCount); ?></h1>
                <p>Hak Cipta Terdaftar</p>
            </div>
            <div class="card">
                <h1><?php echo htmlspecialchars($postRegisteredCount); ?></h1>
                <p>Pasca Hak Cipta Terdaftar</p>
            </div>
        </div>

        <div class="section">
            <h3>Hak Cipta Yang Disetujui</h3>
            <div class="table-wrapper">
                <table id="approvedTable" class="hki-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Pencipta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $approvedQuery = "SELECT id, judul_hak_cipta, created_at 
                                FROM registrations 
                                WHERE user_id = ? AND status = 'Terdaftar'
                                ORDER BY created_at DESC";
                        $stmtTerdaftar = $conn->prepare($approvedQuery);
                        $stmtTerdaftar->bind_param("s", $user_id);
                        $stmtTerdaftar->execute();
                        $approvedResult = $stmtTerdaftar->get_result();

                        if ($approvedResult->num_rows > 0) {
                            while ($row = $approvedResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['judul_hak_cipta']); ?></td>
                                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                                    <td>
                                        <button type="button" onclick="openModal('<?= $row['id'] ?>')" class="btn btn-info">
                                            Detail Pencipta
                                        </button>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td><?php echo "-"; ?></td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <h3>Hak Cipta Yang Belum Disetujui</h3>
            <div class="table-wrapper">
                <table id="pendingTable" class="hki-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Pencipta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pendingQuery = "SELECT id, judul_hak_cipta, created_at 
                               FROM registrations 
                               WHERE user_id = ? AND (status = 'Pending' OR status = 'Rejected')
                               ORDER BY created_at DESC";
                        $stmtPending = $conn->prepare($pendingQuery);
                        $stmtPending->bind_param("s", $user_id);
                        $stmtPending->execute();
                        $pendingResult = $stmtPending->get_result();

                        if ($pendingResult->num_rows > 0) {
                            while ($row = $pendingResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['judul_hak_cipta']); ?></td>
                                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                                    <td>
                                        <button type="button" onclick="openModal('<?= $row['id'] ?>')" class="btn btn-info">
                                            Detail Pencipta
                                        </button>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td><?php echo "-"; ?></td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Detail Pencipta (tetap menggunakan implementasi dari user.js) -->
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