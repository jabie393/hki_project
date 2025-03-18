<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $jenis_permohonan = isset($_POST['jenis_permohonan']) ? $_POST['jenis_permohonan'] : "";
    $jenis_hak_cipta = $_POST['jenis_hak_cipta'];
    $sub_jenis_hak_cipta = $_POST['sub_jenis_hak_cipta'];
    $tanggal_pengumuman = isset($_POST['tanggal_pengumuman']) ? $_POST['tanggal_pengumuman'] : "0000-00-00";
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $negara_pengumuman = isset($_POST['negara_pengumuman']) ? $_POST['negara_pengumuman'] : "";
    $kota_pengumuman = isset($_POST['kota_pengumuman']) ? $_POST['kota_pengumuman'] : "";
    $status = "Pending";
    
    // Ambil data pencipta dan ubah jadi format JSON
    $pencipta = json_encode($_POST['pencipta']);

    // Simpan ke tabel registrations
    $sql = "INSERT INTO registrations (user_id, jenis_permohonan, jenis_hak_cipta, sub_jenis_hak_cipta,  tanggal_pengumuman, judul_hak_cipta, deskripsi, negara_pengumuman, kota_pengumuman, nama_pencipta, status)
            VALUES ('$user_id', '$jenis_permohonan', '$jenis_hak_cipta', '$sub_jenis_hak_cipta', '$tanggal_pengumuman', '$judul', '$deskripsi', '$negara_pengumuman', '$kota_pengumuman', '$pencipta', '$status')";

    if ($conn->query($sql) === TRUE) {
        $reg_id = $conn->insert_id;

        // Simpan file
        $target_dir = "uploads/";
        $file_name = basename($_FILES["dokumen"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
            // Simpan ke tabel documents
            $conn->query("INSERT INTO documents (registration_id, file_name, file_path) VALUES ('$reg_id', '$file_name', '$target_file')");
            echo "Pendaftaran berhasil dikirim!";
        } else {
            echo "Gagal mengunggah dokumen.";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
