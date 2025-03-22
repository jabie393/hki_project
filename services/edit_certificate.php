<?php
include '../config/config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Ambil user_id dan sertifikat lama berdasarkan registration_id
    $query = $conn->query("SELECT user_id, certificate_path FROM registrations WHERE id = '$id'");
    $data = $query->fetch_assoc();
    if (!$data) {
        echo "Pendaftaran tidak ditemukan!";
        exit();
    }
    $user_id = $data['user_id'];
    $old_certificate = $data['certificate_path']; // Path sertifikat lama

    // Direktori penyimpanan
    $upload_dir = "../uploads/users/$user_id/files/$id/certificates/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES["new_certificate"]["name"])) {
        $file_name = "certificate_" . time() . "." . pathinfo($_FILES["new_certificate"]["name"], PATHINFO_EXTENSION);
        $target_file = $upload_dir . $file_name;

        // Hapus sertifikat lama jika ada
        if (!empty($old_certificate) && file_exists("../" . $old_certificate)) {
            unlink("../" . $old_certificate);
        }

        if (move_uploaded_file($_FILES["new_certificate"]["tmp_name"], $target_file)) {
            // Simpan path relatif tanpa '../' di database
            $db_file_path = "uploads/users/$user_id/files/$id/certificates/$file_name";
            $conn->query("UPDATE registrations SET certificate_path='$db_file_path' WHERE id='$id'");

            echo "Sertifikat berhasil diperbarui.";
        } else {
            echo "Gagal mengunggah sertifikat.";
        }
    } else {
        echo "Harap unggah sertifikat.";
    }
}

header("Location: ../rekap_hki.php");
exit();
?>
