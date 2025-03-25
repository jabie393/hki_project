<?php
include '../config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

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

    // Update status menjadi "Terdaftar" dan simpan path sertifikat jika ada
    if ($certificate_path) {
        $conn->query("UPDATE registrations SET status='Terdaftar', certificate_path='$certificate_path' WHERE id='$id'");
    } else {
        $conn->query("UPDATE registrations SET status='Terdaftar' WHERE id='$id'");
    }

    echo "Pengajuan telah disetujui.";
}

header("Location: ../admin.php");
exit();
?>