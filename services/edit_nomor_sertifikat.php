<?php
include '../config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    echo "Unauthorized!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['nomor_sertifikat'])) {
    $id = $_POST['id'];
    $nomor_sertifikat = trim($_POST['nomor_sertifikat']);

    // Pastikan Nomor Sertifikat tidak kosong
    if (empty($nomor_sertifikat)) {
        echo "Nomor Sertifikat tidak boleh kosong!";
        exit();
    }

    // Update database
    $stmt = $conn->prepare("UPDATE registrations SET nomor_sertifikat = ? WHERE id = ?");
    $stmt->bind_param("si", $nomor_sertifikat, $id);
    
    if ($stmt->execute()) {
        echo "Nomor Sertifikat berhasil diperbarui!";
    } else {
        echo "Gagal memperbarui Nomor Sertifikat!";
    }

    $stmt->close();
}

header("Location: ../rekap_hki.php");
exit();
?>
