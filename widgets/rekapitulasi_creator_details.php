<?php
include '../config/config.php';
session_start();

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
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Negara</th>

        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['jenis_kelamin']; ?></td>
                <td><?php echo $row['negara']; ?></td>
            </tr>
        <?php } ?>
    </table>
    </div>
</div>
