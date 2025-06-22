<?php
session_start();
$dashboardPage = isset($_SESSION['user_username']) ? 'dashboard' : 'login';

// Mengambil nama halaman dari query string jika ada, jika tidak pakai PHP_SELF
$currentPage = isset($_GET['page']) ? $_GET['page'] : basename($_SERVER['PHP_SELF'], '.php');

// Daftar menu utama
$menus = [
    'index'        => ['label' => 'BERANDA', 'href' => 'index'],
    'rekapitulasi' => ['label' => 'REKAPITULASI', 'href' => 'rekapitulasi'],
    $dashboardPage => ['label' => 'PENGAJUAN', 'href' => $dashboardPage],
    'register'     => ['label' => 'PENGAJUAN', 'href' => 'register'], // Tambahan untuk register
];

// Jika sedang di about, menampilkan semua menu (tanpa TENTANG)
if ($currentPage === 'about') {
    $order = ['rekapitulasi', 'index', $dashboardPage];
    foreach ($order as $key) {
        echo "<a href=\"{$menus[$key]['href']}\">{$menus[$key]['label']}</a>";
    }
} else {
    // Untuk halaman register, gunakan urutan dan label seperti pengajuan
    $isRegister = ($currentPage === 'register');
    $activePage = $isRegister ? 'register' : $currentPage;

    // Menghilangkan menu sesuai halaman aktif
    $filtered = array_filter($menus, function($k) use ($activePage) {
        return $k !== $activePage;
    }, ARRAY_FILTER_USE_KEY);

    // Urutan link berdasarkan halaman aktif
    if ($activePage === 'index') {
        $order = ['rekapitulasi', $dashboardPage];
    } elseif ($activePage === 'rekapitulasi') {
        $order = [$dashboardPage, 'index'];
    } elseif ($activePage === $dashboardPage || $activePage === 'register') {
        $order = ['rekapitulasi', 'index'];
    } else {
        $order = array_keys($filtered);
    }

    // Menampilkan link sesuai urutan
    foreach ($order as $key) {
        // Untuk halaman register, label dan href sama seperti pengajuan
        if ($isRegister && $key === $dashboardPage) {
            echo "<a href=\"register\">PENGAJUAN</a>";
        } else {
            echo "<a href=\"{$menus[$key]['href']}\">{$menus[$key]['label']}</a>";
        }
    }
    // TENTANG selalu di kanan, kecuali di about
    echo "<a href=\"about\">TENTANG</a>";
}
?>