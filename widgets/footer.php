<?php
session_start();
$dashboardPage = isset($_SESSION['user_username']) ? 'dashboard.php' : 'login.php';
?>

  <a href="rekapitulasi.php">REKAPITULASI</a>
  <a href="index.php">BERANDA</a>
  <a href="<?php echo $dashboardPage; ?>">PENGAJUAN HKI</a>

