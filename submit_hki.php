<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $judul = $_POST['judul'];
    $jenis_hak_cipta = $_POST['jenis_hak_cipta'];
    $sub_jenis_hak_cipta = $_POST['sub_jenis_hak_cipta'];
    $deskripsi = $_POST['deskripsi'];
    $status = "Pending";
    
    // Ambil data pencipta dan ubah jadi format JSON
    $pencipta = json_encode($_POST['pencipta']);

    // Simpan ke tabel registrations
    $sql = "INSERT INTO registrations (user_id, judul_hak_cipta, jenis_hak_cipta, sub_jenis_hak_cipta, nama_pencipta, deskripsi, status) 
            VALUES ('$user_id', '$judul', '$jenis_hak_cipta', '$sub_jenis_hak_cipta', '$pencipta', '$deskripsi', '$status')";

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
