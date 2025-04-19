<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- Tailwind + AdminLTE + Boxicons -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  
  <!-- Custom -->
  <link rel="stylesheet" href="css/side&navbar.css" />
  <title>Dashboard</title>
</head>

<body>
  <?php include "widgets/sidebar.php"; ?>

  <section id="content">
    <?php include "widgets/navbar.php"; ?>

    <main id="content-main">
      <script>
        loadContent('admin.php') // Ganti dengan file yang sesuai
      </script>
    </main>
  </section>

  <script src="js/ajax.js"></script>
</body>

</html>
