<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Boxicons -->
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

  <!-- Css -->
  <link rel="stylesheet" href="css/side&navbar.css" />
  <title>Dashboard</title>
</head>

<body>
  <?php include "widgets/sidebar.php"; ?>

  <section id="content">
    <?php include "widgets/navbar.php"; ?>

    <main id="content-main">
      <script src="js/ajax.js"></script>
      <script>
        // Menentukan halaman default berdasarkan role
        <?php if ($role === 'admin'): ?>
          loadContent('admin.php');
        <?php else: ?>
          loadContent('user.php');
        <?php endif; ?>
      </script>
    </main>
  </section>
</body>
</html>
