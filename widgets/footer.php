<?php
session_start();
$dashboardPage = isset($_SESSION['user_username']) ? 'dashboard' : 'login';
?>

<a href="rekapitulasi">REKAPITULASI</a>
<a href="index">BERANDA</a>
<a href="<?php echo $dashboardPage; ?>">PENGAJUAN HKI</a>