//=== ARROW SIDEBAR ===//
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("arrowToggle");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const navbar = document.querySelector('nav');
    const texts = document.querySelectorAll('.side-menu .text, .brand .text');

    toggleBtn.addEventListener("click", function () {
        const isCollapsed = sidebar.classList.contains('collapsed-sidebar');

        if (isCollapsed) {
            // Sidebar sedang diperluas
            sidebar.classList.remove('collapsed-sidebar'); // Mulai membuka sidebar
            content.classList.remove('expanded-content'); // Konten kembali ke posisi awal

            setTimeout(() => {
                texts.forEach(text => {
                    text.style.display = 'inline'; // Tampilkan teks
                    setTimeout(() => {
                        text.style.opacity = '1';
                        text.style.transform = 'translateX(0)';
                    }, 50); // Mulai transisi teks setelah sidebar selesai
                });
            }, 300); // Tunggu hingga sidebar selesai membuka
        } else {
            // Sidebar sedang dikecilkan
            texts.forEach(text => {
                text.style.opacity = '0';
                text.style.transform = 'translateX(-10px)';
            });

            setTimeout(() => {
                texts.forEach(text => {
                    text.style.display = 'none'; // Sembunyikan teks setelah transisi selesai
                });

                sidebar.classList.add('collapsed-sidebar'); // Mulai menutup sidebar
                content.classList.add('expanded-content'); // Konten meluas bersamaan dengan sidebar
            }, 300); // Tunggu hingga teks selesai menghilang
        }

        navbar.classList.toggle('expanded-navbar');
        setTimeout(() => {
            toggleBtn.innerHTML = sidebar.classList.contains("collapsed-sidebar") ? "&#8594;" : "&#8592;";
        }, 300); // Tunggu hingga transisi selesai
    });
});

//=== BURGER SIDEBAR ===//
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('sidebar-toggle');
    const overlay = document.getElementById('sidebar-overlay');

    sidebar.classList.toggle('show');

    if (sidebar.classList.contains('show')) {
        toggleButton.classList.add('hidden');
        if (window.innerWidth < 768) {
            overlay.classList.add('show');
        }
    } else {
        toggleButton.classList.remove('hidden');
        overlay.classList.remove('show');
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('sidebar-toggle');
    const overlay = document.getElementById('sidebar-overlay');

    sidebar.classList.remove('show');
    toggleButton.classList.remove('hidden');
    overlay.classList.remove('show');
}

// Menambahkan event listener untuk klik di luar sidebar
document.addEventListener('click', function (event) {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('sidebar-toggle');
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar.contains(event.target) && !toggleButton.contains(event.target) && !overlay.contains(event.target)) {
        closeSidebar();
    }
});
