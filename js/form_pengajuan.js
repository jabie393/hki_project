// ================= MEMANGGIL FUNGSI SAAT HALAMAN DIMUAT ===================
document.addEventListener("DOMContentLoaded", function () {
    // ================= MEMBUKA DATEPICKER ===================
    const dateInput = document.getElementById('tanggal_pengumuman');
    if (dateInput) {
        dateInput.addEventListener('click', function (e) {
            if (e.isTrusted && this.showPicker) {
                this.showPicker();
            }
        });
    }
});

function initPengajuanBaru() {

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
                    width: '100%',
                    allowClear: true,
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

    // Ekspor fungsi ke konteks global
    window.toggleCityInput = toggleCityInput;

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
                try {
                    const res = await fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${prov.id}.json`);
                    if (!res.ok) return []; // Jika 404, kembalikan array kosong
                    const cities = await res.json();
                    cities.forEach(city => city.provinsi = prov.nama);
                    return cities;
                } catch (err) {
                    return []; // Jika error, kembalikan array kosong
                }
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

    // ================== INITIALIZE SELECT2 ==================
    // Inisialisasi Select2 untuk jenis pengajuan jika belum ada
    if (!$('#jenis_pengajuan').hasClass("select2-hidden-accessible")) {
        $('#jenis_pengajuan').select2({
            placeholder: "-- Pilih Jenis Pengajuan --",
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
}

// ================== VALIDASI FILE ==================
function setupFileValidation() {
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('file-name');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0];
            const maxSize = 30 * 1024 * 1024; // 30MB in bytes
            const allowedExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];

            if (file) {
                const fileExtension = file.name.split('.').pop().toLowerCase();

                // Validasi ekstensi file
                if (!allowedExtensions.includes(fileExtension)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Dokumen Tidak Valid',
                        text: `Hanya Dokumen dengan ekstensi berikut yang diizinkan: ${allowedExtensions.join(', ')}.`,
                        showConfirmButton: true,
                        confirmButtonText: 'Oke, paham!'
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
                        showConfirmButton: true,
                        confirmButtonText: 'Oke, paham!'
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