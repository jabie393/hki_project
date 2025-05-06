// header & footer
function loadPartial(selector, file) {
    fetch(file)
        .then(res => res.text())
        .then(html => {
            document.querySelector(selector).innerHTML = html;
        });
}

document.addEventListener("DOMContentLoaded", () => {
    loadPartial(".header", "widgets/header.php");
    loadPartial(".footer", "widgets/footer.php");
});

// modal
document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById('announcementModal');

    if (modal) {
        modal.style.display = "flex";
    }
});

function closeModal() {
    document.getElementById('announcementModal').style.display = 'none';
}

// Pagination (rekap_hki.php)
// Deteksi ukuran layar dan atur jumlah tombol pagination
document.addEventListener("DOMContentLoaded", function () {
    const screenWidth = window.innerWidth;
    let limit = 4; // Default untuk laptop

    if (screenWidth <= 768) { // Jika layar kecil (HP)
        limit = 7;
    }

    // Tambahkan parameter limit ke URL jika belum ada atau berbeda
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('limit') || urlParams.get('limit') != limit) {
        urlParams.set('limit', limit);
        window.location.search = urlParams.toString();
    }
});

