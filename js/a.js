function loadCSCStatesWithValues(selectedCountryCode, values) {
    return new Promise((resolve) => {
        const stateSelect = document.querySelector(".state");
        const citySelect = document.querySelector(".city");

        const stateName = values["provinsi[]"];
        const cityName = values["kota[]"];

        stateSelect.innerHTML = '<option value="">Loading state...</option>';
        citySelect.innerHTML = '<option value="">Pilih City</option>';
        citySelect.disabled = true;

        fetch(`${configCSC.cUrl}/${selectedCountryCode}/states`, { headers: { "X-CSCAPI-KEY": configCSC.ckey } })
            .then(res => res.json())
            .then(states => {
                stateSelect.innerHTML = '<option value="">-- Pilih State --</option>';
                states.forEach(s => {
                    const opt = document.createElement("option");
                    opt.value = s.name;
                    opt.textContent = s.name;
                    opt.dataset.iso2 = s.iso2;
                    stateSelect.appendChild(opt);
                });

                // 🔴 Set state (nama) dan trigger event change manual
                if (stateName) {
                    stateSelect.value = stateName;
                    stateSelect.dispatchEvent(new Event("change"));
                }

                // 🔴 Karena kita tahu state-nya, load kotanya manual
                const selectedStateOption = Array.from(stateSelect.options).find(opt => opt.value === stateName);
                const selectedStateIso2 = selectedStateOption ? selectedStateOption.dataset.iso2 : null;

                if (selectedStateIso2) {
                    fetch(`${configCSC.cUrl}/${selectedCountryCode}/states/${selectedStateIso2}/cities`, {
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

                            // 🔴 Set city kalau ada
                            if (cityName) {
                                citySelect.value = cityName;
                            }

                            resolve();
                        });
                } else {
                    resolve();
                }
            });
    });
}
