<?php
date_default_timezone_set('Asia/Jakarta'); // Zona waktu

//=== KONEKSI DATABASE ===//
$host = "localhost"; // Server
$user = "root"; // Username database
$pass = ""; // Password
$dbname = "hak_cipta"; // Nama database

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->query("SET time_zone = '+07:00'"); // Zona waktu ke WIB (GMT+7)
?>
