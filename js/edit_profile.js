// Load negara
fetch('https://restcountries.com/v3.1/all')
    .then(response => response.json())
    .then(data => {
        const select = document.querySelector("select[name='nationality']");
        data.sort((a, b) => a.name.common.localeCompare(b.name.common));
        data.forEach(country => {
            const option = document.createElement("option");
            option.value = country.name.common;
            option.textContent = country.name.common;
            select.appendChild(option);
        });

        const currentValue = "<?= $profile['nationality'] ?? '' ?>";
        if (currentValue) select.value = currentValue;
    })
    .catch(error => console.error("Gagal memuat data negara:", error));

// Cropper
document.addEventListener("DOMContentLoaded", function () {
    let cropper;
    const input = document.getElementById('profilePictureInput');
    const modal = document.getElementById('cropperModal');
    const closeModal = document.getElementById('closeCropperModal');
    const modalPreviewImage = document.getElementById('modalPreviewImage');
    const confirmCropButton = document.getElementById('confirmCropButton');
    const croppedImageInput = document.getElementById('croppedImageInput');
    const previewCircle = document.getElementById('previewCroppedCircle');

    input.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                modalPreviewImage.src = e.target.result;
                modal.style.display = 'block';

                if (cropper) cropper.destroy();
                cropper = new Cropper(modalPreviewImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1
                });
            };
            reader.readAsDataURL(file);
        }
    });

    confirmCropButton.addEventListener('click', function () {
        if (!cropper) return alert("Cropper belum siap!");

        const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
        if (canvas) {
            const croppedData = canvas.toDataURL('image/jpeg');
            croppedImageInput.value = croppedData;
            previewCircle.src = croppedData;
            previewCircle.style.display = 'block';
            modal.style.display = 'none';
            cropper.destroy();
        }
    });

    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
        if (cropper) cropper.destroy();
    });

    window.addEventListener('click', function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            if (cropper) cropper.destroy();
        }
    });
});