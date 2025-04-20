// Tampilkan nama file saat dipilih
function setupFileInput() {
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('file-name');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            fileNameDisplay.textContent = fileInput.files.length > 0
                ? fileInput.files[0].name
                : 'Belum ada file dipilih';
        });
    }
}

// Upload via AJAX
function setupUploadForm() {
    const form = document.getElementById('announcementForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('services/announcement_update.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(response => {
                    Swal.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message,
                        showConfirmButton: false, // Tidak ada tombol confirm
                        timer: 2000 // Menunggu 2 detik
                    }).then(() => {
                        if (response.success) {
                            loadContent('announcement.php'); // refresh ulang
                        }
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mengunggah.',
                    });
                });
        });
    }
}

// Hapus via AJAX + konfirmasi SweetAlert
function setupDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const id = this.getAttribute('data-id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Gambar ini tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`services/announcement_update.php?delete=${id}`)
                        .then(res => res.json())
                        .then(response => {
                            Swal.fire({
                                icon: response.success ? 'success' : 'error',
                                title: response.message,
                                showConfirmButton: false, // Tidak ada tombol confirm
                                timer: 2000 // Menunggu 2 detik
                            }).then(() => {
                                if (response.success) {
                                    loadContent('announcement.php'); // refresh ulang
                                }
                            });
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menghapus.',
                            });
                        });
                }
            });
        });
    });
}

// Inisialisasi semua fitur setelah loadContent atau saat halaman pertama kali dimuat
function initAnnouncementPage() {
    setupFileInput();
    setupUploadForm();
    setupDeleteButtons();
}

// Jalankan saat halaman ini dimuat
initAnnouncementPage();
