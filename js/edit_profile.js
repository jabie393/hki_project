function refreshAllProfilePics() {
    const userId = document.querySelector('.profile-img').dataset.userId;
    const defaultAvatar = 'assets/image/default-avatar.png';
    const profilePath = `uploads/users/${userId}/profile/profile.jpg`;

    fetch(profilePath + '?v=' + Date.now(), { method: 'HEAD' })
        .then(response => {
            const newSrc = response.ok ? profilePath + '?v=' + Date.now() : defaultAvatar + '?v=' + Date.now();
            document.querySelectorAll('.profilePic').forEach(img => {
                img.src = newSrc;
            });
        })
        .catch(() => {
            // Jika gagal, tetap pakai default avatar dengan cache busting
            document.querySelectorAll('.profilePic').forEach(img => {
                img.src = defaultAvatar + '?v=' + Date.now();
            });
        });
}


function initEditProfilePage() {
    const form = document.getElementById('profileForm');
    if (!form) return;

    // ==== Handle Submit Form ====
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        Swal.fire({
            title: "Mengupdate Profil...",
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

                xhr.open("POST", "services/submit_profile.php", true);
                xhr.onload = function () {
                    const elapsedTime = Date.now() - startTime; // Hitung waktu yang telah berlalu
                    const delay = Math.max(1500 - elapsedTime, 0); // Hitung delay agar minimal 1,5 detik

                    setTimeout(() => {
                        Swal.close();

                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                refreshAllProfilePics(); // Update gambar profil di DOM

                                // Muat ulang konten dan update elemen baru
                                loadContent('edit_profile.php', () => {
                                    refreshAllProfilePics();
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    }, delay);
                };

                xhr.onerror = function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Terjadi kesalahan saat mengupdate profil.'
                    });
                };

                xhr.send(formData);
            }
        });
    });

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
}

// ==== Scripct menonaktifkan right click pada cropper ==== //
document.getElementById("modal-page").addEventListener("contextmenu", function (e) {
    e.preventDefault();
});

function setupProfilePictureInput() {
    const input = document.getElementById('profilePictureInput');
    const modal = document.getElementById('cropperModal');
    const modalPreviewImage = document.getElementById('modalPreviewImage');
    const croppedImageInput = document.getElementById('croppedImageInput');
    const previewCircle = document.getElementById('previewCroppedCircle');
    let cropper;

    if (input) {
        input.addEventListener('change', function (event) {
            const file = event.target.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes

            if (file) {
                // Validasi ukuran file
                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran Foto Terlalu Besar',
                        text: 'Ukuran foto melebihi batas maksimal 5MB. Silakan kompres atau pilih foto lain.',
                    });
                    input.value = ''; // Reset input file
                    return;
                }

                // Jika validasi berhasil, buka modal cropper
                const reader = new FileReader();
                reader.onload = function (e) {
                    modalPreviewImage.src = e.target.result;
                    modal.style.display = 'flex';

                    // Hancurkan instance cropper sebelumnya jika ada
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }

                    // Buat instance cropper baru
                    cropper = new Cropper(modalPreviewImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const confirmCropButton = document.getElementById('confirmCropButton');
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

    const closeModal = document.getElementById('closeCropperModal');
    if (closeModal) {
        closeModal.addEventListener('click', function () {
            modal.style.display = 'none';

            // Hancurkan instance cropper saat modal ditutup
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    setupProfilePictureInput();
});
