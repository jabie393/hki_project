<?php
date_default_timezone_set('Asia/Jakarta'); // Zona waktu

//=== BASE URL ===//
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    // Menghapus '/widgets' dari path (keperluan profile_helper.php)
    $basePath = str_replace('/widgets', '', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
    define('BASE_URL', $protocol . $host . $basePath);
}

//=== KONEKSI DATABASE ===//
$host = "localhost"; // Server
$user = "root"; // Username database
$pass = ""; // Password
$dbname = "hki_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->query("SET time_zone = '+07:00'"); // Zona waktu ke WIB (GMT+7)
?>
