<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = isset($_SESSION['user_username']) ? $_SESSION['user_username'] : 'User';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
?>

<!-- SIDEBAR -->
<!-- TOGGLE BUTTON -->
<div id="sidebar-toggle" onclick="toggleSidebar()">
    <i class='bx bx-menu'></i>
</div>
<div id="sidebar-overlay" onclick="closeSidebar()"></div>
<section id="sidebar" class="sidebar">
    <button id="arrowToggle">&#8592;</button>
    <a href="" class="brand">
        <img src="assets/icon/unira.png" alt="Unira Icon" style="width: 30px; height: 30px;">
        <?php if ($role === 'admin'): ?>
            <span id="sidebar-username" class="text">Halo, <?= htmlspecialchars($username) ?>! <i
                    class="fas fa-check-circle" title="Admin"></i></span>
        <?php else: ?>
            <span id="sidebar-username" class="text">Halo, <?= htmlspecialchars($username) ?>!</span>
        <?php endif; ?>
    </a>

    <ul class="side-menu top p-0">
        <li class="sidebar-text" title="Profil">
            <a class="menu-link" onclick="loadContent('edit_profile.php')">
                <i class='bx bxs-user'></i><span class="text">Profil</span>
            </a>
        </li>

        <?php if ($role === 'admin'): ?>
            <li class="sidebar-text" title="Dashboard">
                <a class="menu-link" onclick="loadContent('admin.php')">
                    <i class='bx bxs-dashboard'></i><span class="text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-text" title="Tinjau Pengajuan">
                <a class="menu-link" onclick="loadContent('tinjau_pengajuan.php')">
                    <i class='bx bx-task'></i><span class="text">Tinjau Pengajuan</span>
                </a>
            </li>
            <li class="sidebar-text" title="Rekapitulasi">
                <a class="menu-link" onclick="loadContent('manage_rekapitulasi.php')">
                    <i class='bx bxs-folder'></i><span class="text">Rekapitulasi</span>
                </a>
            </li>
            <li class="sidebar-text" title="Pengajuan Ditolak">
                <a class="menu-link" onclick="loadContent('pengajuan_ditolak.php')">
                    <i class='bx bxs-x-circle'></i><span class="text">Pengajuan Ditolak</span>
                </a>
            </li>
            <li class="sidebar-text" title="Pengumuman">
                <a class="menu-link" onclick="loadContent('announcement.php')">
                    <i class='bx bxs-megaphone'></i><span class="text">Pengumuman</span>
                </a>
            </li>
            <li class="sidebar-text" title="Template Dokumen">
                <a class="menu-link" onclick="loadContent('template.php')">
                    <i class='bx bxs-file-doc'></i><span class="text">Template Dokumen</span>
                </a>
            </li>
            <li class="sidebar-text" title="Reset Password User">
                <a class="menu-link" onclick="loadContent('reset_password.php')">
                    <i class='bx bxs-key'></i><span class="text">Reset Password User</span>
                </a>
            </li>
        <?php else: ?>
            <li class="sidebar-text" title="Dashboard">
                <a class="menu-link" onclick="loadContent('user.php')">
                    <i class='bx bxs-dashboard'></i><span class="text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-text" title="Pengajuan Baru">
                <a class="menu-link" onclick="loadContent('pengajuan_baru.php')">
                    <i class='bx bxs-file-plus'></i><span class="text">Pengajuan Baru</span>
                </a>
            </li>
            <li class="sidebar-text" title="Status Pengajuan">
                <a class="menu-link" onclick="loadContent('status_pengajuan.php')">
                    <i class='bx bxs-folder'></i><span class="text">Status Pengajuan</span>
                </a>
            </li>
            <li class="sidebar-text" title="Revisi Hak Cipta" id="menu-revisi" style="display:none;">
                <a class="menu-link disabled" onclick="return false;" tabindex="-1">
                    <i class='bx bx-pencil'></i><span class="text">Revisi Hak Cipta</span>
                </a>
            </li>
            <li class="sidebar-text" title="Update Data Akun">
                <a class="menu-link" onclick="loadContent('update_account.php')">
                    <i class='bx bxs-cog'></i><span class="text">Update Data Akun</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <ul class="side-menu p-0">
        <li class="sidebar-text" title="Logout">
            <a href="services/logout.php" onclick="localStorage.removeItem('activePage')" class="logout">
                <i class='bx bxs-log-out-circle'></i><span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>

<script src="js/sidebar.js"></script>