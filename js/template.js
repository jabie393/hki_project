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