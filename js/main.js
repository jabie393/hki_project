// main.js

/**
 * Refresh semua gambar profil (yang pakai class .profilePic) dengan versi terbaru.
 * Menambahkan query string ?v=timestamp agar cache browser dilewati.
 *
 * @param {string} newSrc - Path gambar profil yang baru (tanpa query string).
 */
// Fungsi global untuk update semua foto profil yang ada
function refreshAllProfilePictures(newSrc) {
    const allProfilePics = document.querySelectorAll('.profilePic');
    allProfilePics.forEach(img => {
        const cleanSrc = newSrc.split('?')[0]; // Hilangkan versi lama
        img.src = cleanSrc + '?v=' + Date.now(); // Tambahkan query untuk cache bust
    });
}
