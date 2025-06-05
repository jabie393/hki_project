<?php
include '../config/config.php';
$id = intval($_GET['id']);
$query = $conn->query("SELECT * FROM registrations WHERE id = $id");
?>

<div id="detail_ciptaan-page">
    <div class="detail-container">
        <?php
        if ($row = $query->fetch_assoc()) {
        ?>
            <p class="detail-row">
                <span class="detail-label">Tanggal Pengajuan:</span>
                <span class="detail-value"><?= htmlspecialchars($row['created_at']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Judul:</span>
                <span class="detail-value"><?= htmlspecialchars($row['judul_hak_cipta']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Jenis Pengajuan:</span>
                <span class="detail-value"><?= htmlspecialchars($row['jenis_pengajuan']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Jenis Ciptaan:</span>
                <span class="detail-value"><?= htmlspecialchars($row['jenis_hak_cipta']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Sub Jenis Ciptaan:</span>
                <span class="detail-value"><?= htmlspecialchars($row['sub_jenis_hak_cipta']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Tanggal Pengumuman:</span>
                <span class="detail-value"><?= htmlspecialchars($row['tanggal_pengumuman']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Negara Pengumuman:</span>
                <span class="detail-value"><?= htmlspecialchars($row['negara_pengumuman']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Kota Pengumuman:</span>
                <span class="detail-value"><?= htmlspecialchars($row['kota_pengumuman']) ?></span>
            </p>
            <p class="detail-row">
                <span class="detail-label">Deskripsi:</span>
                <span class="detail-value"><div class="deskripsi-box"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></div></span>
            </p>
        <?php
        } else {
            echo "Data tidak ditemukan.";
        }
        ?>
    </div>
</div>
