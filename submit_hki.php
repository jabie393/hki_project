<?php
include 'config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    
    // Simpan ke tabel registrations
    $sql = "INSERT INTO registrations (user_id, judul_hak_cipta, deskripsi) VALUES ('$user_id', '$judul', '$deskripsi')";
    if ($conn->query($sql) === TRUE) {
        $reg_id = $conn->insert_id;

        // Simpan file
        $target_dir = "uploads/";
        $file_name = basename($_FILES["dokumen"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file);

        // Simpan ke tabel documents
        $conn->query("INSERT INTO documents (registration_id, file_name, file_path) VALUES ('$reg_id', '$file_name', '$target_file')");

        echo "Pendaftaran berhasil dikirim!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
