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

            const penciptaDiv = document.createElement("div");
            penciptaDiv.classList.add("pencipta");

            // Ambil data dari form secara manual agar field tersembunyi tetap bisa diabaikan
            const data = {};
            [...modalForm.elements].forEach(input => {
                if (!input.name || input.disabled || input.type === 'submit') return;

                // Abaikan input yang sedang disembunyikan (misal: kodepos luar negeri saat Indonesia aktif)
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
                penciptaDiv.remove();
                updatePenciptaLabels();
            };

            if (editingPencipta) {
                editingPencipta.replaceWith(penciptaDiv);
                editingPencipta = null;
            } else {
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
        modalForm.reset();

        const selectedCountry = values["negara[]"] || "";

        // Tampilkan modal lebih awal agar elemen render
        modal.style.display = "flex";

        loadCountriesForModalForm(selectedCountry).then(() => {
            const nationalitySelect = document.getElementById("nationalityform");
            nationalitySelect.value = selectedCountry;
            nationalitySelect.dispatchEvent(new Event("change")); // Toggle tampilan Indonesia vs non-Indonesia

            const isIndonesia = selectedCountry === "ID";

            // Setelah load negara
            if (isIndonesia) {
                loadIndonesianRegionsWithValues(values).then(() => {
                    // selesai load wilayah Indonesia
                });
            } else {
                loadCSCStatesWithValues(selectedCountry, values).then(() => {
                    // SET INPUT MANUAL NON-INDONESIA
                    const kec = modalForm.querySelector(".manual-kecamatan");
                    const kel = modalForm.querySelector(".manual-kelurahan");
                    const kode = modalForm.querySelector(".manual-kodepos");

                    if (kec) kec.value = values["kecamatan[]"] || "";
                    if (kel) kel.value = values["kelurahan[]"] || "";
                    if (kode) kode.value = values["kode_pos[]"] || "";
                });
            }

            // Set input biasa (nama, nik, telepon, dsb)
            for (const [key, value] of Object.entries(values)) {
                // Lewati yang di-handle manual
                if (["provinsi[]", "kota[]", "kecamatan[]", "kelurahan[]", "kode_pos[]"].includes(key)) {
                    continue;
                }

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


    // Hapus pencipta yang ada
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("hapusPencipta")) {
            let penciptaDiv = event.target.closest(".pencipta");
            penciptaDiv.remove();
            updatePenciptaLabels();
        }
    });

    function loadIndonesianRegionsWithValues(values) {
        return new Promise((resolve) => {
            loadIndonesianRegions(); // Load struktur dropdown dulu

            const provinsiSelect = document.querySelector(".provinsi");
            const kabupatenSelect = document.querySelector(".kabupaten");
            const kecamatanSelect = document.querySelector(".kecamatan");
            const kelurahanSelect = document.querySelector(".kelurahan");

            const prov = values["provinsi[]"];
            const kab = values["kota[]"]; // perhatikan: bukan kabupaten[]
            const kec = values["kecamatan[]"];
            const kel = values["kelurahan[]"];
            const kodepos = values["kode_pos[]"]; // perhatikan: bukan kodepos[]

            setTimeout(() => {
                provinsiSelect.value = prov;
                provinsiSelect.dispatchEvent(new Event("change"));

                setTimeout(() => {
                    kabupatenSelect.value = kab;
                    kabupatenSelect.dispatchEvent(new Event("change"));

                    setTimeout(() => {
                        kecamatanSelect.value = kec;
                        kecamatanSelect.dispatchEvent(new Event("change"));

                        setTimeout(() => {
                            kelurahanSelect.value = kel;
                            kelurahanSelect.dispatchEvent(new Event("change"));

                            setTimeout(() => {
                                document.querySelector(".kodepos").value = kodepos;
                                resolve();
                            }, 300);
                        }, 300);
                    }, 300);
                }, 300);
            }, 300);
        });
    }

    function loadCSCStatesWithValues(selectedCountry, values) {
        return new Promise((resolve) => {
            const stateSelect = document.querySelector(".state");
            const citySelect = document.querySelector(".city");

            const state = values["provinsi[]"];
            const city = values["kota[]"];

            // Kosongkan & aktifkan dropdown
            stateSelect.innerHTML = '<option value="">Loading state...</option>';
            citySelect.innerHTML = '<option value="">Pilih City</option>';
            citySelect.disabled = true;

            // 1. Load states
            fetch(`${configCSC.cUrl}/${selectedCountry}/states`, {
                headers: { "X-CSCAPI-KEY": configCSC.ckey }
            })
                .then(res => res.json())
                .then(states => {
                    stateSelect.innerHTML = '<option value="">-- Pilih State --</option>';
                    states.forEach(s => {
                        const opt = document.createElement("option");
                        opt.value = s.iso2;
                        opt.textContent = s.name;
                        stateSelect.appendChild(opt);
                    });

                    // Set selected state
                    if (state) {
                        stateSelect.value = state;
                        stateSelect.dispatchEvent(new Event("change"));
                    }

                    // 2. Setelah state dipilih, load city
                    stateSelect.addEventListener("change", function onStateChange() {
                        const stateCode = this.value;
                        if (!stateCode) return;

                        // hanya panggil sekali
                        stateSelect.removeEventListener("change", onStateChange);

                        fetch(`${configCSC.cUrl}/${selectedCountry}/states/${stateCode}/cities`, {
                            headers: { "X-CSCAPI-KEY": configCSC.ckey }
                        })
                            .then(res => res.json())
                            .then(cities => {
                                citySelect.innerHTML = '<option value="">-- Pilih City --</option>';
                                cities.forEach(c => {
                                    const opt = document.createElement("option");
                                    opt.value = c.name;
                                    opt.textContent = c.name;
                                    citySelect.appendChild(opt);
                                });

                                citySelect.disabled = false;

                                if (city) {
                                    citySelect.value = city;
                                }

                                resolve(); // semua selesai
                            });
                    });
                });
        });
    }

    const indonesiaKeyword = "ID";

    // Handler negara
    document.getElementById("nationalityform").addEventListener("change", function () {
        const selectedCountry = this.value;
        const isIndonesia = selectedCountry === "ID";

        const indoForm = document.getElementById("indonesia-form");
        const nonIndoForm = document.getElementById("non-indonesia-form");

        if (isIndonesia) {
            indoForm.style.display = "block";
            nonIndoForm.style.display = "none";

            // Enable dropdown, disable manual input
            indoForm.querySelectorAll("select, input").forEach(el => el.disabled = false);
            nonIndoForm.querySelectorAll("input").forEach(el => el.disabled = true);

            loadIndonesianRegions();
        } else {
            indoForm.style.display = "none";
            nonIndoForm.style.display = "block";

            // Enable manual input, disable dropdown
            nonIndoForm.querySelectorAll("input").forEach(el => el.disabled = false);
            indoForm.querySelectorAll("select, input").forEach(el => el.disabled = true);

            loadCSCStates(selectedCountry);
        }
    });


    function loadIndonesianRegions() {
        const provinsiSelect = document.querySelector(".provinsi");
        const kabupatenSelect = document.querySelector(".kabupaten");
        const kecamatanSelect = document.querySelector(".kecamatan");
        const kelurahanSelect = document.querySelector(".kelurahan");
        const kodeposInput = document.querySelector(".kodepos");

        // Reset semua dropdown
        provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
        kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
        kodeposInput.value = '';

        // Load Provinsi
        fetch("https://ibnux.github.io/data-indonesia/provinsi.json")
            .then(res => res.json())
            .then(data => {
                data.forEach(prov => {
                    const option = document.createElement("option");
                    option.value = prov.nama; // Ubah dari prov.id
                    option.textContent = prov.nama;
                    option.dataset.id = prov.id; // tetap simpan ID jika dibutuhkan
                    provinsiSelect.appendChild(option);
                });
            });

        // Load Kabupaten
        provinsiSelect.addEventListener("change", () => {
            const provId = provinsiSelect.options[provinsiSelect.selectedIndex].dataset.id;
            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            kodeposInput.value = '';

            fetch(`https://ibnux.github.io/data-indonesia/kabupaten/${provId}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kab => {
                        const option = document.createElement("option");
                        option.value = kab.nama;
                        option.textContent = kab.nama;
                        option.dataset.id = kab.id;
                        kabupatenSelect.appendChild(option);
                    });
                });
        });

        // Load Kecamatan
        kabupatenSelect.addEventListener("change", () => {
            const kabId = kabupatenSelect.options[kabupatenSelect.selectedIndex].dataset.id;
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            kodeposInput.value = '';

            fetch(`https://ibnux.github.io/data-indonesia/kecamatan/${kabId}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kec => {
                        const option = document.createElement("option");
                        option.value = kec.nama;
                        option.textContent = kec.nama;
                        option.dataset.id = kec.id;
                        kecamatanSelect.appendChild(option);
                    });
                });
        });

        // Load Kelurahan
        kecamatanSelect.addEventListener("change", () => {
            const kecId = kecamatanSelect.options[kecamatanSelect.selectedIndex].dataset.id;
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            kodeposInput.value = '';

            fetch(`https://ibnux.github.io/data-indonesia/kelurahan/${kecId}.json`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(kel => {
                        const option = document.createElement("option");
                        option.value = kel.nama;
                        option.textContent = kel.nama;
                        kelurahanSelect.appendChild(option);
                    });
                });
        });

        // Load Kode Pos
        kelurahanSelect.addEventListener("change", () => {
            const kelurahan = kelurahanSelect.value.trim();
            kodeposInput.value = "Mencari...";

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


    function loadCSCStates(selectedCountry) {
        const stateSelect = document.querySelector(".state");
        const citySelect = document.querySelector(".city");

        stateSelect.innerHTML = '<option value="">Pilih State</option>';
        citySelect.innerHTML = '<option value="">Pilih City</option>';
        stateSelect.disabled = false;
        citySelect.disabled = true;

        fetch(`${configCSC.cUrl}/${selectedCountry}/states`, { headers: { "X-CSCAPI-KEY": configCSC.ckey } })
            .then(res => res.json())
            .then(data => {
                data.forEach(state => {
                    const option = document.createElement("option");
                    option.value = state.iso2;
                    option.textContent = state.name;
                    stateSelect.appendChild(option);
                });
            });

        stateSelect.addEventListener("change", () => {
            const stateCode = stateSelect.value;
            citySelect.innerHTML = '<option value="">Pilih City</option>';
            citySelect.disabled = false;

            fetch(`${configCSC.cUrl}/${selectedCountry}/states/${stateCode}/cities`, { headers: { "X-CSCAPI-KEY": configCSC.ckey } })
                .then(res => res.json())
                .then(data => {
                    data.forEach(city => {
                        const option = document.createElement("option");
                        option.value = city.name;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
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

        return fetch(configCSC.cUrl, { // Tambahkan return di sini
            headers: {
                "X-CSCAPI-KEY": configCSC.ckey
            }
        })
            .then(response => response.json())
            .then(countries => {
                countrySelect.innerHTML = '<option value="">-- Pilih Negara --</option>';
                countries.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.iso2;
                    option.textContent = country.name;

                    // Tandai negara yang dipilih sebelumnya
                    if (country.iso2 === selectedCountry) {
                        option.selected = true;
                    }

                    countrySelect.appendChild(option);
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