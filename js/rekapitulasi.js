document.addEventListener("DOMContentLoaded", function () {
    //=== Deteksi ukuran layar dan atur jumlah tombol pagination ===//
    const screenWidth = window.innerWidth;
    let limit = 5; // Default untuk laptop

    if (screenWidth <= 768) { // Jika layar kecil (HP)
        limit = 10;
    }

    // Tambahkan parameter limit ke URL jika belum ada atau berbeda
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('limit') || urlParams.get('limit') != limit) {
        urlParams.set('limit', limit);
        window.location.search = urlParams.toString();
    }

    //=== Hapus semua query string ===//
    const url = new URL(window.location);
    url.search = '';
    window.history.replaceState({}, document.title, url.pathname);

    //=== Inisialisasi Chart dari admin.js saat DOM siap ===//
    initAdminPage();
});