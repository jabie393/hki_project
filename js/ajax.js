function loadContent(url) {
    localStorage.setItem("activePage", url);

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

            // Eksekusi ulang semua script
            const scripts = container.querySelectorAll("script");
            const scriptPromises = [];

            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");

                if (oldScript.src) {
                    newScript.src = oldScript.src;
                    newScript.async = false;

                    // Promise untuk load eksternal script
                    const scriptPromise = new Promise(resolve => {
                        newScript.onload = resolve;
                        newScript.onerror = resolve;
                    });
                    scriptPromises.push(scriptPromise);

                    document.body.appendChild(newScript);
                } else {
                    newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                }

                oldScript.remove();
            });

            // Setelah semua script selesai dimuat
            Promise.all(scriptPromises).then(() => {
                afterContentLoaded(url);
            });
        })
        .catch(error => {
            console.error('Ada masalah dengan memuat halaman:', error);
            document.getElementById("content-main").innerHTML = "<p>Gagal memuat konten.</p>";
        });
}

function afterContentLoaded(url) {
    highlightActiveMenu(url);

    // Fungsi loadCountries jika ada elemen negara
    if (document.querySelector("#nationality") || document.querySelector(".negara-select")) {
        if (typeof loadCountries === "function") {
            loadCountries();
        }
    }

    // Handle form pencarian jika ada
    const searchForm = document.getElementById("search-form");
    if (searchForm) {
        searchForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const search = formData.get("search");

            fetch("rekap_hki.php?search=" + encodeURIComponent(search))
                .then(response => response.text())
                .then(data => {
                    document.getElementById("content-main").innerHTML = data;
                    loadContent("rekap_hki.php?search=" + encodeURIComponent(search));
                });
        });
    }

    // Tutup sidebar otomatis di layar kecil
    if (window.innerWidth < 768) {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebar-toggle');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.remove('show');
        toggleButton.classList.remove('hidden');
        overlay.classList.remove('show');
    }
}

// Highlight menu yang aktif
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

// Inisialisasi halaman saat pertama kali dimuat
document.addEventListener("DOMContentLoaded", function () {
    const lastPage = localStorage.getItem("activePage") || 'admin.php';
    loadContent(lastPage);
});
