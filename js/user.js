// Option
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

// Tambahkan pencipta baru
document.getElementById("addPencipta").addEventListener("click", function () {
    let original = document.querySelector(".pencipta");
    let clone = original.cloneNode(true);

    // Reset input pada clone
    clone.querySelectorAll("input, select, textarea").forEach(input => input.value = "");

    // Perbarui label pencipta
    let penciptaList = document.getElementById("pencipta-list");
    let penciptaCount = penciptaList.children.length + 1;
    let label = clone.querySelector(".pencipta-label");

    if (!label) {
        label = document.createElement("h4");
        label.classList.add("pencipta-label");
        clone.insertBefore(label, clone.firstChild);
    }
    label.textContent = "Pencipta " + penciptaCount;

    // Tambahkan tombol hapus jika belum ada
    let deleteButton = clone.querySelector(".hapusPencipta");
    if (!deleteButton) {
        deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.classList.add("hapusPencipta");
        deleteButton.textContent = "Hapus";
        deleteButton.onclick = function () {
            clone.remove();
            updatePenciptaLabels();
        };
        clone.appendChild(deleteButton);
    }

    penciptaList.appendChild(clone);
    updatePenciptaLabels();
});

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

// Country
function loadCountries() {
    fetch("https://restcountries.com/v3.1/all")
        .then(response => response.json())
        .then(data => {
            data.sort((a, b) => a.name.common.localeCompare(b.name.common));
            const selects = document.querySelectorAll("#nationality, .negara-select");
            selects.forEach(select => {
                select.innerHTML = '<option value="">-- Pilih Negara --</option>';
                data.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.name.common;
                    option.textContent = country.name.common;
                    select.appendChild(option);
                });
            });
        })
        .catch(error => console.error("Gagal memuat data negara:", error));
}

