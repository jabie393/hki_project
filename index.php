<?php
session_start();

// session role yang menunjukkan peran pengguna
$dashboardPage = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'admin.php' : 'dashboard.php';
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hak Kekayaan Intelektual</title>
    <link rel="stylesheet" href="/hki_project/css/style.css" />
    <style>
        body{
          background: url('/hki_project/assets/image/bg.png') no-repeat center center;
          font-family: 'Poppins', sans-serif;
          font-size: 25px;
          margin: 0;
          padding: 0;
          text-align: center;
          background-size: cover;
          background-attachment: fixed;
          height: 100vh;
          color: rgb(255, 255, 255);
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
        }
    </style>
  </head>
  <body>
    <div class="header">
      <img src="/hki_project/assets/image/logo.png" alt="Logo Unira" />
    </div>
    <h1>Hak Kekayaan Intelektual</h1>
    <div class="menu">
      <button onclick="location.href='rekapitulasi.php'">REKAPITULASI</button>
      <button onclick="location.href='<?php echo $dashboardPage; ?>'">PENGAJUAN HKI</button>
      <button onclick="location.href='petunjuk_pengajuan.html'">PETUNJUK PENGAJUAN HKI</button>
    </div>
    <div class="footer">
      <a href="rekapitulasi.php">REKAPITULASI</a>
      <a href="index.php">BERANDA</a>
      <a href="<?php echo $dashboardPage; ?>">PENGAJUAN HKI</a>
    </div>
  </body>
</html>
