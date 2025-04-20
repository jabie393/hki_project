function loadContent(url) {
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memuat halaman: ' + url);
            }
            return response.text();
        })
        .then(data => {
            const container = document.getElementById("content-main");
            container.innerHTML = data;

            // Jalankan ulang semua <script> dalam konten yang dimuat
            const scripts = container.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");

                if (oldScript.src) {
                    newScript.src = oldScript.src;
                    newScript.async = false;
                } else {
                    newScript.textContent = oldScript.textContent;
                }

                document.body.appendChild(newScript);
                oldScript.remove();
            });

            // Re-attach search form event listener jika ada
            const searchForm = document.getElementById("search-form");
            if (searchForm) {
                searchForm.addEventListener("submit", function (e) {
                    e.preventDefault();
                    const formData = new FormData(searchForm);
                    const search = formData.get("search");

                    fetch("rekap_hki.php?search=" + encodeURIComponent(search))
                        .then(response => response.text())
                        .then(data => {
                            container.innerHTML = data;
                            // Panggil loadContent lagi agar form search bisa aktif ulang
                            loadContent("rekap_hki.php?search=" + encodeURIComponent(search));
                        });
                });
            }
        })
        .catch(error => {
            console.error('Ada masalah dengan memuat halaman:', error);
            document.getElementById("content-main").innerHTML = "<p>Gagal memuat konten.</p>";
        });
}

// Inisialisasi pertama saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const loadFile = params.get('load') || 'admin.php';
    loadContent(loadFile);
});
