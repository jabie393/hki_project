<?php
session_start();
session_destroy(); // Hapus semua sesi pengguna
header("Location: ../index");
exit();
?>