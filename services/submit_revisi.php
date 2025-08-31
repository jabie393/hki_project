<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Sesi tidak valid. Silakan login kembali.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $reg_id = $_POST['revisi_id'] ?? null;

    if (!$reg_id) {
        echo "ID pengajuan tidak ditemukan.";
        exit();
    }

    // Cek kepemilikan pengajuan
    $cek = $conn->prepare("SELECT id FROM registrations WHERE id = ? AND user_id = ?");
    $cek->bind_param("ii", $reg_id, $user_id);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows === 0) {
        echo "Pengajuan tidak ditemukan atau bukan milik Anda.";
        exit();
    }

    // Ambil data dari form
    $jenis_pengajuan = $_POST['jenis_pengajuan'] ?? "";
    $jenis_hak_cipta = $_POST['jenis_hak_cipta'];
    $sub_jenis_hak_cipta = $_POST['sub_jenis_hak_cipta'];
    $tanggal_pengumuman = $_POST['tanggal_pengumuman'] ?? NULL;
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $negara_pengumuman = $_POST['negara_pengumuman'] ?? "";
    $kota_pengumuman = $_POST['kota_pengumuman'] ?? "";
    $status = "Pending";

    // Update registrations
    $sql = $conn->prepare("UPDATE registrations SET
        jenis_pengajuan=?, jenis_hak_cipta=?, sub_jenis_hak_cipta=?, tanggal_pengumuman=?,
        judul_hak_cipta=?, deskripsi=?, negara_pengumuman=?, kota_pengumuman=?, status=?
        WHERE id=? AND user_id=?");
    $sql->bind_param("ssssssssssi", $jenis_pengajuan, $jenis_hak_cipta, $sub_jenis_hak_cipta, $tanggal_pengumuman, $judul, $deskripsi, $negara_pengumuman, $kota_pengumuman, $status, $reg_id, $user_id);

    // Validasi minimal 1 pencipta harus ada
    if (!isset($_POST["nama"]) || !is_array($_POST["nama"]) || count($_POST["nama"]) === 0) {
        echo "Gagal: Minimal 1 pencipta harus diisi.";
        exit;
    }

    if ($sql->execute()) {
        // Hapus pencipta lama
        $conn->query("DELETE FROM creators WHERE registration_id = $reg_id");

        // Simpan data pencipta baru
        $stmt_pencipta = $conn->prepare("INSERT INTO creators
            (registration_id, nik, nama, no_telepon, jenis_kelamin, alamat, negara, provinsi, kota, kecamatan, kelurahan, kode_pos)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($_POST['nama'] as $index => $nama) {
            $nik = $_POST['nik'][$index];
            $no_telepon = $_POST['no_telepon'][$index];
            $jenis_kelamin = $_POST['jenis_kelamin'][$index];
            $alamat = $_POST['alamat'][$index];
            $negara = $_POST['negara'][$index];
            $provinsi = $_POST['provinsi'][$index] ?? "-";
            $kota = $_POST['kota'][$index] ?? "-";
            $kecamatan = $_POST['kecamatan'][$index] ?? "-";
            $kelurahan = $_POST['kelurahan'][$index] ?? "-";
            $kode_pos = $_POST['kode_pos'][$index] ?? "-";

            $stmt_pencipta->bind_param("isssssssssss", $reg_id, $nik, $nama, $no_telepon, $jenis_kelamin, $alamat, $negara, $provinsi, $kota, $kecamatan, $kelurahan, $kode_pos);
            $stmt_pencipta->execute();
        }

        // Upload file jika ada file baru
        if (isset($_FILES["dokumen"]) && $_FILES["dokumen"]["size"] > 0) {
            // Ambil file_path dokumen lama
            $result = $conn->query("SELECT file_path FROM documents WHERE registration_id = $reg_id");
            while ($row = $result->fetch_assoc()) {
                $old_file = "../" . $row['file_path'];
                if (file_exists($old_file)) {
                    @unlink($old_file);
                }
            }

            // Hapus dokumen lama dari database
            $conn->query("DELETE FROM documents WHERE registration_id = $reg_id");

            // Buat folder upload
            $user_dir = "../uploads/users/$user_id/files/";
            $reg_dir = $user_dir . $reg_id . "/";

            if (!is_dir($reg_dir)) {
                mkdir($reg_dir, 0777, true);
            }

            $file_name = basename($_FILES["dokumen"]["name"]);
            $target_file = $reg_dir . $file_name;

            if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
                // Simpan ke tabel documents dengan path relatif (tanpa ../)
                $relative_file_path = "uploads/users/$user_id/files/$reg_id/$file_name";
                $stmt_doc = $conn->prepare("INSERT INTO documents (registration_id, file_name, file_path) VALUES (?, ?, ?)");
                $stmt_doc->bind_param("iss", $reg_id, $file_name, $relative_file_path);
                $stmt_doc->execute();
            } else {
                echo "Gagal mengunggah dokumen.";
                exit();
            }
        }

        echo "Revisi pengajuan berhasil disimpan!";
    } else {
        echo "Terjadi kesalahan saat menyimpan revisi.";
    }

    $sql->close();
    $stmt_pencipta->close();
    $conn->close();
} else {
    echo "Metode tidak valid.";
}
?>