<!-- Flow FE -->
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
    <title>Admin - Upload & Download Documents</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/template.css">
</head>

<body>

    <main>
        <h2>Upload Dokumen</h2>
        <form action="services/edit_template.php" method="post" enctype="multipart/form-data">
            <div class="custom-select-wrapper">
                <select name="doc_type" required>
                    <option value="surat_pernyataan">Surat Pernyataan</option>
                    <option value="surat_pengalihan_hak">Surat Pengalihan Hak</option>
                </select>
                <span class="custom-arrow">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 10l5 5 5-5" stroke="#4f46e5" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
            </div>

            <div class="custom-file-upload">
                <input type="file" name="file" id="fileInput" required>
                <div class="upload-box">
                    <span class="upload-icon">📁</span>
                    <span id="upload-text">Klik untuk memilih file</span>
                </div>
            </div>
            <span id="file-name">Belum ada file dipilih</span>



            <button type="submit">Upload</button>
        </form>

        <h2>Dokumen Tersedia</h2>
        <ul>
            <?php
            $docs = ['surat_pernyataan' => 'Surat Pernyataan', 'surat_pengalihan_hak' => 'Surat Pengalihan Hak'];
            foreach ($docs as $key => $label) {
                $result = $conn->query("SELECT * FROM template_documents WHERE doc_type='$key'");
                if ($row = $result->fetch_assoc()) {
                    echo "<li><strong>$label:</strong><br><span class='doc-actions'>";
                    echo "<a class='download-btn' href='" . $row['filepath'] . "' download>" . $row['filename'] . "</a>";
                    echo "<a class='delete-btn' href='services/edit_template.php?delete=$key' onclick='return confirm(\"Hapus dokumen ini?\")'>Hapus</a>";
                    echo "</span></li>";
                } else {
                    echo "<li><strong>$label:</strong> <em>Belum diunggah</em></li>";
                }
            }
            ?>
        </ul>

        <div class="nav-links">
            <a href="profile.php">Profil</a> |
            <a href="admin.php">Dashboard</a> |
            <a href="rekap_hki.php">Rekap HKI</a> |
            <a href="reset_password.php">Reset Password</a> |
            <a href="services/logout.php">Logout</a>
        </div>
    </main>
    <script src="js/template.js"></script>

</body>

</html>