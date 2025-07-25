function refreshAllProfilePics() {
    const userId = document.querySelector('.profileimg').dataset.userId;
    const defaultAvatar = 'assets/images/default-avatar.png';
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
    loadCountriesForProfile();

    // ================= MEMBUKA DATEPICKER =================== //
    const dateInput = document.getElementById('birth_date');
    if (dateInput) {
        dateInput.addEventListener('click', function (e) {
            if (e.isTrusted && this.showPicker) {
                this.showPicker();
            }
        });
    }

    // ================== INITIALIZE SELECT2 ================== //
    $('#gender').select2({
        placeholder: "-- Pilih Jenis Kelamin --",
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: Infinity,
    });
    $('#type_of_applicant').select2({
        placeholder: "-- Pilih Tipe Pengaju --",
        allowClear: true,
        width: '100%'
    });
    // auto focus pada input pencarian Select2
    $('.auto-search').on('select2:open', function () {
        document.querySelector('.select2-search__field').focus();
    });

    const form = document.getElementById('profileForm');
    if (!form) return;

    // ==== Handle Submit Form ==== //
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Normalisasi nomor telepon: hapus 0 di depan jika ada
        let telephone = $('#telephone').val().trim();
        if (telephone.startsWith("0")) {
            telephone = telephone.slice(1);
            $('#telephone').val(telephone);
        }

        let consultation_number = $('#consultation_number').val() ? $('#consultation_number').val().trim() : '';
        if (consultation_number.startsWith("0")) {
            consultation_number = consultation_number.slice(1);
            $('#consultation_number').val(consultation_number);
        }

        const formData = new FormData(form);
        // memastikan yang dikirim sudah tanpa 0 di depan
        formData.set('telephone', telephone);
        formData.set('consultation_number', consultation_number);

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
                const startTime = Date.now();

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
                    const elapsedTime = Date.now() - startTime;
                    const delay = Math.max(1500 - elapsedTime, 0);

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
                                refreshAllProfilePics();
                                loadContent('edit_profile.php', () => {
                                    refreshAllProfilePics();
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonText: 'Oke!'
                            });
                        }
                    }, delay);
                };

                xhr.onerror = function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Terjadi kesalahan saat mengupdate profil.',
                        showConfirmButton: true,
                        confirmButtonText: 'Oke!'
                    });
                };

                xhr.send(formData);
            }
        });
    });

    // ================== INISIALISASI SELECT2 KODE TELEPON NEGARA ================== //
    fetch("json/country_codes.json")
        .then(res => res.json())
        .then(data => {
            const options = data.map(c => {
                const phone = Array.isArray(c.phoneCode) ? c.phoneCode[0] : c.phoneCode;
                const isoCode = (c.countryCode || "").split(" / ")[0];
                const flagURL = `https://flagcdn.com/w40/${isoCode.toLowerCase()}.png`;

                return {
                    id: `+${phone}`,
                    text: `${isoCode.toUpperCase()} (+${phone})`,
                    img: flagURL,
                    iso: isoCode.toUpperCase()
                };
            });

            $('#phone_code, #consult_phone_code').select2({
                data: options,
                templateResult: formatOption,
                templateSelection: formatOption,
                escapeMarkup: m => m,
                placeholder: "Pilih negara",
                minimumResultsForSearch: 5
            });
            $('#phone_code, #consult_phone_code').next('.select2-container').addClass('select2-code');

            // Set value SESUAI data-selected
            const defaultCode = $('#phone_code').data('selected') || '+62';
            $('#phone_code').val(defaultCode).trigger('change');
            const defaultCodeConsult = $('#consult_phone_code').data('selected') || '+62';
            $('#consult_phone_code').val(defaultCodeConsult).trigger('change');
        });

    function formatOption(opt) {
        if (!opt.id) return opt.text;
        return `<span><img src="${opt.img}" style="width: 20px; height: 15px; margin-right: 5px;" /> ${opt.text}</span>`;
    }

    // ================== Load Negara ================== //
    function loadCountriesForProfile() {
        const select = $("#nationality_profile");
        if (!select.length) return;

        const selected = select.data("selected");

        // Konfigurasi API CSC
        const configCSC = {
            cUrl: 'https://api.countrystatecity.in/v1/countries',
            ckey: 'dW04ZkIxZGM5UDNYVWdzZWQ5N0JGMHpsRk5NR0dKNnVOelVDQloxaQ=='
        };

        fetch(configCSC.cUrl, {
            headers: {
                "X-CSCAPI-KEY": configCSC.ckey
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                data.sort((a, b) => a.name.localeCompare(b.name));

                const selects = document.querySelectorAll("#nationality_profile, .negara-select");
                selects.forEach(select => {
                    select.innerHTML = '<option value="">-- Pilih Negara --</option>';

                    data.forEach(country => {
                        const option = document.createElement("option");
                        option.value = country.name;
                        option.textContent = country.name;
                        option.setAttribute("data-flag", `https://flagcdn.com/w20/${country.iso2.toLowerCase()}.png`);
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

                $('#nationality_profile').on('select2:open', function () {
                    document.querySelector('.select2-search__field').focus();
                });

                if (selected) {
                    select.val(selected).trigger('change');
                }
            })
            .catch(error => {
                console.error("Gagal memuat data negara:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data Negara',
                    text: 'Tidak dapat memuat daftar negara. Silakan coba lagi nanti.',
                    showConfirmButton: true,
                    confirmButtonText: 'Oke, paham!'
                });
            });
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
                        icon: 'warning',
                        title: 'Ukuran Foto Terlalu Besar',
                        text: 'Ukuran foto melebihi batas maksimal 5MB. Silakan kompres atau pilih foto lain.',
                        showConfirmButton: true,
                        confirmButtonText: 'Oke, paham!'
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