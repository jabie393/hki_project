<?php

$page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
?>

<!-- SIDEBAR -->
<!-- TOGGLE BUTTON -->
<div id="sidebar-toggle" onclick="toggleSidebar()">
    <i class='bx bx-menu'></i>
</div>
<div id="sidebar-overlay" onclick="closeSidebar()"></div>
<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-smile'></i>
        <span class="text">AdminHub</span>
    </a>
    <ul class="side-menu top p-0">
        <li class="sidebar-text"><a class="menu-link" onclick="loadContent('profile.php')"><i
                    class='bx bxs-user'></i><span class="text">Profil</span></a></li>
        <li class="sidebar-text"><a class="menu-link" onclick="loadContent('admin.php')"><i
                    class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
        <li class="sidebar-text"><a class="menu-link" onclick="loadContent('rekap_hki.php')"><i
                    class='bx bxs-folder'></i><span class="text">Rekap HKI</span></a></li>
        <li class="sidebar-text"><a class="menu-link" onclick="loadContent('announcement.php')"><i
                    class='bx bxs-megaphone'></i><span class="text">Pengumuman</span></a></li>
        <li class="sidebar-text"><a class="menu-link" onclick="loadContent('template.php')"><i
                    class='bx bxs-file-doc'></i><span class="text">Template Dokumen</span></a></li>
        <li class="sidebar-text"><a class="menu-link" onclick="loadContent('reset_password.php')"><i
                    class='bx bxs-key'></i><span class="text">Reset Password User</span></a></li>
    </ul>
    <ul class="side-menu p-0">
        <li class="sidebar-text"><a href="services/logout.php" onclick="localStorage.removeItem('activePage')" class="logout"><i class='bx bxs-log-out-circle'></i><span
                    class="text">Logout</span></a></li>
    </ul>
</section>

<script src="js/sidebar.js"></script>