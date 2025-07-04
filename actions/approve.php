<?php
include '../config/config.php';
session_start();

header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nomor_pengajuan = isset($_POST['nomor_pengajuan']) ? trim($_POST['nomor_pengajuan']) : null;
    $nomor_sertifikat = isset($_POST['nomor_sertifikat']) ? trim($_POST['nomor_sertifikat']) : null;

    // Cari user_id dan certificate_path dari registration
    $query = $conn->query("SELECT user_id, certificate_path FROM registrations WHERE id = '$id'");
    $data = $query->fetch_assoc();
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan hak cipta tidak ditemukan!']);
        exit();
    }
    $user_id = $data['user_id'];
    $old_certificate_path = $data['certificate_path'];

    // Set kolom rejection_path dan rejected_at menjadi NULL
    $conn->query("UPDATE registrations SET rejection_path=NULL, rejected_at=NULL WHERE id='$id'");

    // Hapus folder rejection_file jika ada
    $rejection_dir = "../uploads/users/$user_id/files/$id/rejection_file/";
    if (file_exists($rejection_dir) && is_dir($rejection_dir)) {
        // Hapus semua file di dalam folder
        $files = glob($rejection_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        // Hapus foldernya
        @rmdir($rejection_dir);
    }

    $certificate_path = null;

    if (!empty($_FILES["certificate"]["name"])) {
        $upload_dir = "../uploads/users/$user_id/files/$id/certificate/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Hapus file lama jika ada
        if (!empty($old_certificate_path)) {
            $old_file = "../" . $old_certificate_path;
            if (file_exists($old_file)) {
                @unlink($old_file);
            }
        }

        $file_name = "certificate_" . time() . "." . pathinfo($_FILES["certificate"]["name"], PATHINFO_EXTENSION);
        $full_path = $upload_dir . $file_name;
        $db_file_path = "uploads/users/$user_id/files/$id/certificate/$file_name";

        if (!move_uploaded_file($_FILES["certificate"]["tmp_name"], $full_path)) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengunggah sertifikat.']);
            exit();
        }

        $certificate_path = $db_file_path;
    }

    // Update status dan data
    $update_query = "UPDATE registrations SET status='Terdaftar'";
    if ($certificate_path) {
        $update_query .= ", certificate_path='$certificate_path'";
    }
    // Nomor pengajuan
    if ($nomor_pengajuan === "" || $nomor_pengajuan === null) {
        $update_query .= ", nomor_pengajuan=NULL";
    } else {
        $update_query .= ", nomor_pengajuan='" . $conn->real_escape_string($nomor_pengajuan) . "'";
    }
    // Nomor sertifikat
    if ($nomor_sertifikat === "" || $nomor_sertifikat === null) {
        $update_query .= ", nomor_sertifikat=NULL";
    } else {
        $update_query .= ", nomor_sertifikat='" . $conn->real_escape_string($nomor_sertifikat) . "'";
    }
    $update_query .= " WHERE id='$id'";

    if ($conn->query($update_query)) {
        echo json_encode(['success' => true, 'message' => 'Pengajuan hak cipta telah disetujui.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
}
?>