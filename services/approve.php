<?php
include '../config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.html");
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
        $certificate_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES["certificate"]["tmp_name"], $certificate_path)) {
            echo "Gagal mengunggah sertifikat.";
            exit();
        }
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
?>
