<?php
include '../config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nomor_permohonan = isset($_POST['nomor_permohonan']) ? $_POST['nomor_permohonan'] : null;
    $nomor_sertifikat = isset($_POST['nomor_sertifikat']) ? $_POST['nomor_sertifikat'] : null;

    // Ambil user_id berdasarkan id registrasi
    $query = $conn->query("SELECT user_id FROM registrations WHERE id = '$id'");
    $data = $query->fetch_assoc();
    if (!$data) {
        echo "Pendaftaran tidak ditemukan!";
        exit();
    }
    $user_id = $data['user_id'];

    $certificate_path = null;

    // Jika ada file yang diunggah, proses penyimpanan sertifikat
    if (!empty($_FILES["certificate"]["name"])) {
        $upload_dir = "../uploads/users/$user_id/files/$id/certificates/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = "certificate_" . time() . "." . pathinfo($_FILES["certificate"]["name"], PATHINFO_EXTENSION);
        $full_path = $upload_dir . $file_name;
        $db_file_path = "uploads/users/$user_id/files/$id/certificates/$file_name"; // Path yang disimpan tanpa '../'

        if (!move_uploaded_file($_FILES["certificate"]["tmp_name"], $full_path)) {
            echo "Gagal mengunggah sertifikat.";
            exit();
        }

        $certificate_path = $db_file_path;
    }

    // Update status menjadi "Terdaftar" dan simpan path sertifikat, nomor permohonan, serta nomor sertifikat jika ada
    $update_query = "UPDATE registrations SET status='Terdaftar'";
    if ($certificate_path) {
        $update_query .= ", certificate_path='$certificate_path'";
    }
    if ($nomor_permohonan) {
        $update_query .= ", nomor_permohonan='$nomor_permohonan'";
    }
    if ($nomor_sertifikat) {
        $update_query .= ", nomor_sertifikat='$nomor_sertifikat'";
    }
    $update_query .= " WHERE id='$id'";

    $conn->query($update_query);

    echo "Pengajuan telah disetujui.";
}

header("Location: ../admin.php");
exit();
?>