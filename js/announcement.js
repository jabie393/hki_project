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

            Swal.fire({
                title: "Mengunggah Pengumuman...",
                html: `
                    <div id="progress-container">
                        <div id="progress-bar-container">
                            <div id="progress-bar"></div>
                        </div>
                        <p id="progress-text">0%</p>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    const xhr = new XMLHttpRequest();
                    const startTime = Date.now(); // Catat waktu mulai

                    xhr.upload.addEventListener("progress", function (e) {
                        if (e.lengthComputable) {
                            const percentComplete = Math.round((e.loaded / e.total) * 100);
                            const progressBar = document.getElementById("progress-bar");
                            const progressText = document.getElementById("progress-text");

                            progressBar.style.width = `${percentComplete}%`;
                            progressText.textContent = `${percentComplete}%`;
                        }
                    });

                    xhr.open("POST", "services/announcement_update.php", true);
                    xhr.onload = function () {
                        const elapsedTime = Date.now() - startTime; // Hitung waktu yang telah berlalu
                        const delay = Math.max(1500 - elapsedTime, 0); // Hitung delay agar minimal 1,5 detik

                        setTimeout(() => {
                            Swal.close();

                            const response = JSON.parse(xhr.responseText);
                            Swal.fire({
                                icon: response.success ? 'success' : 'error',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                if (response.success) {
                                    loadContent('announcement.php'); // refresh ulang
                                }
                            });
                        }, delay);
                    };

                    xhr.onerror = function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat mengunggah.',
                            showConfirmButton: true,
                            confirmButtonText: 'Oke!'
                        });
                    };

                    xhr.send(formData);
                }
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
                text: "Pengumuman ini tidak bisa dikembalikan!",
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
                                showConfirmButton: true,
                                confirmButtonText: 'Oke!'
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
