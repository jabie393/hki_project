function initUserPage() {
    loadCountriesForMainForm();
    document.getElementById('jenis_hak_cipta').addEventListener('change', function () {
        var jenis = this.value;
        var subJenis = document.getElementById('sub_jenis_hak_cipta');
        subJenis.innerHTML = '';

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
            subJenis.innerHTML = '<option value="">-- Pilih Sub Jenis --</option>' +
                options[jenis].map(option => `<option value="${option}">${option}</option>`).join('');
        }
    });

    // ================== JENIS & SUBJENIS ==================
    function initJenisHakCipta() {
        const jenisSelect = document.getElementById("jenis_hak_cipta");
        const subJenisSelect = document.getElementById("sub_jenis_hak_cipta");

        const options = { /* tetap sama seperti sebelumnya, tidak diubah */ };

        if (jenisSelect && subJenisSelect) {
            jenisSelect.addEventListener("change", function () {
                const jenis = this.value;
                subJenisSelect.innerHTML = "";
                if (options[jenis]) {
                    subJenisSelect.innerHTML = '<option value="">-- Pilih Sub Jenis --</option>' +
                        options[jenis].map(option => `<option value="${option}">${option}</option>`).join("");
                }
            });
        }
    }

    // ================== LOAD NEGARA ==================
    function loadCountriesForMainForm() {
        fetch("https://restcountries.com/v3.1/all")
            .then(response => response.json())
            .then(data => {
                data.sort((a, b) => a.name.common.localeCompare(b.name.common));

                const mainFormSelect = document.querySelector("#nationality");
                if (!mainFormSelect) return;

                mainFormSelect.innerHTML = '<option value="">-- Pilih Negara --</option>';
                data.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.name.common;
                    option.textContent = country.name.common;
                    option.setAttribute("data-flag", country.flags.svg); // Tambahkan URL bendera
                    mainFormSelect.appendChild(option);
                });

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
            })
            .catch(error => console.error("Gagal memuat data negara untuk form utama:", error));
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

        penciptaDivs.forEach(div => {
            const data = JSON.parse(div.dataset.form);
            for (const [key, value] of Object.entries(data)) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = key;
                input.value = value;
                hiddenInputsContainer.appendChild(input);
            }
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
                            $('#jenis_hak_cipta').val(null).trigger('change');
                            $('#sub_jenis_hak_cipta').val(null).trigger('change');
                            $('#nationality').val(null).trigger('change');

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

// Fungsi untuk memperbarui label pencipta dan tombol hapus
function updatePenciptaLabels() {
    let penciptaDivs = document.querySelectorAll(".pencipta");
    penciptaDivs.forEach((div, index) => {
        let label = div.querySelector(".pencipta-label");
        if (!label) {
            label = document.createElement("h4");
            label.classList.add("pencipta-label");
            div.insertBefore(label, div.firstChild);
        }
        label.textContent = "Pencipta " + (index + 1);
    });

    let deleteButtons = document.querySelectorAll(".hapusPencipta");
    if (penciptaDivs.length > 1) {
        deleteButtons.forEach(btn => btn.style.display = "inline-block");
    } else {
        deleteButtons.forEach(btn => btn.style.display = "none");
    }
}

// Hapus pencipta yang ada
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("hapusPencipta")) {
        let penciptaDiv = event.target.closest(".pencipta");
        penciptaDiv.remove();
        updatePenciptaLabels();
    }
});

// ================== MODAL PENCIPTA ==================
function initModalPencipta() {
    const modal = document.getElementById("modalPencipta");
    const modalForm = document.getElementById("modalFormPencipta");
    const penciptaList = document.getElementById("pencipta-list");
    let editingPencipta = null;

    // Pastikan listener hanya ditambahkan sekali
    if (!modalForm.dataset.listenerAdded) {
        modalForm.dataset.listenerAdded = true;

        modalForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(modalForm);
            const penciptaDiv = document.createElement("div");
            penciptaDiv.classList.add("pencipta");

            const nama = formData.get("nama[]") || "Tanpa Nama";

            penciptaDiv.innerHTML = `
                <h4 class="pencipta-label">Pencipta</h4>
                <strong>${nama}</strong><br>
                <button type="button" class="editPencipta">Edit</button>
                <button type="button" class="hapusPencipta">Hapus</button>
            `;

            penciptaDiv.dataset.form = JSON.stringify(Object.fromEntries(formData.entries()));

            penciptaDiv.querySelector(".editPencipta").onclick = () => openModalForEdit(penciptaDiv);
            penciptaDiv.querySelector(".hapusPencipta").onclick = () => {
                penciptaDiv.remove();
                updatePenciptaLabels();
            };

            if (editingPencipta) {
                // Jika sedang mengedit, ganti elemen yang ada
                editingPencipta.replaceWith(penciptaDiv);
                editingPencipta = null; // Reset variabel setelah proses edit selesai
            } else {
                // Jika tidak sedang mengedit, tambahkan pencipta baru
                penciptaList.appendChild(penciptaDiv);
            }

            modal.style.display = "none";
            modalForm.reset();
            updatePenciptaLabels();
        });
    }

    function openModalForEdit(div) {
        editingPencipta = div;
        const values = JSON.parse(div.dataset.form);
        for (const [key, value] of Object.entries(values)) {
            const input = modalForm.querySelector(`[name="${key}"]`);
            if (input) input.value = value;
        }

        const selectedCountry = values["negara[]"] || "";
        loadCountriesForModalForm(selectedCountry).then(() => {
            modal.style.display = "flex";
        });
    }

    function updatePenciptaLabels() {
        document.querySelectorAll(".pencipta-label").forEach((label, index) => {
            label.textContent = `Pencipta ${index + 1}`;
        });

        document.querySelectorAll(".hapusPencipta").forEach((btn, i, arr) => {
            btn.style.display = arr.length > 1 ? "inline-block" : "none";
        });
    }

    function loadCountriesForModalForm(selectedCountry = "") {
        return fetch("https://restcountries.com/v3.1/all")
            .then(response => response.json())
            .then(data => {
                data.sort((a, b) => a.name.common.localeCompare(b.name.common));

                const modalFormSelect = document.querySelector("#nationalityform");
                if (!modalFormSelect) return;

                modalFormSelect.innerHTML = '<option value="">-- Pilih Negara --</option>';
                data.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.name.common;
                    option.textContent = country.name.common;
                    option.setAttribute("data-flag", country.flags.svg); // Tambahkan URL bendera

                    // Tandai negara yang dipilih sebelumnya
                    if (country.name.common === selectedCountry) {
                        option.selected = true;
                    }

                    modalFormSelect.appendChild(option);
                });

                const $select = $(modalFormSelect);
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2('destroy');
                }

                $select.select2({
                    templateResult: formatCountryOption,
                    templateSelection: formatCountryOption,
                    placeholder: "-- Pilih Negara --",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(modalFormSelect).closest('.modal')
                });
                $('#nationalityform').on('select2:open', function () {
                    document.querySelector('.select2-search__field').focus();
                });
            })
            .catch(error => console.error("Gagal memuat data negara untuk form modal:", error));
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

    // Modal
    // ==== Modal Tambah Pencipta ====
    const addPenciptaBtn = document.getElementById("addPencipta");
    const closeModalBtn = document.querySelector(".close-modal");

    // Tampilkan modal
    addPenciptaBtn.addEventListener("click", function () {
        modal.style.display = "flex";
        modalForm.reset();
        editingPencipta = null; // Reset editingPencipta saat menambah pencipta baru
        loadCountriesForModalForm(); // Load negara untuk form modal
    });

    // Tutup modal
    closeModalBtn.addEventListener("click", function () {
        modal.style.display = "none";
        modalForm.reset();
        editingPencipta = null; // Reset editingPencipta saat modal ditutup
    });

    // Klik di luar konten modal = tutup
    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
            modalForm.reset();
            editingPencipta = null; // Reset editingPencipta saat modal ditutup
        }
    });

    // Cegah duplikasi event listener
    if (!window.formSubmitInitialized) {
        window.formSubmitInitialized = true;
        initFormSubmission();
        initModalPencipta();
    }
}

// Pastikan fungsi ini dipanggil saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
    initFormSubmission();
    setupFileValidation();
});

// ================== VALIDASI FILE ==================
function setupFileValidation() {
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('file-name');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0];
            const maxSize = 30 * 1024 * 1024; // 30MB in bytes

            if (file) {
                // Validasi ukuran file
                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
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
document.getElementById('fileInput').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || "Belum ada dokumen";
    document.getElementById('file-name').textContent = fileName;
});