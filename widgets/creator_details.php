<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID tidak valid.";
    exit();
}

$registration_id = intval($_GET['id']);
$query = "SELECT creators.* FROM creators WHERE registration_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $registration_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div id="table_modal-page">
    <div class="cdtable-wrapper">
        <table id="cdtable">
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>No. Telepon</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Negara</th>
                <th>Provinsi</th>
                <th>Kota/Kabupaten</th>
                <th>Kecamatan</th>
                <th>Kelurahan</th>
                <th>Kode Pos</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['nik']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['no_telepon']; ?></td>
                    <td><?php echo $row['jenis_kelamin']; ?></td>
                    <td><?php echo $row['alamat']; ?></td>
                    <td><?php echo $row['negara']; ?></td>
                    <td><?php echo $row['provinsi']; ?></td>
                    <td><?php echo $row['kota']; ?></td>
                    <td><?php echo $row['kecamatan']; ?></td>
                    <td><?php echo $row['kelurahan']; ?></td>
                    <td><?php echo $row['kode_pos']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>