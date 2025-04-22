<?php
include __DIR__ . '/../config/config.php';

$types = ['surat_pernyataan', 'surat_pengalihan_hak'];
foreach ($types as $type):
    $res = $conn->query("SELECT * FROM template_documents WHERE doc_type = '$type'");
    $row = $res->fetch_assoc();
    ?>
    <li data-doc-type="<?= $type; ?>">
        <?php $label = ucwords(str_replace('_', ' ', $type)); ?>
        <strong><?= $label; ?>:</strong><br>
        <?php if ($row): ?>
            <span class="doc-actions">
                <a class="download-btn" href="<?= $row['filepath']; ?>" download><?= $row['filename']; ?></a>
                <a class="delete-btn" href="#" onclick="deleteDocument('<?= $type; ?>'); return false;">Hapus</a>
            </span>
        <?php else: ?>
            <span class="no-file">📄 Dokumen belum diunggah.</span>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
