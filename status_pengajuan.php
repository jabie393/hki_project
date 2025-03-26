<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Gunakan prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT * FROM registrations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengajuan HKI</title>
    <link rel="stylesheet" href="css/pengajuan.css">
    <link rel="stylesheet" href="css/modal.css">
</head>
<body>

<h2>Status Pengajuan</h2>
<table>
    <tr>
        <th>Nomor Permohonan</th>
        <th>Jenis Permohonan</th>
        <th>Jenis Ciptaan</th>
        <th>Sub Jenis Ciptaan</th>
        <th>Tanggal Pengumuman</th>
        <th>Judul</th>
        <th>Negara Pengumuman</th>
        <th>Kota Pengumuman</th>
        <th>Pencipta</th>
        <th>Status</th>
        <th>Sertifikat</th>
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
            <td><?php echo htmlspecialchars($row['negara_pengumuman']); ?></td>
            <td><?php echo htmlspecialchars($row['kota_pengumuman']); ?></td>
            <td>
                <button class="btn btn-info" onclick="showCreator(<?php echo $row['id']; ?>)">Detail Pencipta</button>
            </td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <?php if (!empty($row['certificate_path'])) { ?>
                    <a href="<?= $row['certificate_path'] ?>" class="btn btn-download" download>Download</a>
                <?php } else { ?>
                    <span>Belum tersedia</span>
                <?php } ?>
            </td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
                    <a href="services/cancel_hki.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin membatalkan?')">Batalkan</a>
                <?php } else { ?>
                    <span style="color: gray;">Tidak bisa dibatalkan</span>
                <?php } ?>
            </td>
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
        fetch(`creator_details.php?id=${id}`)
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
