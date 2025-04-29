function refreshAllProfilePics() {
    document.querySelectorAll('.profilePic').forEach(img => {
        const baseSrc = img.src.split('?')[0];
        img.src = `${baseSrc}?v=${Date.now()}`; // cache bust
    });
}

function initEditProfilePage() {
    const form = document.getElementById('profileForm');
    if (!form) return;

    // ==== Handle Submit Form ====
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('services/submit_profile.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Pertama, update yang sudah ada
                        refreshAllProfilePics();

                        // Muat ulang edit_profile.php (untuk dapetin ulang elemen <img>)
                        loadContent('edit_profile.php');

                        // Setelah konten selesai dimuat, baru update yang baru dimuat juga
                        setTimeout(() => {
                            refreshAllProfilePics(); // bekerja untuk <img> baru dari edit_profile.php
                        }, 300);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            })

            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Terjadi kesalahan saat mengirim data.'
                });
            });
    },);

    // ==== Load Negara ====
    const select = $("#nationality");
    if (select.length) {
        const selected = select.data("selected");

        fetch("https://restcountries.com/v3.1/all")
            .then(response => response.json())
            .then(data => {
                data.sort((a, b) => a.name.common.localeCompare(b.name.common));
                data.forEach(country => {
                    const option = new Option(country.name.common, country.name.common);
                    select.append(option);
                });

                // Destroy select2 dulu kalau sudah ada
                if (select.hasClass("select2-hidden-accessible")) {
                    select.select2('destroy');
                }

                select.select2({
                    placeholder: "-- Pilih Negara --",
                    allowClear: true,
                    width: '100%'
                });

                $('#nationality').on('select2:open', function () {
                    document.querySelector('.select2-search__field').focus();
                });

                if (selected) {
                    select.val(selected).trigger('change');
                }
            })
            .catch(error => console.error("Gagal memuat data negara:", error));
    }

    // ==== CropperJS Upload ====
    let cropper;
    const input = document.getElementById('profilePictureInput');
    const modal = document.getElementById('cropperModal');
    const closeModal = document.getElementById('closeCropperModal');
    const modalPreviewImage = document.getElementById('modalPreviewImage');
    const confirmCropButton = document.getElementById('confirmCropButton');
    const croppedImageInput = document.getElementById('croppedImageInput');
    const previewCircle = document.getElementById('previewCroppedCircle');

    if (input) {
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
    }

    if (confirmCropButton) {
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
    }

    if (closeModal) {
        closeModal.addEventListener('click', function () {
            modal.style.display = 'none';
            if (cropper) cropper.destroy();
        });
    }

    window.addEventListener('click', function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            if (cropper) cropper.destroy();
        }
    });
}
