// ================== FORM HKI SUBMISSION ==================
function initFormSubmission() {
    loadCountriesForMainForm();
    const form = document.querySelector("#form-hki");
    if (!form) return;

    // Cegah duplikasi event listener
    if (form.dataset.listenerAdded === "true") {
        return;
    }
    form.dataset.listenerAdded = "true";

    const submitButton = form.querySelector("button[type='submit']");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const penciptaDivs = document.querySelectorAll("#pencipta-list .pencipta");

        // Validasi: pastikan minimal 1 pencipta diisi
        if (penciptaDivs.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "Perhatian",
                text: "Minimal 1 pencipta harus ditambahkan sebelum mengirim form.",
                showConfirmButton: true,
                confirmButtonText: 'Oke, paham!'
            });
            return;
        }

        const hiddenInputsContainer = document.getElementById("pencipta-hidden-inputs");
        hiddenInputsContainer.innerHTML = "";

        // Daftar field yang harus ada
        const requiredFields = [
            'nik[]', 'nama[]', 'no_telepon[]', 'jenis_kelamin[]', 'alamat[]',
            'negara[]', 'provinsi[]', 'kota[]', 'kecamatan[]', 'kelurahan[]', 'kode_pos[]'
        ];

        penciptaDivs.forEach(div => {
            const data = JSON.parse(div.dataset.form);

            // Pastikan semua field ada
            requiredFields.forEach(field => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = field;

                // Ambil nilai dari data atau gunakan "-" jika tidak ada
                const fieldName = field.replace('[]', '');
                input.value = data[field] || "-";

                hiddenInputsContainer.appendChild(input);
            });
        });

        const formData = new FormData(form);

        // Tampilkan SweetAlert untuk progres
        Swal.fire({
            title: "Mengunggah Pengajuan...",
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

                // Perbarui progres upload
                xhr.upload.addEventListener("progress", function (e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        const progressBar = document.getElementById("progress-bar");
                        const progressText = document.getElementById("progress-text");

                        progressBar.style.width = `${percentComplete}%`;
                        progressText.textContent = `${percentComplete}%`;
                    }
                });

                // Kirim data form
                xhr.open("POST", "services/submit_pengajuan.php", true);
                xhr.onload = function () {
                    const elapsedTime = Date.now() - startTime; // Hitung waktu yang telah berlalu
                    const delay = Math.max(1500 - elapsedTime, 0); // Hitung delay agar minimal 1.5 detik

                    setTimeout(() => {
                        Swal.close(); // Tutup SweetAlert progres setelah delay

                        if (xhr.responseText.includes("berhasil")) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: xhr.responseText,
                                showConfirmButton: false,
                                timer: 2000 // Menunggu 2 detik
                            });

                            // load pengajuan_baru.php
                            if (typeof loadContent === "function") {
                                loadContent('pengajuan_baru.php');
                            }
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: xhr.responseText,
                                showConfirmButton: true,
                                confirmButtonText: 'Oke, paham!'
                            });
                        }
                    }, delay);
                };

                xhr.onerror = function () {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Terjadi kesalahan saat mengirim data.",
                        showConfirmButton: true,
                        confirmButtonText: 'Oke!'
                    });
                };

                xhr.onloadend = function () {
                    // Aktifkan kembali tombol submit setelah proses selesai
                    submitButton.disabled = false;
                };

                xhr.send(formData);
            }
        });

        // Nonaktifkan tombol submit
        submitButton.disabled = true;
    });
}

// ================== LOAD NEGARA ==================
function loadCountriesForMainForm() {
    // Konfigurasi API
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

            const mainFormSelect = document.querySelector("#nationality");
            if (!mainFormSelect) return;

            mainFormSelect.innerHTML = '<option value="">-- Pilih Negara --</option>';

            // Cari dan tandai Indonesia sebagai default
            let defaultCountrySet = false;
            data.forEach(country => {
                const option = document.createElement("option");
                option.value = country.name;
                option.textContent = country.name;
                option.setAttribute("data-flag", `https://flagcdn.com/w20/${country.iso2.toLowerCase()}.png`);

                // Set Indonesia sebagai selected
                if (country.name === 'Indonesia') {
                    option.selected = true;
                    defaultCountrySet = true;
                }

                mainFormSelect.appendChild(option);
            });

            // Inisialisasi Select2
            const $select = $(mainFormSelect);
            if ($select.hasClass("select2-hidden-accessible")) {
                $select.select2('destroy');
            }

            $select.select2({
                templateResult: formatCountryOption,
                templateSelection: formatCountryOption,
                placeholder: "-- Pilih Negara --",
                allowClear: true,
                width: '100%'
            });

            $('#nationality').on('select2:open', function () {
                document.querySelector('.select2-search__field').focus();
            });

            // Tambahkan event listener untuk perubahan negara
            $('#nationality').on('change', function () {
                toggleCityInput(this.value);
            });

            // Jika Indonesia adalah default, aktifkan dropdown kota
            if (defaultCountrySet) {
                toggleCityInput('Indonesia');
            }
        })
        .catch(error => {
            console.error("Gagal memuat data negara:", error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data Negara',
                text: 'Tidak dapat memuat daftar negara. Silakan coba lagi nanti.',
                showConfirmButton: true,
                confirmButtonText: 'Oke!'
            });
        });
}

// Fungsi untuk menampilkan bendera di dropdown
function formatCountryOption(option) {
    if (!option.id) {
        return option.text;
    }
    const flagUrl = $(option.element).data("flag");
    return $(
        `<span><img src="${flagUrl}" alt="" style="width: 20px; height: 15px; margin-right: 10px;">${option.text}</span>`
    );
}