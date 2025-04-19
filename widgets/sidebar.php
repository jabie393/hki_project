<?php

$page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
?>

<!-- SIDEBAR -->
<!-- TOGGLE BUTTON -->
<div id="sidebar-toggle" onclick="toggleSidebar()">
    <i class='bx bx-menu'></i>
</div>
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-smile'></i>
        <span class="text">AdminHub</span>
    </a>
    <ul class="side-menu top p-0">
        <li><a onclick="loadContent('profile.php')"><i class='bx bxs-user'></i><span class="text">Profil</span></a></li>
        <li><a onclick="loadContent('admin.php')"><i class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
        <li><a onclick="loadContent('rekap_hki.php')"><i class='bx bxs-folder'></i><span class="text">Rekap HKI</span></a></li>
        <li><a onclick="loadContent('announcement.php')"><i class='bx bxs-megaphone'></i><span class="text">Pengumuman</span></a></li>
        <li><a onclick="loadContent('template.php')"><i class='bx bxs-file-doc'></i><span class="text">Template Dokumen</span></a></li>
        <li><a onclick="loadContent('reset_password.php')"><i class='bx bxs-key'></i><span class="text">Reset Password User</span></a></li>
    </ul>
    <ul class="side-menu p-0">
        <li><a href="services/logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
    </ul>
</section>

<script src="js/ajax.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }
</script>

<!-- SIDEBAR -->