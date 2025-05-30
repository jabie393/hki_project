// ================= MEMANGGIL FUNGSI SAAT HALAMAN DIMUAT ===================
document.addEventListener("DOMContentLoaded", function () {
    initFormSubmission();
    setupFileValidation();
    initUserPage();

    // ================= MEMBUKA DATEPICKER ===================
    const dateInput = document.getElementById('tanggal_pengumuman');
    if (dateInput) {
        dateInput.addEventListener('click', function (e) {
            if (e.isTrusted && this.showPicker) {
                this.showPicker();
            }
        });
    }
    // Menangani perubahan form saat submit
    document.getElementById('form-hki').addEventListener('submit', function (e) {
        // Pastikan nilai yang benar dikirim
        const country = document.getElementById('nationality').value;
        const citySelect = document.getElementById('kota_pengumuman');
        const cityInput = document.getElementById('kota_pengumuman_input');

        if (country === 'Indonesia') {
            // Nonaktifkan input jika dropdown yang aktif
            cityInput.disabled = true;
            cityInput.removeAttribute('name'); // Hapus name attribute dari input
            citySelect.disabled = false;
            citySelect.setAttribute('name', 'kota_pengumuman'); // Set name untuk select
        } else {
            // Nonaktifkan select jika input yang aktif
            citySelect.disabled = true;
            citySelect.removeAttribute('name'); // Hapus name attribute dari select
            cityInput.disabled = false;
            cityInput.setAttribute('name', 'kota_pengumuman'); // Set name untuk input
        }
    });
});

function initUserPage() {
    loadCountriesForMainForm();

    // ================== INITIALIZE SELECT2 ==================
    // Inisialisasi Select2 untuk jenis permohonan jika belum ada
    if (!$('#jenis_permohonan').hasClass("select2-hidden-accessible")) {
        $('#jenis_permohonan').select2({
            placeholder: "-- Pilih Jenis Permohonan --",
            allowClear: true,
            width: '100%'
        });
    }

    // Inisialisasi Select2 untuk jenis hak cipta jika belum ada
    if (!$('#jenis_hak_cipta').hasClass("select2-hidden-accessible")) {
        $('#jenis_hak_cipta').select2({
            placeholder: "-- Pilih Jenis --",
            allowClear: true,
            width: '100%'
        });
    }

    // Inisialisasi Select2 untuk sub jenis hak cipta jika belum ada
    if (!$('#sub_jenis_hak_cipta').hasClass("select2-hidden-accessible")) {
        $('#sub_jenis_hak_cipta').select2({
            placeholder: "-- Pilih Sub Jenis --",
            allowClear: true,
            width: '100%',
            disabled: true
        });
    }

    // auto focus pada input pencarian Select2
    $('.auto-search').on('select2:open', function () {
        document.querySelector('.select2-search__field').focus();
    });


    // Event listener untuk perubahan jenis hak cipta
    $('#jenis_hak_cipta').on('change', function () {
        var jenis = this.value;

        // Reset dan disable dulu
        var $subJenis = $('#sub_jenis_hak_cipta');
        $subJenis.empty().val(null).trigger('change');
        $subJenis.prop('disabled', !jenis); // Disable jika jenis kosong

        if (jenis) {
            var options = {
                "Karya Tulis": [
                    "Karya Tulis Lainnya", "Naskah Drama / Pertunjukan", "Naskah Karya Siaran",
                    "Buku", "Karya Tulis", "Terjemahan", "Tafsir", "Saduran", "Bunga Rampai",
                    "Perwajahan Karya Tulis", "Naskah Film", "Karya Tulis (Artikel)",
                    "Karya Tulis (Skripsi)", "Karya Tulis (Disertasi)", "Karya Tulis (Tesis)",
                    "Naskah Karya Sinematografi", "Modul", "Novel", "e-Book", "Cerita Bergambar",
                    "Komik", "Buku Panduan / Petunjuk", "Atlas", "Buku Saku", "Buku Pelajaran",
                    "Diktat", "Buku Mewarnai", "Dongeng", "Majalah", "Kamus", "Ensiklopedia",
                    "Biografi", "Booklet", "Jurnal", "Makalah", "Proposal Penelitian",
                    "Resensi", "Resume / Ringkasan", "Sinopsis", "Karya Ilmiah", "Laporan Penelitian", "Puisi"
                ],
                "Karya Seni": [
                    "Seni Gambar", "Alat Peraga", "Pamflet", "Seni Rupa", "Seni Lukis",
                    "Seni Patung", "Seni Pahat", "Kaligrafi", "Seni Motif", "Arsitektur",
                    "Peta", "Brosur", "Seni Terapan", "Karya Seni Rupa", "Ukiran", "Kolase",
                    "Seni Ilustrasi", "Sketsa", "Diorama", "Karya Seni Batik", "Seni Songket",
                    "Motif Tenun Ikat", "Motif Tapis", "Motif Ulos", "Motif Sasirangan",
                    "Seni Motif Lainnya", "Flyer", "Leaflet", "Poster", "Banner", "Spanduk",
                    "Baliho", "Seni Umum"
                ],
                "Komposisi Musik": [
                    "Musik Karawitan", "Lagu (Musik Dengan Teks)", "Musik Tanpa Teks", "Aransemen", "Musik",
                    "Musik Klasik", "Musik Jazz", "Musik Gospel", "Musik Blues", "Musik Rhythm and Blues",
                    "Musik Funk", "Musik Rock", "Musik Metal", "Musik Elektronik", "Musik Ska, Reggae, Dub",
                    "Musik Hip Hop, Rap, Rapcore", "Musik Pop", "Musik Latin", "Musik Country", "Musk Dangdut"
                ],
                "Karya Audio Visual": [
                    "Film", "Karya Rekaman Video", "Kuliah", "Karya Siaran Media Televisi dan Film", "Karya Siaran Media Radio", "Karya Siaran Video",
                    "Karya Sinematografi", "Film Dokumenter", "Film Iklan", "Film Kartun", "Reportase", "Film Cerita", "Karya Siaran"
                ],
                "Karya Fotografi": [
                    "Karya Fotografi", "Potret"
                ],
                "Karya Drama & Koreografi": [
                    "Drama / Pertunjukan", "Pewayangan", "Pantomim", "Koreografi", "Seni Pertunjukan", "Tari (Sendra Tari)", "Drama Musikal",
                    "Ludruk", "Lenong", "Ketoprak", "Komedi / Lawak", "Seni Akrobat", "Opera", "Pentas Musik", "Sulap", "Sirkus"
                ],
                "Karya Rekaman": [
                    "Ceramah", "Pidato", "Karya Rekaman Suara atau Bunyi", "Khutbah"
                ],
                "Karya Lainnya": [
                    "Basis Data", "Kompilasi Ciptaan / Data", "Permainan Video", "Program Komputer"
                ]
            };

            if (options[jenis]) {
                // Tambahkan opsi default
                $subJenis.append(new Option("-- Pilih Sub Jenis --", "", true, true));

                // Tambahkan semua opsi
                options[jenis].forEach(option => {
                    $subJenis.append(new Option(option, option));
                });

                // Enable select dan trigger change
                $subJenis.prop('disabled', false).trigger('change');
            }
        }
    });

    // ================== LOAD NEGARA ==================
    function loadCountriesForMainForm() {
        // Konfigurasi API
        const configCSC = {
            cUrl: 'https://api.countrystatecity.in/v1/countries',
            ckey: 'NHhvOEcyWk50N2Vna3VFTE00bFp3MjFKR0ZEOUhkZlg4RTk1MlJlaA=='
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
                    text: 'Tidak dapat memuat daftar negara. Silakan coba lagi nanti.'
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

    // Fungsi untuk toggle antara dropdown dan input text
    function toggleCityInput(country) {
        const citySelect = document.getElementById('kota_pengumuman');
        const cityInput = document.getElementById('kota_pengumuman_input');

        if (country === 'Indonesia') {
            // Tampilkan dropdown dan sembunyikan input
            citySelect.style.display = 'block';
            citySelect.required = true;
            citySelect.disabled = false;
            citySelect.setAttribute('name', 'kota_pengumuman');

            cityInput.style.display = 'none';
            cityInput.required = false;
            cityInput.disabled = true;
            cityInput.removeAttribute('name');

            // Inisialisasi Select2 untuk dropdown kota
            if (!$(citySelect).hasClass("select2-hidden-accessible")) {
                $(citySelect).select2({
                    placeholder: "-- Pilih Kota/Kabupaten --",
                    width: '100%'
                });
            }

            // Load data kota dari API Ibnux
            loadIndonesianCities();
        } else {
            // Tampilkan input dan sembunyikan dropdown
            citySelect.style.display = 'none';
            citySelect.required = false;
            citySelect.disabled = true;
            citySelect.removeAttribute('name');

            cityInput.style.display = 'block';
            cityInput.required = true;
            cityInput.disabled = false;
            cityInput.setAttribute('name', 'kota_pengumuman');

            // Hancurkan Select2 jika sudah diinisialisasi
            if ($(citySelect).hasClass("select2-hidden-accessible")) {
                $(citySelect).select2('destroy');
            }
        }
    }

    // Fungsi untuk memuat data kota Indonesia dari API Ibnux
    async function loadIndonesianCities() {
        const $citySelect = $('#kota_pengumuman');
        $citySelect.html('<option value="">-- Memuat data kota... --</option>');

        try {
            // Step 1: Fetch semua provinsi
            const provResponse = await fetch('https://ibnux.github.io/data-indonesia/propinsi.json');
            const provinsi = await provResponse.json();

            // Step 2: Fetch semua kabupaten/kota (paralel)
            const allCitiesPromises = provinsi.map(async (prov) => {
                const res = await fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${prov.id}.json`);
                const cities = await res.json();
                cities.forEach(city => city.provinsi = prov.nama);
                return cities;
            });

            const allCitiesArrays = await Promise.all(allCitiesPromises);
            const allCities = allCitiesArrays.flat();

            // Step 3: Isi dropdown
            $citySelect.html('<option value="">-- Pilih Kota/Kabupaten --</option>');
            allCities.forEach(city => {
                const option = new Option(`${city.nama} (${city.provinsi})`, city.nama, false, false);
                $citySelect.append(option);
            });

        } catch (error) {
            console.error('Gagal memuat data kota:', error);
            $citySelect.html('<option value="">-- Gagal memuat data kota --</option>');
        }
    }
}

// ================== FORM HKI SUBMISSION ==================
function initFormSubmission() {
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
                text: "Minimal 1 pencipta harus ditambahkan sebelum mengirim form."
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
                xhr.open("POST", "services/submit_hki.php", true);
                xhr.onload = function () {
                    const elapsedTime = Date.now() - startTime; // Hitung waktu yang telah berlalu
                    const delay = Math.max(1500 - elapsedTime, 0); // Hitung delay agar minimal 1.5 detik

                    setTimeout(() => {
                        Swal.close(); // Tutup SweetAlert progres setelah delay

                        Swal.fire({
                            icon: xhr.responseText.includes("berhasil") ? "success" : "error",
                            title: xhr.responseText.includes("berhasil") ? "Berhasil!" : "Gagal!",
                            text: xhr.responseText,
                            showConfirmButton: false, // Tidak ada tombol confirm
                            timer: 2000 // Menunggu 2 detik
                        });

                        if (xhr.responseText.includes("berhasil")) {
                            // Reset semua input dalam form
                            form.reset();

                            // Kosongkan daftar pencipta
                            document.getElementById("pencipta-list").innerHTML = "";

                            // Reset elemen Select2
                            $('#jenis_permohonan').val(null).trigger('change');
                            $('#jenis_hak_cipta').val(null).trigger('change');
                            $('#sub_jenis_hak_cipta').val(null).prop('disabled', true).trigger('change');

                            // Reset negara
                            $('#nationality').val('Indonesia').trigger('change');
                            toggleCityInput('Indonesia');

                            // Kosongkan semua input Select2 lainnya (jika ada)
                            $('.select2-hidden-accessible').val(null).trigger('change');

                            // Reset tampilan nama file
                            document.getElementById('file-name').textContent = "Belum ada dokumen";
                        }
                    }, delay);
                };

                xhr.onerror = function () {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Terjadi kesalahan saat mengirim data."
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

// ================== VALIDASI FILE ==================
function setupFileValidation() {
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('file-name');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0];
            const maxSize = 30 * 1024 * 1024; // 30MB in bytes
            const allowedExtensions = ['pdf', 'doc', 'docx', 'zip', 'rar', '7z', 'tar', 'gz'];

            if (file) {
                const fileExtension = file.name.split('.').pop().toLowerCase();

                // Validasi ekstensi file
                if (!allowedExtensions.includes(fileExtension)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Dokumen Tidak Valid',
                        text: `Hanya Dokumen dengan ekstensi berikut yang diizinkan: ${allowedExtensions.join(', ')}.`,
                    }).then(() => {
                        // Reset input file dan tampilan nama file
                        fileInput.value = ''; // Reset input file
                        fileNameDisplay.textContent = 'Belum ada dokumen'; // Reset tampilan nama file
                    });
                    return;
                }

                // Validasi ukuran file
                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Dokumen Terlalu Besar',
                        text: 'Ukuran dokumen melebihi batas maksimal 30MB. Silakan kompres atau pilih dokumen lain.',
                    }).then(() => {
                        // Reset input file dan tampilan nama file
                        fileInput.value = ''; // Reset input file
                        fileNameDisplay.textContent = 'Belum ada dokumen'; // Reset tampilan nama file
                    });
                } else {
                    // Jika validasi berhasil, perbarui tampilan nama file
                    fileNameDisplay.textContent = file.name;
                }
            } else {
                // Jika tidak ada file yang dipilih, reset tampilan nama file
                fileNameDisplay.textContent = 'Belum ada dokumen';
            }
        });
    }
}
document.getElementById('fileInput').addEventListener('change', function (e) {
    const fileName = e.target.files[0]?.name || "Belum ada dokumen";
    document.getElementById('file-name').textContent = fileName;
});