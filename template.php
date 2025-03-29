<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload & Download Documents</title>
</head>

<body>
    <h2>Admin Upload</h2>
    <form action="services/edit_template.php" method="post" enctype="multipart/form-data">
        <select name="doc_type" required>
            <option value="surat_pernyataan">Surat Pernyataan</option>
            <option value="surat_pengalihan_hak">Surat Pengalihan Hak</option>
        </select>
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Available Documents</h2>
    <ul>
        <?php
        $docs = ['surat_pernyataan' => 'Surat Pernyataan', 'surat_pengalihan_hak' => 'Surat Pengalihan Hak'];
        foreach ($docs as $key => $label) {
            $result = $conn->query("SELECT * FROM template_documents WHERE doc_type='$key'");
            if ($row = $result->fetch_assoc()) {
                echo "<li>$label: <a href='" . $row['filepath'] . "' download>" . $row['filename'] . "</a> ";
                echo "<a href='services/edit_template.php?delete=$key' onclick='return confirm(\"Hapus dokumen ini?\")'>[Delete]</a></li>";
            } else {
                echo "<li>$label: Belum diunggah</li>";
            }
        }
        ?>
    </ul>
</body>

</html>