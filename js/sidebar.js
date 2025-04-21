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
