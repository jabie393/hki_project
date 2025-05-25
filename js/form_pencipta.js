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

            // Cek apakah form Indonesia aktif
            const isIndonesiaFormActive = document.getElementById('indonesia-form').style.display !== 'none';
            const requiredFields = modalForm.querySelectorAll('.required-field');
            let isValid = true;
            let emptyFields = [];

            requiredFields.forEach(field => {
                // Skip fields yang tidak terlihat atau disabled
                if (field.offsetParent === null || field.disabled) return;

                // Skip field dari form yang tidak aktif
                if (isIndonesiaFormActive && field.closest('#non-indonesia-form')) return;
                if (!isIndonesiaFormActive && field.closest('#indonesia-form')) return;

                // Validasi khusus untuk dropdown
                if (field.tagName === 'SELECT') {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error-field');
                        emptyFields.push(getFieldLabel(field));
                    } else {
                        field.classList.remove('error-field');
                    }
                }
                // Validasi untuk input biasa
                else if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error-field');
                    emptyFields.push(getFieldLabel(field));
                } else {
                    field.classList.remove('error-field');
                }
            });

            if (!isValid) {
                // Tampilkan SweetAlert
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Belum Lengkap',
                    html: `Harap lengkapi semua field wajib:<br><br>- ${emptyFields.join('<br>- ')}`,
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        container: 'swal2-top'
                    }
                });
                return;
            }

            // Jika validasi sukses, proses form
            const penciptaDiv = document.createElement("div");
            penciptaDiv.classList.add("pencipta");

            // Ambil data dari form secara manual agar field tersembunyi tetap bisa diabaikan
            const data = {};
            [...modalForm.elements].forEach(input => {
                if (!input.name || input.disabled || input.type === 'submit') return;

                // Abaikan input yang sedang disembunyikan
                if (input.offsetParent === null) return;

                const key = input.name;
                const value = input.value.trim().replace(/,+$/, "");

                if (key in data) {
                    if (Array.isArray(data[key])) {
                        data[key].push(value);
                    } else {
                        data[key] = [data[key], value];
                    }
                } else {
                    data[key] = value;
                }
            });
            // Fungsi untuk mendapatkan label field
            function getFieldLabel(field) {
                const label = modalForm.querySelector(`label[for="${field.id}"]`) ||
                    field.closest('.form-group')?.querySelector('label');
                return label?.textContent?.replace(':', '').trim() || 'Field';
            }

            const nama = data["nama[]"] || "Tanpa Nama";

            penciptaDiv.innerHTML = `
                <h4 class="pencipta-label">Pencipta</h4>
                <strong>${nama}</strong><br>
                <button type="button" class="editPencipta">Edit</button>
                <button type="button" class="hapusPencipta">Hapus</button>
            `;

            penciptaDiv.dataset.form = JSON.stringify(data);

            penciptaDiv.querySelector(".editPencipta").onclick = () => openModalForEdit(penciptaDiv);
            penciptaDiv.querySelector(".hapusPencipta").onclick = () => {
                // Konfirmasi sebelum hapus
                Swal.fire({
                    title: 'Hapus Pencipta?',
                    text: `Anda yakin ingin menghapus pencipta ${nama}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'swal-confirm-button',
                        cancelButton: 'swal-cancel-button'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        penciptaDiv.remove();
                        updatePenciptaLabels();

                        // Feedback setelah hapus
                        Swal.fire({
                            title: 'Dihapus!',
                            text: 'Pencipta telah dihapus',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            };

            if (editingPencipta) {
                editingPencipta.replaceWith(penciptaDiv);
                editingPencipta = null;

                // Feedback setelah edit
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data pencipta telah diperbarui',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                penciptaList.appendChild(penciptaDiv);

                // Feedback setelah tambah
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Pencipta baru telah ditambahkan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }

            modal.style.display = "none";
            modalForm.reset();
            updatePenciptaLabels();

            // Reset semua select2 setelah submit
            $(modalForm).find('select').each(function () {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).val('').trigger('change');
                }
            });
        });

    }

    function openModalForEdit(div) {
        editingPencipta = div;
        const values = JSON.parse(div.dataset.form);
        modalForm.reset();

        const selectedCountryName = values["negara[]"] || "";

        // Tampilkan modal lebih awal agar elemen render
        modal.style.display = "flex";

        loadCountriesForModalForm(selectedCountryName).then(() => {
            const nationalitySelect = document.getElementById("nationalityform");

            // Set selected value
            $(nationalitySelect).val(selectedCountryName).trigger("change");

            // Ambil kode ISO2 dari option terpilih
            const selectedOption = Array.from(nationalitySelect.options).find(opt => opt.value === selectedCountryName);
            const selectedCountryCode = selectedOption?.dataset.iso2 || "";
            const isIndonesia = selectedCountryCode === "ID";

            const indoForm = document.getElementById("indonesia-form");
            const nonIndoForm = document.getElementById("non-indonesia-form");

            if (isIndonesia) {
                indoForm.style.display = "block";
                nonIndoForm.style.display = "none";
                indoForm.querySelectorAll("select, input").forEach(el => el.disabled = false);
                nonIndoForm.querySelectorAll("input").forEach(el => el.disabled = true);

                loadIndonesianRegionsWithValues(values).then(() => {
                    // selesai
                });
            } else {
                indoForm.style.display = "none";
                nonIndoForm.style.display = "block";
                nonIndoForm.querySelectorAll("input").forEach(el => el.disabled = false);
                indoForm.querySelectorAll("select, input").forEach(el => el.disabled = true);

                loadCSCStatesWithValues(selectedCountryCode, values).then(() => {
                    // Isi input manual
                    const kec = modalForm.querySelector(".manual-kecamatan");
                    const kel = modalForm.querySelector(".manual-kelurahan");
                    const kode = modalForm.querySelector(".manual-kodepos");

                    if (kec) kec.value = values["kecamatan[]"] || "";
                    if (kel) kel.value = values["kelurahan[]"] || "";
                    if (kode) kode.value = values["kode_pos[]"] || "";
                });
            }

            // Set input biasa (nama, nik, dsb)
            for (const [key, value] of Object.entries(values)) {
                if (["provinsi[]", "kota[]", "kecamatan[]", "kelurahan[]", "kode_pos[]"].includes(key)) continue;
                const input = modalForm.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = Array.isArray(value) ? value[0] : value;
                }
            }

        });

    }


    // Fungsi untuk memperbarui label pencipta dan tombol hapus
    function updatePenciptaLabels() {
        let penciptaDivs = document.querySelectorAll(".pencipta");
        penciptaDivs.forEach((div, index) => {
            let label = div.querySelector(".pencipta-label");
            if (label) {
                label.textContent = "Pencipta " + (index + 1);
            }
        });

        let deleteButtons = document.querySelectorAll(".hapusPencipta");
        if (penciptaDivs.length > 1) {
            deleteButtons.forEach(btn => btn.style.display = "inline-block");
        } else {
            deleteButtons.forEach(btn => btn.style.display = "none");
        }
    }

    // Handler negara
    $(document).on("change", "#nationalityform", function () {
        const selectedCountryCode = this.options[this.selectedIndex].dataset.iso2;

        const isIndonesia = selectedCountryCode === "ID";
        const indoForm = document.getElementById("indonesia-form");
        const nonIndoForm = document.getElementById("non-indonesia-form");

        if (isIndonesia) {
            indoForm.style.display = "block";
            nonIndoForm.style.display = "none";
            indoForm.querySelectorAll("select, input").forEach(el => el.disabled = false);
            nonIndoForm.querySelectorAll("input").forEach(el => el.disabled = true);

            loadIndonesianRegions();
        } else if (selectedCountryCode) {
            // pastikan selectedCountryCode valid sebelum loadCSCStates
            indoForm.style.display = "none";
            nonIndoForm.style.display = "block";
            nonIndoForm.querySelectorAll("input").forEach(el => el.disabled = false);
            indoForm.querySelectorAll("select, input").forEach(el => el.disabled = true);

            loadCSCStates(selectedCountryCode);
        } else {
            // Jika tidak ada country selected
            indoForm.style.display = "none";
            nonIndoForm.style.display = "none";
        }
    });



    // Fungsi untuk memuat data wilayah Indonesia dengan Select2
    function loadIndonesianRegions() {
        const provinsiSelect = document.querySelector(".provinsi");
        const kabupatenSelect = document.querySelector(".kabupaten");
        const kecamatanSelect = document.querySelector(".kecamatan");
        const kelurahanSelect = document.querySelector(".kelurahan");
        const kodeposInput = document.querySelector(".kodepos");

        // Inisialisasi Select2 untuk semua dropdown
        initializeSelect2(provinsiSelect, "Pilih Provinsi");
        initializeSelect2(kabupatenSelect, "Pilih Kabupaten/Kota");
        initializeSelect2(kecamatanSelect, "Pilih Kecamatan");
        initializeSelect2(kelurahanSelect, "Pilih Kelurahan");

        // Reset semua dropdown
        $(provinsiSelect).empty().append('<option value="">Pilih Provinsi</option>');
        $(kabupatenSelect).empty().append('<option value="">Pilih Kabupaten/Kota</option>').prop('disabled', true);
        $(kecamatanSelect).empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $(kelurahanSelect).empty().append('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
        kodeposInput.value = '';

        // Load Provinsi
        fetch("https://ibnux.github.io/data-indonesia/provinsi.json")
            .then(res => res.json())
            .then(data => {
                data.forEach(prov => {
                    const option = new Option(prov.nama, prov.nama);
                    option.dataset.id = prov.id;
                    $(provinsiSelect).append(option);
                });
            });

        // Event ketika provinsi dipilih
        $(provinsiSelect).on('change', function () {
            if (!this.value) {
                $(kabupatenSelect).empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>').prop('disabled', true);
                return;
            }

            const provId = $(this).find(':selected').data('id');

            // Reset dropdown dependen
            $(kabupatenSelect).empty().append('<option value="">Pilih Kabupaten/Kota</option>').prop('disabled', false);
            $(kecamatanSelect).empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $(kelurahanSelect).empty().append('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
            kodeposInput.value = '';

            if (!provId) return;

            fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${provId}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kab => {
                        const option = new Option(kab.nama, kab.nama);
                        option.dataset.id = kab.id;
                        $(kabupatenSelect).append(option);
                    });
                });
        });

        // Event ketika kabupaten dipilih
        $(kabupatenSelect).on('change', function () {
            if (!this.value) {
                $(kecamatanSelect).empty().append('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                return;
            }
            const kabId = $(this).find(':selected').data('id');

            // Reset dropdown dependen
            $(kecamatanSelect).empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', false);
            $(kelurahanSelect).empty().append('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
            kodeposInput.value = '';

            if (!kabId) return;

            fetch(`https://ibnux.github.io/data-indonesia/kecamatan/${kabId}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kec => {
                        const option = new Option(kec.nama, kec.nama);
                        option.dataset.id = kec.id;
                        $(kecamatanSelect).append(option);
                    });
                });
        });

        // Event ketika kecamatan dipilih
        $(kecamatanSelect).on('change', function () {
            if (!this.value) {
                $(kelurahanSelect).empty().append('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
                return;
            }
            const kecId = $(this).find(':selected').data('id');

            // Reset dropdown dependen
            $(kelurahanSelect).empty().append('<option value="">Pilih Kelurahan</option>').prop('disabled', false);
            kodeposInput.value = '';

            if (!kecId) return;

            fetch(`https://ibnux.github.io/data-indonesia/kelurahan/${kecId}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kel => {
                        const option = new Option(kel.nama, kel.nama);
                        $(kelurahanSelect).append(option);
                    });
                });
        });

        // Event ketika kelurahan dipilih
        $(kelurahanSelect).on('change', function () {
            const kelurahan = $(this).val().trim();
            kodeposInput.value = 'Mencari...';

            if (!kelurahan) {
                kodeposInput.value = '';
                return;
            }

            fetch(`https://kodepos.vercel.app/search?q=${encodeURIComponent(kelurahan)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.data && data.data.length > 0) {
                        kodeposInput.value = data.data[0].code;
                    } else {
                        kodeposInput.value = "Tidak ditemukan";
                    }
                })
                .catch(() => {
                    kodeposInput.value = "Error";
                });
        });
    }


    // Fungsi untuk memuat data state dan city dengan Select2 (non-Indonesia)
    function loadCSCStates(selectedCountry) {
        if (!selectedCountry) {
            console.error("Country code is undefined or empty!");
            return;
        }

        const stateSelect = document.querySelector(".state");
        const citySelect = document.querySelector(".city");

        // Inisialisasi Select2
        initializeSelect2(stateSelect, "Pilih State");
        initializeSelect2(citySelect, "Pilih City");

        // Reset dropdown
        $(stateSelect).empty().append('<option value="">Pilih State</option>').prop('disabled', false);
        $(citySelect).empty().append('<option value="">Pilih City</option>').prop('disabled', true);

        // Load states
        fetch(`${configCSC.cUrl}/${selectedCountry}/states`, {
            headers: { "X-CSCAPI-KEY": configCSC.ckey }
        })
            .then(res => res.json())
            .then(data => {
                data.forEach(state => {
                    const option = new Option(state.name, state.name);
                    option.dataset.iso2 = state.iso2;
                    $(stateSelect).append(option);
                });
            });

        // Event ketika state dipilih
        $(stateSelect).on('change', function () {
            const selectedStateIso2 = $(this).find(':selected').data('iso2');
            const stateCode = selectedStateIso2;

            // Reset city dropdown
            $(citySelect).empty().append('<option value="">Pilih City</option>').prop('disabled', false);

            if (!stateCode) return;

            fetch(`${configCSC.cUrl}/${selectedCountry}/states/${stateCode}/cities`, {
                headers: { "X-CSCAPI-KEY": configCSC.ckey }
            })
                .then(res => res.json())
                .then(data => {
                    data.forEach(city => {
                        const option = new Option(city.name, city.name);
                        $(citySelect).append(option);
                    });
                });
        });
    }

    // Fungsi untuk memuat data wilayah Indonesia dengan nilai yang sudah ada (untuk edit)
    function loadIndonesianRegionsWithValues(values) {
        return new Promise((resolve) => {
            loadIndonesianRegions(); // Load struktur dropdown dulu

            const provinsiSelect = document.querySelector(".provinsi");
            const kabupatenSelect = document.querySelector(".kabupaten");
            const kecamatanSelect = document.querySelector(".kecamatan");
            const kelurahanSelect = document.querySelector(".kelurahan");

            const prov = values["provinsi[]"];
            const kab = values["kota[]"];
            const kec = values["kecamatan[]"];
            const kel = values["kelurahan[]"];
            const kodepos = values["kode_pos[]"];

            setTimeout(() => {
                if (prov) {
                    $(provinsiSelect).val(prov).trigger('change');

                    setTimeout(() => {
                        if (kab) {
                            $(kabupatenSelect).val(kab).trigger('change');

                            setTimeout(() => {
                                if (kec) {
                                    $(kecamatanSelect).val(kec).trigger('change');

                                    setTimeout(() => {
                                        if (kel) {
                                            $(kelurahanSelect).val(kel).trigger('change');
                                        }
                                        document.querySelector(".kodepos").value = kodepos || '';
                                        resolve();
                                    }, 300);
                                } else {
                                    resolve();
                                }
                            }, 300);
                        } else {
                            resolve();
                        }
                    }, 300);
                } else {
                    resolve();
                }
            }, 300);
        });
    }

    // Fungsi untuk memuat data state dan city dengan nilai yang sudah ada (untuk edit)
    function loadCSCStatesWithValues(selectedCountryCode, values) {
        return new Promise((resolve) => {
            const stateSelect = document.querySelector(".state");
            const citySelect = document.querySelector(".city");

            const stateName = values["provinsi[]"];
            const cityName = values["kota[]"];

            // Inisialisasi Select2
            initializeSelect2(stateSelect, "Pilih State");
            initializeSelect2(citySelect, "Pilih City");

            // Reset dropdown
            $(stateSelect).empty().append('<option value="">Loading state...</option>').prop('disabled', false);
            $(citySelect).empty().append('<option value="">Pilih City</option>').prop('disabled', true);

            fetch(`${configCSC.cUrl}/${selectedCountryCode}/states`, {
                headers: { "X-CSCAPI-KEY": configCSC.ckey }
            })
                .then(res => res.json())
                .then(states => {
                    $(stateSelect).empty().append('<option value="">-- Pilih State --</option>');

                    states.forEach(s => {
                        const option = new Option(s.name, s.name);
                        option.dataset.iso2 = s.iso2;
                        $(stateSelect).append(option);
                    });

                    // Set state jika ada nilai sebelumnya
                    if (stateName) {
                        $(stateSelect).val(stateName).trigger('change');

                        // Load cities untuk state yang dipilih
                        const selectedStateOption = $(stateSelect).find(`option[value="${stateName}"]`);
                        const selectedStateIso2 = selectedStateOption.data('iso2');

                        if (selectedStateIso2) {
                            fetch(`${configCSC.cUrl}/${selectedCountryCode}/states/${selectedStateIso2}/cities`, {
                                headers: { "X-CSCAPI-KEY": configCSC.ckey }
                            })
                                .then(res => res.json())
                                .then(cities => {
                                    $(citySelect).empty().append('<option value="">-- Pilih City --</option>');

                                    cities.forEach(c => {
                                        const option = new Option(c.name, c.name);
                                        $(citySelect).append(option);
                                    });

                                    $(citySelect).prop('disabled', false);

                                    // Set city jika ada nilai sebelumnya
                                    if (cityName) {
                                        $(citySelect).val(cityName);
                                    }

                                    // Isi input manual
                                    const kec = modalForm.querySelector(".manual-kecamatan");
                                    const kel = modalForm.querySelector(".manual-kelurahan");
                                    const kode = modalForm.querySelector(".manual-kodepos");

                                    if (kec) kec.value = values["kecamatan[]"] || "";
                                    if (kel) kel.value = values["kelurahan[]"] || "";
                                    if (kode) kode.value = values["kode_pos[]"] || "";

                                    resolve();
                                });
                        } else {
                            resolve();
                        }
                    } else {
                        resolve();
                    }
                });
        });
    }

    const configCSC = {
        cUrl: 'https://api.countrystatecity.in/v1/countries',
        ckey: 'NHhvOEcyWk50N2Vna3VFTE00bFp3MjFKR0ZEOUhkZlg4RTk1MlJlaA=='
    };

    function loadCountriesForModalForm(selectedCountry = "") {
        const countrySelect = document.getElementById("nationalityform");
        countrySelect.innerHTML = '<option value="">Loading countries...</option>';

        return fetch(configCSC.cUrl, {
            headers: { "X-CSCAPI-KEY": configCSC.ckey }
        })
            .then(response => response.json())
            .then(countries => {
                countrySelect.innerHTML = '<option value="">-- Pilih Negara --</option>';
                countries.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.name;
                    option.textContent = country.name;
                    option.dataset.iso2 = country.iso2;

                    // Tandai negara yang dipilih sebelumnya
                    if (country.iso2 === selectedCountry) {
                        option.selected = true;
                    }

                    countrySelect.appendChild(option);
                });
                // Inisialisasi Select2 setelah isi diubah
                const $select = $(countrySelect);
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2("destroy");
                }
                $select.select2({
                    placeholder: "-- Pilih Negara --",
                    width: "100%",
                    allowClear: true,
                    dropdownParent: $('#modalPencipta')
                });

                // Tambahkan trigger change jika ada selectedCountry
                if (selectedCountry) {
                    $select.trigger("change");
                }
            })
            .catch(error => {
                console.error("Error loading countries:", error);
                countrySelect.innerHTML = '<option value="">Gagal memuat negara</option>';
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

    // Fungsi untuk menghapus kelas error dari semua field
    function clearErrorFields() {
        const errorFields = document.querySelectorAll('.error-field');
        errorFields.forEach(field => {
            field.classList.remove('error-field');
        });
    }

    // Modal
    // ==== Modal Tambah Pencipta ====
    const addPenciptaBtn = document.getElementById("addPencipta");
    const closeModalBtn = document.querySelector(".close-modal");

    // Tampilkan modal
    addPenciptaBtn.addEventListener("click", function () {
        modal.style.display = "flex";
        modalForm.reset();
        editingPencipta = null;

        // Hapus semua error field sebelum memulai
        clearErrorFields();

        // Load ulang dropdown negara
        loadCountriesForModalForm();

        // Reset form agar hanya dropdown negara aktif, lainnya disembunyikan
        const indoForm = document.getElementById("indonesia-form");
        const nonIndoForm = document.getElementById("non-indonesia-form");
        indoForm.style.display = "none";
        nonIndoForm.style.display = "none";

        // Reset dropdown Indonesia
        const provinsiSelect = document.querySelector(".provinsi");
        const kabupatenSelect = document.querySelector(".kabupaten");
        const kecamatanSelect = document.querySelector(".kecamatan");
        const kelurahanSelect = document.querySelector(".kelurahan");
        const kodeposInput = document.querySelector(".kodepos");

        initializeSelect2(provinsiSelect, "Pilih Provinsi");
        initializeSelect2(kabupatenSelect, "Pilih Kabupaten/Kota");
        initializeSelect2(kecamatanSelect, "Pilih Kecamatan");
        initializeSelect2(kelurahanSelect, "Pilih Kelurahan");

        $(provinsiSelect).empty().append('<option value="">Pilih Provinsi</option>');
        $(kabupatenSelect).empty().append('<option value="">Pilih Kabupaten/Kota</option>').prop('disabled', true);
        $(kecamatanSelect).empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $(kelurahanSelect).empty().append('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
        if (kodeposInput) kodeposInput.value = "";

        // Reset dropdown non-Indonesia
        const stateSelect = document.querySelector(".state");
        const citySelect = document.querySelector(".city");
        if (stateSelect) stateSelect.innerHTML = '<option value="">Pilih State</option>';
        if (citySelect) citySelect.innerHTML = '<option value="">Pilih City</option>';
        if (stateSelect) stateSelect.disabled = true;
        if (citySelect) citySelect.disabled = true;

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

    // Fungsi inisialisasi Select2 untuk elemen tertentu
    function initializeSelect2(selectElement, placeholder) {
        const $select = $(selectElement);

        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2("destroy");
        }

        $select.select2({
            placeholder: placeholder || "Pilih...",
            width: "100%",
            allowClear: true,
            dropdownParent: $('#modalPencipta')
        });
    }
}