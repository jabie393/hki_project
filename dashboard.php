<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM registrations WHERE user_id = '$user_id'");

?>

<h2>Form Pendaftaran HKI</h2>
<form action="submit_hki.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="judul" placeholder="Judul Hak Cipta" required />
    <textarea name="deskripsi" placeholder="Deskripsi"></textarea>
    <input type="file" name="dokumen" required />
    <button type="submit">Kirim</button>
</form>

<h2>Status Pendaftaran</h2>
<table border="1">
    <tr>
        <th>Judul</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['judul_hak_cipta']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
                    <a href="cancel_hki.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin membatalkan?')">Batalkan</a>
                <?php } else { ?>
                    Tidak bisa dibatalkan
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>
<div>
    <a href="logout.php">Logout</a>
</div>
