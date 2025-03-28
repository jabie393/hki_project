<?php
include '../config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    echo "Unauthorized!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['nomor_permohonan'])) {
    $id = $_POST['id'];
    $nomor_permohonan = trim($_POST['nomor_permohonan']);

    // Pastikan Nomor Permohonan tidak kosong
    if (empty($nomor_permohonan)) {
        echo "Nomor Permohonan tidak boleh kosong!";
        exit();
    }

    // Update database
    $stmt = $conn->prepare("UPDATE registrations SET nomor_permohonan = ? WHERE id = ?");
    $stmt->bind_param("si", $nomor_permohonan, $id);
    
    if ($stmt->execute()) {
        echo "Nomor Permohonan berhasil diperbarui!";
    } else {
        echo "Gagal memperbarui Nomor Permohonan!";
    }

    $stmt->close();
}

header("Location: ../rekap_hki.php");
exit();
?>
