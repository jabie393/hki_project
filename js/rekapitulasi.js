// Pagination (rekap_hki.php)
// Deteksi ukuran layar dan atur jumlah tombol pagination
document.addEventListener("DOMContentLoaded", function () {
    const screenWidth = window.innerWidth;
    let limit = 5; // Default untuk laptop

    if (screenWidth <= 768) { // Jika layar kecil (HP)
        limit = 8;
    }

    // Tambahkan parameter limit ke URL jika belum ada atau berbeda
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('limit') || urlParams.get('limit') != limit) {
        urlParams.set('limit', limit);
        window.location.search = urlParams.toString();
    }
});

// Hapus semua query string
document.addEventListener("DOMContentLoaded", function () {
    const url = new URL(window.location);
    url.search = '';
    window.history.replaceState({}, document.title, url.pathname);
});