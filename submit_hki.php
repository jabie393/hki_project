<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $jenis_permohonan = $_POST['jenis_permohonan'] ?? "";
    $jenis_hak_cipta = $_POST['jenis_hak_cipta'];
    $sub_jenis_hak_cipta = $_POST['sub_jenis_hak_cipta'];
    $tanggal_pengumuman = $_POST['tanggal_pengumuman'] ?? NULL;
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $negara_pengumuman = $_POST['negara_pengumuman'] ?? "";
    $kota_pengumuman = $_POST['kota_pengumuman'] ?? "";
    $status = "Pending";
    
    // Simpan ke tabel registrations
    $sql = "INSERT INTO registrations (user_id, jenis_permohonan, jenis_hak_cipta, sub_jenis_hak_cipta, tanggal_pengumuman, judul_hak_cipta, deskripsi, negara_pengumuman, kota_pengumuman, status)
            VALUES ('$user_id', '$jenis_permohonan', '$jenis_hak_cipta', '$sub_jenis_hak_cipta', '$tanggal_pengumuman', '$judul', '$deskripsi', '$negara_pengumuman', '$kota_pengumuman', '$status')";

    if ($conn->query($sql) === TRUE) {
        $reg_id = $conn->insert_id;

        // Simpan data pencipta
        foreach ($_POST['nama'] as $index => $nama) {
            $nik = $_POST['nik'][$index];
            $no_telepon = $_POST['no_telepon'][$index];
            $jenis_kelamin = $_POST['jenis_kelamin'][$index];
            $alamat = $_POST['alamat'][$index];
            $negara = $_POST['negara'][$index];
            $provinsi = $_POST['provinsi'][$index];
            $kota = $_POST['kota'][$index];
            $kecamatan = $_POST['kecamatan'][$index];
            $kelurahan = $_POST['kelurahan'][$index];
            $kode_pos = $_POST['kode_pos'][$index];

            $sql_pencipta = "INSERT INTO creators (registration_id, nik, nama, no_telepon, jenis_kelamin, alamat, negara, provinsi, kota, kecamatan, kelurahan, kode_pos) 
                            VALUES ('$reg_id', '$nik', '$nama', '$no_telepon', '$jenis_kelamin', '$alamat', '$negara', '$provinsi', '$kota', '$kecamatan', '$kelurahan', '$kode_pos')";

            $conn->query($sql_pencipta);
        }

        // **Buat folder berdasarkan user_id dan registration_id**
        $user_dir = "uploads/users/$user_id/files/";
        $reg_dir = $user_dir . $reg_id . "/";

        if (!is_dir($reg_dir)) {
            mkdir($reg_dir, 0777, true);
        }

        // Simpan file di folder pendaftaran
        $file_name = basename($_FILES["dokumen"]["name"]);
        $target_file = $reg_dir . $file_name;

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
