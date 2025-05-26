<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Sesi tidak valid. Silakan login kembali.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];

    // Cek apakah profil user sudah lengkap
    $query = $conn->prepare("SELECT nama_lengkap, no_ktp, telephone, birth_date, gender, nationality, type_of_applicant FROM user_profile WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $profile = $result->fetch_assoc();

    if (!$profile || in_array(null, $profile, true) || in_array("", $profile, true)) {
        echo "Profil Anda belum lengkap. Silakan lengkapi profil sebelum mendaftar HKI.";
        exit();
    }

    // Ambil data dari form
    $jenis_permohonan = $_POST['jenis_permohonan'] ?? "";
    $jenis_hak_cipta = $_POST['jenis_hak_cipta'];
    $sub_jenis_hak_cipta = $_POST['sub_jenis_hak_cipta'];
    $tanggal_pengumuman = $_POST['tanggal_pengumuman'] ?? NULL;
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $negara_pengumuman = $_POST['negara_pengumuman'] ?? "";
    $kota_pengumuman = $_POST['kota_pengumuman'] ?? "";
    $status = "Pending";

    $sql = $conn->prepare("INSERT INTO registrations (user_id, jenis_permohonan, jenis_hak_cipta, sub_jenis_hak_cipta, tanggal_pengumuman, judul_hak_cipta, deskripsi, negara_pengumuman, kota_pengumuman, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $sql->bind_param("isssssssss", $user_id, $jenis_permohonan, $jenis_hak_cipta, $sub_jenis_hak_cipta, $tanggal_pengumuman, $judul, $deskripsi, $negara_pengumuman, $kota_pengumuman, $status);

    // Validasi minimal 1 pencipta harus ada
    if (!isset($_POST["nama"]) || !is_array($_POST["nama"]) || count($_POST["nama"]) === 0) {
        echo "Gagal: Minimal 1 pencipta harus diisi.";
        exit;
    }
    if ($sql->execute()) {
        $reg_id = $conn->insert_id;

        // Simpan data pencipta
        $stmt_pencipta = $conn->prepare("INSERT INTO creators 
            (registration_id, nik, nama, no_telepon, jenis_kelamin, alamat, negara, provinsi, kota, kecamatan, kelurahan, kode_pos) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

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

            // Berikan nilai default "-" jika field tidak ada
            $provinsi = isset($_POST['provinsi'][$index]) ? $_POST['provinsi'][$index] : "-";
            $kota = isset($_POST['kota'][$index]) ? $_POST['kota'][$index] : "-";
            $kecamatan = isset($_POST['kecamatan'][$index]) ? $_POST['kecamatan'][$index] : "-";
            $kelurahan = isset($_POST['kelurahan'][$index]) ? $_POST['kelurahan'][$index] : "-";
            $kode_pos = isset($_POST['kode_pos'][$index]) ? $_POST['kode_pos'][$index] : "-";

            $stmt_pencipta->bind_param("isssssssssss", $reg_id, $nik, $nama, $no_telepon, $jenis_kelamin, $alamat, $negara, $provinsi, $kota, $kecamatan, $kelurahan, $kode_pos);
            $stmt_pencipta->execute();
        }

        // Buat folder upload
        $user_dir = "../uploads/users/$user_id/files/";
        $reg_dir = $user_dir . $reg_id . "/";

        if (!is_dir($reg_dir)) {
            mkdir($reg_dir, 0777, true);
        }

        // Upload file
        $file_name = basename($_FILES["dokumen"]["name"]);
        $target_file = $reg_dir . $file_name;

        if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
            // Simpan ke tabel documents
            $stmt_doc = $conn->prepare("INSERT INTO documents (registration_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt_doc->bind_param("iss", $reg_id, $file_name, $target_file);
            $stmt_doc->execute();

            echo "Pendaftaran berhasil dikirim!";
        } else {
            echo "Gagal mengunggah dokumen.";
        }
    } else {
        echo "Terjadi kesalahan saat menyimpan data pendaftaran.";
    }

    $sql->close();
    $stmt_pencipta->close();
    $conn->close();
} else {
    echo "Metode tidak valid.";
}
?>