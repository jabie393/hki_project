// Panah Dropdown //
const selectElement = document.querySelector('.custom-select-wrapper select');
const customArrow = document.querySelector('.custom-arrow');
let isDropdownOpen = false;

// Event untuk menangani klik pada dropdown
selectElement.addEventListener('click', function () {
    // Jika dropdown terbuka, ubah panah ke atas, jika tidak ke bawah
    if (isDropdownOpen) {
        customArrow.style.transform = "translateY(-50%) rotate(0deg)"; // Panah menghadap ke bawah
    } else {
        customArrow.style.transform = "translateY(-50%) rotate(180deg)"; // Panah menghadap ke atas
    }

    // Toggle status dropdown (terbuka/tutup)
    isDropdownOpen = !isDropdownOpen;
});

// Event ketika opsi dipilih, memastikan panah menghadap ke bawah
selectElement.addEventListener('change', function () {
    customArrow.style.transform = "translateY(-50%) rotate(0deg)"; // Panah kembali ke bawah setelah memilih opsi
    isDropdownOpen = false; // Menandakan dropdown tertutup
});

// Event untuk memastikan panah menghadap ke bawah saat dropdown tertutup
selectElement.addEventListener('blur', function () {
    customArrow.style.transform = "translateY(-50%) rotate(0deg)"; // Panah menghadap ke bawah
    isDropdownOpen = false; // Menandakan dropdown tertutup
});

// Refresh Semua List //
function refreshDocumentList() {
    fetch('services/get_templates.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('document-list').innerHTML = html;
        });
}

// Fungsi untuk validasi file input
function validateFileInput(fileInput, allowedExtensions, fileNameDisplay) {
    const file = fileInput.files[0];

    if (file) {
        const fileExtension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(fileExtension)) {
            Swal.fire({
                icon: 'warning',
                title: 'Dokumen Tidak Valid',
                text: `Hanya dokumen dengan ekstensi berikut yang diizinkan: ${allowedExtensions.join(', ')}.`,
            });
            fileInput.value = ''; // Reset input file
            fileNameDisplay.textContent = 'Belum ada dokumen yang dipilih';
            return false;
        }

        fileNameDisplay.textContent = file.name;
        return true;
    } else {
        fileNameDisplay.textContent = 'Belum ada dokumen yang dipilih';
        return false;
    }
}

// Fungsi untuk menambahkan event listener ke tombol hapus
function setupDeleteButtons() {
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const docType = this.getAttribute('data-doc-type');
            deleteDocument(docType);
        });
    });
}

// Fungsi untuk menghapus dokumen
function deleteDocument(docType) {
    Swal.fire({
        title: "Yakin ingin menghapus dokumen ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('services/edit_template.php?delete=' + encodeURIComponent(docType))
                .then(res => res.json())
                .then(response => {
                    Swal.fire({
                        icon: response.status,
                        title: response.status === 'success' ? 'Berhasil' : 'Gagal',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        if (response.status === 'success') {
                            refreshDocumentList();
                        }
                    });
                })
                .catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
                });
        }
    });
}

// Fungsi untuk inisialisasi halaman template
function initTemplatePage() {
    refreshDocumentList();

    setupDeleteButtons();

    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('file-name');
    const allowedExtensions = ['pdf', 'doc', 'docx'];

    fileInput.addEventListener('change', function () {
        validateFileInput(fileInput, allowedExtensions, fileNameDisplay);
    });

    // Upload file via AJAX
    document.getElementById('uploadForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        Swal.fire({
            title: "Mengunggah Dokumen...",
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

                xhr.open("POST", "services/edit_template.php", true);
                xhr.onload = function () {
                    const elapsedTime = Date.now() - startTime; // Hitung waktu yang telah berlalu
                    const delay = Math.max(1500 - elapsedTime, 0); // Hitung delay agar minimal 1,5 detik

                    setTimeout(() => {
                        Swal.close();

                        const response = JSON.parse(xhr.responseText);
                        Swal.fire({
                            icon: response.status === 'success' ? 'success' : 'error',
                            title: response.status === 'success' ? 'Berhasil' : 'Gagal',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            if (response.status === 'success') {
                                refreshDocumentList();
                            }
                        });
                    }, delay);
                };

                xhr.onerror = function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mengunggah.',
                    });
                };

                xhr.send(formData);
            }
        });
    });
}
