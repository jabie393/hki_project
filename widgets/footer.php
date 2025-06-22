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
];

// Jika sedang di about, menampilkan semua menu (tanpa TENTANG)
if ($currentPage === 'about') {
    $order = ['rekapitulasi', 'index', $dashboardPage];
    foreach ($order as $key) {
        echo "<a href=\"{$menus[$key]['href']}\">{$menus[$key]['label']}</a>";
    }
} else {
    // Menghilangkan menu sesuai halaman aktif
    $filtered = array_filter($menus, function($k) use ($currentPage) {
        return $k !== $currentPage;
    }, ARRAY_FILTER_USE_KEY);

    // Urutan link berdasarkan halaman aktif
    if ($currentPage === 'index') {
        $order = ['rekapitulasi', $dashboardPage];
    } elseif ($currentPage === 'rekapitulasi') {
        $order = [$dashboardPage, 'index'];
    } elseif ($currentPage === $dashboardPage) {
        $order = ['rekapitulasi', 'index'];
    } else {
        $order = array_keys($filtered);
    }

    // Menampilkan link sesuai urutan
    foreach ($order as $key) {
        echo "<a href=\"{$menus[$key]['href']}\">{$menus[$key]['label']}</a>";
    }
    // TENTANG selalu di kanan
    echo "<a href=\"about\">TENTANG</a>";
}
?>