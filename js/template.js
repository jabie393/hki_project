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


// Pembaca File //
const fileInput = document.getElementById('fileInput');
const fileNameDisplay = document.getElementById('file-name');

fileInput.addEventListener('change', function () {
    if (fileInput.files.length > 0) {
        fileNameDisplay.textContent = fileInput.files[0].name;
    } else {
        fileNameDisplay.textContent = 'Belum ada file dipilih';
    }
});

// Upload file via AJAX
document.getElementById('fileInput').addEventListener('change', function () {
    const fileName = this.files[0]?.name || "Belum ada file dipilih";
    document.getElementById('file-name').textContent = fileName;
});

document.getElementById('uploadForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('services/edit_template.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(response => {
        Swal.fire({
            icon: response.status,
            title: response.status === 'success' ? 'Berhasil' : 'Gagal',
            text: response.message,
            showConfirmButton: false, // Tidak ada tombol confirm
            timer: 2000 // Menunggu 2 detik
        }).then(() => {
            if (response.status === 'success') {
                location.reload();
            }
        });
    })
    .catch(err => {
        Swal.fire('Error', 'Terjadi kesalahan saat upload.', 'error');
    });
});

function deleteDocument(docType) {
    Swal.fire({
        title: "Yakin ingin menghapus dokumen ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#aaa",
        confirmButtonText: "Hapus"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('services/edit_template.php?delete=' + encodeURIComponent(docType))
                .then(res => res.json())
                .then(response => {
                    Swal.fire({
                        icon: response.status,
                        title: response.status === 'success' ? 'Berhasil' : 'Gagal',
                        text: response.message,
                        showConfirmButton: false, // Tidak ada tombol confirm
                        timer: 2000 // Menunggu 2 detik
                    }).then(() => {
                        if (response.status === 'success') {
                            location.reload();
                        }
                    });
                })
                .catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
                });
        }
    });
}
