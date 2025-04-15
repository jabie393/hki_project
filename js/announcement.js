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