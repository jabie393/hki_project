<?php
date_default_timezone_set('Asia/Jakarta'); // Atur zona waktu

$host = "localhost"; // Sesuaikan dengan server
$user = "root"; // Sesuaikan dengan username database
$pass = ""; // Kosongkan jika tidak ada password
$dbname = "hki_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->query("SET time_zone = '+07:00'"); // Atur zona waktu ke WIB (GMT+7)
?>
