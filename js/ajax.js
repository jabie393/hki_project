// Fungsi untuk memuat konten halaman secara dinamis
function loadContent(url, callback) {
    localStorage.setItem("activePage", url.split('?')[0]); // Simpan halaman aktif tanpa parameter query

    const noCacheUrl = url.includes("?") ? `${url}&v=${Date.now()}` : `${url}?v=${Date.now()}`;

    fetch(noCacheUrl)
        .then(response => {
            if (!response.ok) throw new Error('Gagal memuat halaman: ' + url);
            return response.text();
        })
        .then(data => {
            const container = document.getElementById("content-main");
            container.innerHTML = data;

            // Cari semua <script> di dalam konten baru
            const scripts = container.querySelectorAll("script");
            const scriptPromises = [];

            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                    newScript.async = false;
                    scriptPromises.push(new Promise(resolve => {
                        newScript.onload = resolve;
                        newScript.onerror = resolve;
                    }));
                    document.body.appendChild(newScript);
                } else {
                    newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                }
                oldScript.remove();
            });

            Promise.all(scriptPromises).then(() => {
                afterContentLoaded(url); // tetap kirim URL asli (tanpa ?v=...)

                if (typeof callback === 'function') {
                    callback(); // jalankan callback jika disediakan
                }
            });
        })
        .catch(error => {
            console.error('Ada masalah dengan memuat halaman:', error);
            document.getElementById("content-main").innerHTML = "<p>Gagal memuat konten.</p>";
        });
}

// Fungsi untuk memuat halaman dengan parameter pagination dan pencarian
function loadPage(page, limit, search, order) {
    // Ambil halaman aktif dari localStorage
    const activePage = localStorage.getItem("activePage") || "tinjau_pengajuan.php";

    // Bangun URL berdasarkan halaman aktif
    const url = `${activePage.split('?')[0]}?page=${page}&limit=${limit}&search=${encodeURIComponent(search)}&order=${order}`;

    // Muat konten menggunakan loadContent
    loadContent(url);
}

// Fungsi yang dipanggil setelah konten berhasil dimuat
function afterContentLoaded(url) {
    const cleanUrl = url.split('?')[0]; // Buang ?v=...

    highlightActiveMenu(cleanUrl);

    // Tampilkan menu revisi hanya jika revisi.php aktif
    const revisiMenu = document.getElementById('menu-revisi');
    if (revisiMenu) {
        if (cleanUrl.endsWith('revisi.php')) {
            revisiMenu.style.display = '';

            // Tambahkan event untuk menutup sidebar jika diklik di layar kecil
            const revisiLink = revisiMenu.querySelector('a');
            if (revisiLink) {
                revisiLink.onclick = function(e) {
                    // Jika di layar kecil, tutup sidebar
                    if (window.innerWidth < 768) {
                        document.getElementById('sidebar').classList.remove('show');
                        document.getElementById('sidebar-toggle').classList.remove('hidden');
                        document.getElementById('sidebar-overlay').classList.remove('show');
                    }
                    // Tetap return false agar tidak reload
                    return false;
                };
            }
        } else {
            revisiMenu.style.display = 'none';
        }
    }

    // Inisialisasi halaman spesifik
    if (cleanUrl.endsWith("admin.php")) {
        if (typeof initAdminPage === "function") initAdminPage();
    }

    if (cleanUrl.endsWith("manage_rekapitulasi.php")) {
        if (typeof initSelect2 === "function") initSelect2();
    }

    if (cleanUrl.endsWith("user.php")) {
        if (typeof pagination === "function") pagination();
    }

    if (cleanUrl.endsWith("pengajuan_baru.php")) {
        if (typeof initPengajuanBaru === "function") initPengajuanBaru();
        if (typeof setupFileValidation === "function") setupFileValidation();
        if (typeof initFormSubmission === "function") initFormSubmission();
        if (typeof initModalPencipta === "function") initModalPencipta();
    }

    if (cleanUrl.endsWith("revisi.php")) {
        if (typeof initPengajuanBaru === "function") initPengajuanBaru();
        if (typeof setupFileValidation === "function") setupFileValidation();
        if (typeof initReviseFormSubmission === "function") initReviseFormSubmission();
        if (typeof initModalPencipta === "function") initModalPencipta();
    }

    if (cleanUrl.endsWith("edit_profile.php")) {
        if (typeof initEditProfilePage === "function") initEditProfilePage();
        if (typeof setupProfilePictureInput === "function") setupProfilePictureInput();
    }

    if (cleanUrl.endsWith("template.php")) {
        if (typeof initTemplatePage === "function") initTemplatePage();
    }

    // Kalau ada select negara, load data negara
    if (document.querySelector("#nationality") || document.querySelector(".negara-select")) {
        if (typeof loadCountries === "function") {
            loadCountries();
        }
    }

    // Kalau ada form pencarian, pasang handler submit-nya
    const searchForm = document.getElementById("search-form");
    if (searchForm) {
        searchForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const search = formData.get("search");

            // Ambil halaman aktif dari localStorage
            const activePage = localStorage.getItem("activePage") || "tinjau_pengajuan.php";

            // Bangun URL pencarian berdasarkan halaman aktif
            const searchUrl = `${activePage.split('?')[0]}?search=${encodeURIComponent(search)}`;

            // Muat konten menggunakan loadContent
            loadContent(searchUrl);
        });
    }

    // Kalau di layar kecil, tutup sidebar otomatis
    if (window.innerWidth < 768) {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebar-toggle');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.remove('show');
        toggleButton.classList.remove('hidden');
        overlay.classList.remove('show');
    }
}

// Fungsi untuk menandai menu yang aktif di sidebar
function highlightActiveMenu(currentPage) {
    document.querySelectorAll('.menu-link').forEach(link => {
        link.classList.remove('active');

        const onclickAttr = link.getAttribute('onclick');
        if (onclickAttr) {
            const match = onclickAttr.match(/'([^']+)'/);
            if (match && match[1] === currentPage) {
                link.classList.add('active');
            }
        }
    });
}

// Inisialisasi saat halaman pertama kali diload
document.addEventListener("DOMContentLoaded", function () {
    const lastPage = localStorage.getItem("activePage") || 'dashboard.php'; // default kalau tidak ada
    loadContent(lastPage);
});
