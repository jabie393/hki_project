<?php
include 'config/config.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

$id = $_GET['id'];
$conn->query("UPDATE registrations SET status='Terdaftar' WHERE id=$id");
header("Location: admin.php");
?>
