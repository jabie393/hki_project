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

    // ================= INIT =================
    document.addEventListener("DOMContentLoaded", function () {
        initJenisHakCipta();
        initFormSubmission();
    });
}

// ================== FORM HKI SUBMISSION ==================
function initFormSubmission() {
    const form = document.querySelector("#form-hki");
    if (!form) return;

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

        fetch("services/submit_hki.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.text())
            .then(result => {
                Swal.fire({
                    icon: result.includes("berhasil") ? "success" : "error",
                    title: result.includes("berhasil") ? "Berhasil!" : "Gagal!",
                    text: result,
                    timer: 2000,
                    showConfirmButton: false
                });

                if (result.includes("berhasil")) {
                    form.reset(); // Reset semua input dalam form
                    document.getElementById("pencipta-list").innerHTML = ""; // Kosongkan daftar pencipta

                    // Reset elemen Select2
                    $('#jenis_hak_cipta').val(null).trigger('change');
                    $('#sub_jenis_hak_cipta').val(null).trigger('change');
                    $('#nationality').val(null).trigger('change');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Terjadi kesalahan saat mengirim data."
                });
            });
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