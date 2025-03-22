<?php
include 'config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Ambil user_id berdasarkan registration_id
    $query = $conn->query("SELECT user_id FROM registrations WHERE id = '$id'");
    $data = $query->fetch_assoc();
    if (!$data) {
        echo "Pendaftaran tidak ditemukan!";
        exit();
    }
    $user_id = $data['user_id'];

    // Direktori penyimpanan
    $upload_dir = "uploads/users/$user_id/files/$id/certificates/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES["new_certificate"]["name"])) {
        $file_name = "certificate_" . time() . "." . pathinfo($_FILES["new_certificate"]["name"], PATHINFO_EXTENSION);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["new_certificate"]["tmp_name"], $target_file)) {
            // Update path di database
            $conn->query("UPDATE registrations SET certificate_path='$target_file' WHERE id='$id'");
            echo "Sertifikat berhasil diperbarui.";
        } else {
            echo "Gagal mengunggah sertifikat.";
        }
    } else {
        echo "Harap unggah sertifikat.";
    }
}

header("Location: rekap_hki.php");
?>
