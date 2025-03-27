<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$target_dir = "../uploads/announcement/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Proses Upload Gambar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $save_path = "uploads/announcement/" . basename($_FILES["image"]["name"]); // Path disimpan tanpa "../"

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO announcement (image_path) VALUES (?)");
        $stmt->bind_param("s", $save_path);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ../announcement.php");
    exit();
}

// Proses Hapus Gambar
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("SELECT image_path FROM announcement WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    if (file_exists("../" . $file_path)) { // Tambahkan "../" untuk akses path yang benar
        unlink("../" . $file_path);
    }

    $stmt = $conn->prepare("DELETE FROM announcement WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../announcement.php");
    exit();
}
?>
