// ================== MODAL PENCIPTA ==================
function initModalPencipta() {
    const modal = document.getElementById("modalPencipta");
    const modalForm = document.getElementById("modalFormPencipta");
    const penciptaList = document.getElementById("pencipta-list");
    let editingPencipta = null;

    const stateSelect = document.querySelector(".state");
    const citySelect = document.querySelector(".city");

    // Pastikan listener hanya ditambahkan sekali
    if (!modalForm.dataset.listenerAdded) {
        modalForm.dataset.listenerAdded = true;

        modalForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Untuk negara tanpa state, set nilai default
            const countrySelect = document.getElementById("nationalityform");
            const countryCode = countrySelect.options[countrySelect.selectedIndex]?.dataset.iso2;

            if (countryCode && countryCode !== "ID" && !countryHasStates(countryCode)) {
                document.querySelectorAll('input[name="provinsi[]"]').forEach(el => el.value = "-");
                document.querySelectorAll('input[name="kota[]"]').forEach(el => el.value = "-");
                document.querySelectorAll('input[name="kecamatan[]"]').forEach(el => el.value = "-");
                document.querySelectorAll('input[name="kelurahan[]"]').forEach(el => el.value = "-");
                document.querySelectorAll('input[name="kode_pos[]"]').forEach(el => el.value = "-");
            }

            // Ambil elemen form
            const negaraSelect = document.getElementById("nationalityform");
            const selectedCountryCode = negaraSelect.options[negaraSelect.selectedIndex].dataset.iso2;
            const isIndonesia = selectedCountryCode === "ID";

            // Jika negara non-Indonesia dan tidak memiliki state
            if (!isIndonesia && negaraSelect.value && !countryHasStates(selectedCountryCode)) {
                // Force set nilai untuk negara tanpa state
                document.querySelector('input[name="kecamatan[]"]').value = "-";
                document.querySelector('input[name="kelurahan[]"]').value = "-";
            }

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

                // Skip field state/city jika dropdown disabled atau tidak tersedia
                if ((field.name === 'provinsi[]' || field.name === 'kota[]') && field.disabled) return;

                // Skip field district dan village jika tidak required
                if ((field.name === 'kecamatan[]' || field.name === 'kelurahan[]') &&
                    !field.classList.contains('required-field')) return;

                // Validasi khusus untuk dropdown
                if (field.tagName === 'SELECT') {
                    if (!field.value.trim()) {
                        isValid = false;
                        // Tambahkan kelas error ke select asli dan container Select2
                        field.classList.add('error-field');
                        $(field).next('.select2-container').addClass('error-field');
                        emptyFields.push(getFieldLabel(field));
                    } else {
                        field.classList.remove('error-field');
                        $(field).next('.select2-container').removeClass('error-field');
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
                    showConfirmButton: true,
                    confirmButtonText: 'Oke, paham!',
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
                <button type="button" class="btn editPencipta">Edit</button>
                <button type="button" class="btn hapusPencipta">Hapus</button>
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

    let suppressNationalityChange = false;

    function openModalForEdit(div) {
        editingPencipta = div;
        const values = JSON.parse(div.dataset.form);
        modalForm.reset();

        // Hapus semua error field sebelum memulai
        clearErrorFields();

        const selectedCountryName = values["negara[]"] || "";

        // Tampilkan modal lebih awal agar elemen render
        modal.style.display = "flex";

        loadCountriesForModalForm(selectedCountryName).then(() => {
            const nationalitySelect = document.getElementById("nationalityform");

            // Set selected value
            suppressNationalityChange = true;
            $(nationalitySelect).val(selectedCountryName).trigger("change.select2");

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

                    // Trigger change untuk Select2 jika ada nilai
                    if ($(input).hasClass('select2-hidden-accessible')) {
                        $(input).trigger('change');
                    }
                }
            }

            // Inisialisasi Select2 untuk jenis kelamin
            const jenisKelaminSelect = $('#jenis_kelamin');
            if (jenisKelaminSelect.length) {
                if (jenisKelaminSelect.hasClass('select2-hidden-accessible')) {
                    jenisKelaminSelect.select2('destroy');
                }
                jenisKelaminSelect.select2({
                    placeholder: "-- Pilih Jenis Kelamin --",
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: Infinity,
                    dropdownParent: $('#modalPencipta')
                });
                // Set value dari data
                if (values["jenis_kelamin[]"]) {
                    jenisKelaminSelect.val(values["jenis_kelamin[]"]).trigger('change');
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
    $(document).on("change", "#nationalityform", async function () {
        if (suppressNationalityChange) {
            suppressNationalityChange = false;
            return; // lewati auto-trigger dari edit
        }

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
            indoForm.style.display = "none";
            nonIndoForm.style.display = "block";
            nonIndoForm.querySelectorAll("input").forEach(el => el.disabled = false);
            indoForm.querySelectorAll("select, input").forEach(el => el.disabled = true);

            // Cek apakah negara memiliki state
            const hasStates = await countryHasStates(selectedCountryCode);

            if (!hasStates) {
                // Negara tanpa state (seperti Antartica)
                if ($(stateSelect).hasClass("select2-hidden-accessible")) {
                    $(stateSelect).select2("destroy");
                }
                $(stateSelect).empty().append('<option value="">-- Not Available --</option>').prop('disabled', true);
                initializeSelect2(stateSelect, "-- Not Available --");

                if ($(citySelect).hasClass("select2-hidden-accessible")) {
                    $(citySelect).select2("destroy");
                }
                $(citySelect).empty().append('<option value="">-- Not Available --</option>').prop('disabled', true);
                initializeSelect2(citySelect, "-- Not Available --");

                // Nonaktifkan required field untuk semua field alamat
                document.querySelector('.manual-kecamatan').classList.remove('required-field');
                document.querySelector('.manual-kelurahan').classList.remove('required-field');

                // Isi otomatis dengan "-"
                document.querySelector('.manual-kecamatan').value = "-";
                document.querySelector('.manual-kelurahan').value = "-";
            } else {
                // Negara dengan state, load seperti biasa
                loadCSCStates(selectedCountryCode);

                // Aktifkan required field
                document.querySelector('.manual-kecamatan').classList.add('required-field');
                document.querySelector('.manual-kelurahan').classList.add('required-field');
            }
        } else {
            // Jika tidak ada country selected
            indoForm.style.display = "none";
            nonIndoForm.style.display = "none";
        }
    });

    // Event ketika state dipilih
    $(stateSelect).on('change', async function () {
        const selectedStateIso2 = $(this).find(':selected').data('iso2');
        const stateCode = selectedStateIso2;
        const selectedCountryCode = $('#nationalityform').find(':selected').data('iso2');

        // Reset city dropdown
        $(citySelect).empty().append('<option value="">Loading cities...</option>').prop('disabled', true);

        if (!stateCode || !selectedCountryCode) return;

        try {
            // Cek apakah state memiliki city
            const hasCities = await stateHasCities(selectedCountryCode, stateCode);

            if (!hasCities) {
                // Jika tidak ada kota: isi "-" dan nonaktifkan dropdown
                $(citySelect)
                    .empty()
                    .append('<option value="-">-- Not Available --</option>')
                    .prop('disabled', true);
                $(citySelect).val("-").trigger("change");

                // Nonaktifkan required
                document.querySelector('.manual-kecamatan').classList.remove('required-field');
                document.querySelector('.manual-kelurahan').classList.remove('required-field');

                // Isi otomatis
                document.querySelector('.manual-kecamatan').value = "-";
                document.querySelector('.manual-kelurahan').value = "-";
                return;
            }

            // Jika memiliki city, lanjutkan dengan load city normal
            const response = await fetch(`${configCSC.cUrl}/${selectedCountryCode}/states/${stateCode}/cities`, {
                headers: { "X-CSCAPI-KEY": configCSC.ckey }
            });
            const cities = await response.json();

            $(citySelect).empty().append('<option value="">-- Select City --</option>').prop('disabled', false);

            cities.forEach(c => {
                const option = new Option(c.name, c.name);
                $(citySelect).append(option);
            });

            // Aktifkan kembali required field
            document.querySelector('.manual-kecamatan').classList.add('required-field');
            document.querySelector('.manual-kelurahan').classList.add('required-field');

        } catch (error) {
            console.error("Error loading cities:", error);
            $(citySelect).empty().append('<option value="">Error loading cities</option>');
        }
    });

    // Fungsi untuk mengecek apakah negara memiliki state
    async function countryHasStates(countryCode) {
        try {
            const response = await fetch(`${configCSC.cUrl}/${countryCode}/states`, {
                headers: { "X-CSCAPI-KEY": configCSC.ckey }
            });
            const states = await response.json();
            return states && states.length > 0;
        } catch (error) {
            console.error("Error checking states:", error);
            return false;
        }
    }

    // Fungsi untuk mengecek apakah state memiliki city
    async function stateHasCities(countryCode, stateIso2) {
        try {
            const response = await fetch(`${configCSC.cUrl}/${countryCode}/states/${stateIso2}/cities`, {
                headers: { "X-CSCAPI-KEY": configCSC.ckey }
            });
            const cities = await response.json();
            return Array.isArray(cities) && cities.length > 0;
        } catch (error) {
            console.error("Error checking cities:", error);
            return false;
        }
    }


    // Fungsi untuk memuat data wilayah Indonesia dengan Select2
    function loadIndonesianRegions() {
        const provinsiSelect = document.querySelector(".provinsi");
        const kabupatenSelect = document.querySelector(".kabupaten");
        const kecamatanSelect = document.querySelector(".kecamatan");
        const kelurahanSelect = document.querySelector(".kelurahan");
        const kodeposInput = document.querySelector(".kodepos");

        // Inisialisasi Select2 untuk semua dropdown
        initializeSelect2(provinsiSelect, "-- Pilih Provinsi --");
        initializeSelect2(kabupatenSelect, "-- Pilih Kabupaten/Kota --");
        initializeSelect2(kecamatanSelect, "-- Pilih Kecamatan --");
        initializeSelect2(kelurahanSelect, "-- Pilih Kelurahan --");

        // Reset semua dropdown
        $(provinsiSelect).empty().append('<option value="">-- Pilih Provinsi --</option>');
        $(kabupatenSelect).empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>').prop('disabled', true);
        $(kecamatanSelect).empty().append('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
        $(kelurahanSelect).empty().append('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
        kodeposInput.value = '';

        // Load Provinsi
        fetch("https://ibnux.github.io/data-indonesia/provinsi.json")
            .then(res => res.json())
            .then(data => {
                // Destroy Select2 sebelum mengubah isi
                if ($(provinsiSelect).hasClass("select2-hidden-accessible")) {
                    $(provinsiSelect).select2("destroy");
                }
                // Hapus semua option kecuali placeholder
                $(provinsiSelect).find('option:not(:first)').remove();
                data.forEach(prov => {
                    const option = new Option(prov.nama, prov.nama);
                    option.dataset.id = prov.id;
                    $(provinsiSelect).append(option);
                });
                // Inisialisasi ulang Select2 dengan placeholder
                initializeSelect2(provinsiSelect, "-- Pilih Provinsi --");
            });

        // Event ketika provinsi dipilih
        $(provinsiSelect).off('change').on('change', function () {
            if (!this.value) {
                $(kabupatenSelect).empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>').prop('disabled', true);
                return;
            }

            const provId = $(this).find(':selected').data('id');

            // Reset dropdown dependen
            $(kabupatenSelect).empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>').prop('disabled', false);
            $(kecamatanSelect).empty().append('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
            $(kelurahanSelect).empty().append('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
            kodeposInput.value = '';

            if (!provId) return;

            fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${provId}.json`)
                .then(res => res.json())
                .then(data => {
                    if ($(kabupatenSelect).hasClass("select2-hidden-accessible")) {
                        $(kabupatenSelect).select2("destroy");
                    }
                    $(kabupatenSelect).find('option:not(:first)').remove();
                    data.forEach(kab => {
                        const option = new Option(kab.nama, kab.nama);
                        option.dataset.id = kab.id;
                        $(kabupatenSelect).append(option);
                    });
                    initializeSelect2(kabupatenSelect, "-- Pilih Kabupaten/Kota --");
                });
        });

        // Event ketika kabupaten dipilih
        $(kabupatenSelect).off('change').on('change', function () {
            if (!this.value) {
                $(kecamatanSelect).empty().append('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                return;
            }
            const kabId = $(this).find(':selected').data('id');

            // Reset dropdown dependen
            $(kecamatanSelect).empty().append('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', false);
            $(kelurahanSelect).empty().append('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
            kodeposInput.value = '';

            if (!kabId) return;

            fetch(`https://ibnux.github.io/data-indonesia/kecamatan/${kabId}.json`)
                .then(res => res.json())
                .then(data => {
                    if ($(kecamatanSelect).hasClass("select2-hidden-accessible")) {
                        $(kecamatanSelect).select2("destroy");
                    }
                    $(kecamatanSelect).find('option:not(:first)').remove();
                    data.forEach(kec => {
                        const option = new Option(kec.nama, kec.nama);
                        option.dataset.id = kec.id;
                        $(kecamatanSelect).append(option);
                    });
                    initializeSelect2(kecamatanSelect, "-- Pilih Kecamatan --");
                });
        });

        // Event ketika kecamatan dipilih
        $(kecamatanSelect).off('change').on('change', function () {
            if (!this.value) {
                $(kelurahanSelect).empty().append('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
                return;
            }
            const kecId = $(this).find(':selected').data('id');

            // Reset dropdown dependen
            $(kelurahanSelect).empty().append('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', false);
            kodeposInput.value = '';

            if (!kecId) return;

            fetch(`https://ibnux.github.io/data-indonesia/kelurahan/${kecId}.json`)
                .then(res => res.json())
                .then(data => {
                    if ($(kelurahanSelect).hasClass("select2-hidden-accessible")) {
                        $(kelurahanSelect).select2("destroy");
                    }
                    $(kelurahanSelect).find('option:not(:first)').remove();
                    data.forEach(kel => {
                        const option = new Option(kel.nama, kel.nama);
                        $(kelurahanSelect).append(option);
                    });
                    initializeSelect2(kelurahanSelect, "-- Pilih Kelurahan --");
                });
        });

        // Event ketika kelurahan dipilih
        $(kelurahanSelect).off('change').on('change', function () {
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
                        kodeposInput.value = "Tidak Ditemukan";
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

        // Destroy Select2 sebelum mengubah isi
        if ($(stateSelect).hasClass("select2-hidden-accessible")) {
            $(stateSelect).select2("destroy");
        }
        if ($(citySelect).hasClass("select2-hidden-accessible")) {
            $(citySelect).select2("destroy");
        }

        // Reset dropdown
        $(stateSelect).empty().append('<option value="">-- Select State --</option>').prop('disabled', false);
        $(citySelect).empty().append('<option value="">-- Not Available --</option>').prop('disabled', true);

        // Inisialisasi ulang Select2 dengan placeholder
        initializeSelect2(stateSelect, "-- Select State --");
        initializeSelect2(citySelect, "-- Select City --");

        // Load states
        fetch(`${configCSC.cUrl}/${selectedCountry}/states`, {
            headers: { "X-CSCAPI-KEY": configCSC.ckey }
        })
            .then(res => res.json())
            .then(data => {
                // Destroy Select2 sebelum mengubah isi
                if ($(stateSelect).hasClass("select2-hidden-accessible")) {
                    $(stateSelect).select2("destroy");
                }
                $(stateSelect).find('option:not(:first)').remove();
                data.forEach(state => {
                    const option = new Option(state.name, state.name);
                    option.dataset.iso2 = state.iso2;
                    $(stateSelect).append(option);
                });
                initializeSelect2(stateSelect, "-- Select State --");
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

    function waitForOption(selectElement, valueToWait, timeout = 5000) {
        return new Promise((resolve, reject) => {
            const optionExists = () => [...selectElement.options].some(opt => opt.value === valueToWait);
            if (optionExists()) {
                resolve();
                return;
            }

            const observer = new MutationObserver(() => {
                if (optionExists()) {
                    observer.disconnect();
                    resolve();
                }
            });

            observer.observe(selectElement, { childList: true });

            setTimeout(() => {
                observer.disconnect();
                reject(new Error("Option not found in time: " + valueToWait));
            }, timeout);
        });
    }

    async function loadCSCStatesWithValues(selectedCountryCode, values) {
        return new Promise(async (resolve) => {
            const stateSelect = document.querySelector(".state");
            const citySelect = document.querySelector(".city");

            const stateName = values["provinsi[]"];
            const cityName = values["kota[]"];

            // Cek apakah negara memiliki state
            const hasStates = await countryHasStates(selectedCountryCode);

            if (!hasStates) {
                $(stateSelect).empty().append('<option value="">-- Not Available --</option>').prop('disabled', true);
                $(citySelect).empty().append('<option value="">-- Not Available --</option>').prop('disabled', true);

                document.querySelector('.manual-kecamatan').classList.remove('required-field');
                document.querySelector('.manual-kelurahan').classList.remove('required-field');

                document.querySelector('.manual-kecamatan').value = "-";
                document.querySelector('.manual-kelurahan').value = "-";
                resolve();
                return;
            }

            initializeSelect2(stateSelect, "-- Select State --");
            initializeSelect2(citySelect, "-- Select City --");

            $(stateSelect).empty().append('<option value="">Loading state...</option>').prop('disabled', false);
            $(citySelect).empty().append('<option value="">-- Not Available --</option>').prop('disabled', true);

            try {
                const response = await fetch(`${configCSC.cUrl}/${selectedCountryCode}/states`, {
                    headers: { "X-CSCAPI-KEY": configCSC.ckey }
                });
                const states = await response.json();

                $(stateSelect).empty().append('<option value="">-- Select State --</option>');
                states.forEach(s => {
                    const option = new Option(s.name, s.name);
                    option.dataset.iso2 = s.iso2;
                    $(stateSelect).append(option);
                });

                if (stateName) {
                    await waitForOption(stateSelect, stateName);
                    $(stateSelect).val(stateName).trigger('change');

                    const selectedStateOption = $(stateSelect).find(`option[value="${stateName}"]`);
                    const selectedStateIso2 = selectedStateOption.data('iso2');

                    if (selectedStateIso2) {
                        const hasCities = await stateHasCities(selectedCountryCode, selectedStateIso2);

                        if (!hasCities) {
                            // Jika tidak ada kota: isi "-" dan nonaktifkan dropdown
                            $(citySelect)
                                .empty()
                                .append('<option value="-">-- Not Available --</option>')
                                .prop('disabled', true);
                            $(citySelect).val("-").trigger("change");

                            document.querySelector('.manual-kecamatan').classList.remove('required-field');
                            document.querySelector('.manual-kelurahan').classList.remove('required-field');

                            document.querySelector('.manual-kecamatan').value = "-";
                            document.querySelector('.manual-kelurahan').value = "-";
                            document.querySelector('.manual-kodepos').value = "-";
                            resolve();
                            return;
                        }

                        const cityResponse = await fetch(`${configCSC.cUrl}/${selectedCountryCode}/states/${selectedStateIso2}/cities`, {
                            headers: { "X-CSCAPI-KEY": configCSC.ckey }
                        });
                        const cities = await cityResponse.json();

                        $(citySelect).empty().append('<option value="">-- Select City --</option>');
                        cities.forEach(c => {
                            const option = new Option(c.name, c.name);
                            $(citySelect).append(option);
                        });

                        $(citySelect).prop('disabled', false);

                        if (cityName) {
                            await waitForOption(citySelect, cityName);
                            $(citySelect).val(cityName).trigger('change');
                        }

                        document.querySelector('.manual-kecamatan').classList.add('required-field');
                        document.querySelector('.manual-kelurahan').classList.add('required-field');
                    }
                }

                // Isi input manual
                const kec = modalForm.querySelector(".manual-kecamatan");
                const kel = modalForm.querySelector(".manual-kelurahan");
                const kode = modalForm.querySelector(".manual-kodepos");

                if (kec) kec.value = values["kecamatan[]"] || "";
                if (kel) kel.value = values["kelurahan[]"] || "";
                if (kode) kode.value = values["kode_pos[]"] || "";

                resolve();
            } catch (error) {
                console.error("Error loading states or cities:", error);
                $(stateSelect).empty().append('<option value="">Error loading states</option>');
                resolve();
            }
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

                    option.dataset.flag = `https://flagcdn.com/w20/${country.iso2.toLowerCase()}.png`; // URL bendera
                    countrySelect.appendChild(option);
                });
                // Inisialisasi Select2 setelah isi diubah
                const $select = $(countrySelect);
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2("destroy");
                }
                $select.select2({
                    templateResult: formatCountryOption,
                    templateSelection: formatCountryOption,
                    placeholder: "-- Pilih Negara --",
                    width: "100%",
                    allowClear: true,
                    dropdownParent: $('#modalPencipta')
                });

                $('#nationalityform').on('select2:open', function () {
                    document.querySelector('.select2-search__field').focus();
                });
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
        // Hapus kelas error dari semua elemen input/select
        document.querySelectorAll('.error-field').forEach(el => {
            el.classList.remove('error-field');
        });

        // Hapus kelas error dari semua container Select2
        document.querySelectorAll('.select2-container').forEach(el => {
            el.classList.remove('error-field');
        });

        // Hapus juga dari elemen Select2 yang mungkin memiliki kelas error
        $('.select2-container').removeClass('error-field');

        // Pastikan dropdown negara juga direset
        const nationalitySelect = document.getElementById("nationalityform");
        if (nationalitySelect) {
            nationalitySelect.classList.remove('error-field');
            $(nationalitySelect).next('.select2-container').removeClass('error-field');
        }
    }

    // Fungsi untuk menghandle perubahan nilai field
    function setupFieldChangeHandlers() {
        const modalForm = document.getElementById("modalFormPencipta");

        // Handler untuk input biasa
        modalForm.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('input', function () {
                if (this.value.trim()) {
                    this.classList.remove('error-field');
                }
            });
        });

        // Handler untuk select (baik native maupun Select2)
        const handleSelectChange = function (select) {
            if (select.value) {
                select.classList.remove('error-field');
                const select2Container = $(select).next('.select2-container');
                if (select2Container.length) {
                    select2Container.removeClass('error-field');
                }
            }
        };

        modalForm.querySelectorAll('select').forEach(select => {
            // Handler untuk perubahan native
            select.addEventListener('change', function () {
                handleSelectChange(this);
            });

            // Handler khusus untuk Select2
            if ($(select).hasClass('select2-hidden-accessible')) {
                $(select).on('select2:select select2:unselect select2:clear', function (e) {
                    handleSelectChange(this);
                });
            }
        });

        // Tambahkan handler khusus untuk dropdown negara
        const nationalitySelect = document.getElementById("nationalityform");
        if (nationalitySelect) {
            // Handler untuk perubahan native
            nationalitySelect.addEventListener('change', function () {
                handleSelectChange(this);
            });

            // Handler untuk Select2
            $(nationalitySelect).on('select2:select select2:unselect select2:clear', function (e) {
                handleSelectChange(this);
            });
        }
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

        // Reset dropdown jenis kelamin
        const jenisKelaminSelect = $('#jenis_kelamin');
        jenisKelaminSelect.val('').trigger('change');

        // Paksa update placeholder Select2
        jenisKelaminSelect.select2({
            placeholder: "-- Pilih Jenis Kelamin --",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: Infinity,
            dropdownParent: $('#modalPencipta')
        });
        setupFieldChangeHandlers();

        // Hapus semua error field sebelum memulai
        clearErrorFields();

        // Load ulang dropdown negara
        loadCountriesForModalForm();

        // Reset nilai manual untuk non-Indonesia form
        document.querySelector('.manual-kecamatan').value = "";
        document.querySelector('.manual-kelurahan').value = "";
        document.querySelector('.manual-kodepos').value = "";

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

        // Re-inisialisasi Select2 untuk dropdown Indonesia
        initializeSelect2(provinsiSelect, "-- Pilih Provinsi --");
        initializeSelect2(kabupatenSelect, "-- Pilih Kabupaten/Kota --");
        initializeSelect2(kecamatanSelect, "-- Pilih Kecamatan --");
        initializeSelect2(kelurahanSelect, "-- Pilih Kelurahan --");

        // Kosongkan dan disable dropdown dependen
        $(provinsiSelect).empty().append('<option value="">-- Pilih Provinsi --</option>');
        $(kabupatenSelect).empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>').prop('disabled', true);
        $(kecamatanSelect).empty().append('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
        $(kelurahanSelect).empty().append('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
        if (kodeposInput) kodeposInput.value = "";

        // Reset dropdown non-Indonesia
        const stateSelect = document.querySelector(".state");
        const citySelect = document.querySelector(".city");
        if (stateSelect) stateSelect.innerHTML = '<option value="">-- Select State --</option>';
        if (citySelect) citySelect.innerHTML = '<option value="">-- Select City --</option>';
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
        }).on('select2:select select2:unselect select2:clear', function (e) {
            // Langsung tangani perubahan Select2 saat inisialisasi
            if (this.value) {
                this.classList.remove('error-field');
                $(this).next('.select2-container').removeClass('error-field');
            }
        });

        $('.auto-search').on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });
    }
    setupFieldChangeHandlers();

    // Ekspor fungsi ke konteks global
    window.openModalForEdit = openModalForEdit;
}