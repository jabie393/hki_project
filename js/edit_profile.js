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
                        refreshAllProfilePics(); // update yang sudah di DOM saat ini

                        // Muat ulang konten dan setelah selesai, update juga profil baru yang muncul
                        loadContent('edit_profile.php', () => {
                            refreshAllProfilePics(); // update juga elemen baru dari halaman edit_profile.php
                        });
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
                const selects = document.querySelectorAll("#nationality, .negara-select");
                selects.forEach(select => {
                    select.innerHTML = '<option value="">-- Pilih Negara --</option>';
                    data.forEach(country => {
                        const option = document.createElement("option");
                        option.value = country.name.common;
                        option.textContent = country.name.common;
                        option.setAttribute("data-flag", country.flags.svg); // Tambahkan URL bendera
                        select.appendChild(option);
                    });

                    const $select = $(select);

                    // Destroy select2 dulu jika sudah ada
                    if ($select.hasClass("select2-hidden-accessible")) {
                        $select.select2('destroy');
                    }

                    // Inisialisasi Select2
                    $select.select2({
                        placeholder: "-- Pilih Negara --",
                        allowClear: true,
                        width: '100%',
                        templateResult: function (state) {
                            if (!state.id) return state.text;
                            const flagUrl = $(state.element).data("flag");
                            return $(
                                `<span><img src="${flagUrl}" style="width: 20px; height: 15px; margin-right: 5px;" /> ${state.text}</span>`
                            );
                        },
                        templateSelection: function (state) {
                            const flagUrl = $(state.element).data("flag");
                            return state.id
                                ? $(
                                    `<span><img src="${flagUrl}" style="width: 20px; height: 15px; margin-right: 5px;" /> ${state.text}</span>`
                                )
                                : state.text;
                        }
                    });
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
