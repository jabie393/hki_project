<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login");
  exit();
}

$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Hak Cipta UNIRA Malang</title>
  <link rel="shortcut icon" href="assets/icons/fcompany.png" type="image/x-icon">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- F Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Boxicons -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- Css -->
  <link rel="stylesheet" href="css/side&navbar.css" />
  <link rel="stylesheet" href="css/modal.css">
</head>

<input type="hidden" id="loggedInUserId" value="<?= $_SESSION['user_id'] ?>">
<body>
  <?php include "widgets/sidebar.php"; ?>

  <section id="content">
    <?php include "widgets/navbar.php"; ?>

    <main id="content-main">
      <script src="js/main.js"></script>
      <script>
        // Set halaman aktif ke dashboard sesuai role
        <?php if ($role === 'admin'): ?>
          localStorage.setItem('activePage', 'admin.php');
        <?php else: ?>
          localStorage.setItem('activePage', 'user.php');
        <?php endif; ?>
      </script>
    </main>
  </section>
  <!-- Modal Profil (Navbar) -->
  <div id="modal-page">
    <div id="modal-profile" class="modal modal-hidden">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Profil</h2>
          <button class="close" id="close-modal">&times;</button>
        </div>
        <div id="userProfileContent">
          <!-- Konten Ajax -->
        </div>
      </div>
    </div>
  </div>
</body>
<script src="js/ajax.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/darkmode.js"></script>

</html>