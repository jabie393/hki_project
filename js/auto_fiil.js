// Fungsi autofill
function autofillPengajuanForm(data) {
    // Jenis Pengajuan
    $('#jenis_pengajuan').val(data.jenis_pengajuan).trigger('change');

    // Jenis Hak Cipta
    $('#jenis_hak_cipta').val(data.jenis_hak_cipta).trigger('change');

    // Tunggu sub_jenis_hak_cipta aktif, lalu set nilainya
    setTimeout(function () {
        $('#sub_jenis_hak_cipta').val(data.sub_jenis_hak_cipta).trigger('change');
    }, 300);

    // Tanggal Pengumuman
    $('[name="tanggal_pengumuman"]').val(data.tanggal_pengumuman);

    // Judul
    $('[name="judul"]').val(data.judul_hak_cipta);

    // Deskripsi
    $('[name="deskripsi"]').val(data.deskripsi);

    // Negara
    $('#nationality').val(data.negara_pengumuman).trigger('change');

    // Tunggu kota ready (karena tergantung negara)
    if (data.negara_pengumuman === 'Indonesia') {
        // Polling: cek setiap 200ms apakah option kota sudah tersedia
        const interval = setInterval(() => {
            const $kota = $('#kota_pengumuman');
            // Cek apakah option dengan value yang diinginkan sudah ada
            if ($kota.find(`option[value="${data.kota_pengumuman}"]`).length > 0) {
                $kota.val(data.kota_pengumuman).trigger('change');
                clearInterval(interval);
            }
        }, 200);
    } else {
        $('#kota_pengumuman_input').val(data.kota_pengumuman);
    }

    // Render pencipta jika ada
    if (data.creators && Array.isArray(data.creators)) {
        const penciptaList = document.getElementById('pencipta-list');
        penciptaList.innerHTML = '';
        data.creators.forEach((creator, idx) => {
            // Buat div pencipta sesuai struktur
            const div = document.createElement('div');
            div.className = 'pencipta';
            div.dataset.form = JSON.stringify({
                "nik[]": creator.nik,
                "nama[]": creator.nama,
                "no_telepon[]": creator.no_telepon,
                "jenis_kelamin[]": creator.jenis_kelamin,
                "alamat[]": creator.alamat,
                "negara[]": creator.negara,
                "provinsi[]": creator.provinsi,
                "kota[]": creator.kota,
                "kecamatan[]": creator.kecamatan,
                "kelurahan[]": creator.kelurahan,
                "kode_pos[]": creator.kode_pos
            });
            div.innerHTML = `
                <h4 class="pencipta-label">Pencipta ${idx + 1}</h4>
                <strong>${creator.nama}</strong><br>
                <button type="button" class="btn editPencipta">Edit</button>
                <button type="button" class="btn hapusPencipta">Hapus</button>
            `;
            // Event edit/hapus
            div.querySelector(".editPencipta").onclick = () => openModalForEdit(div);
            div.querySelector(".hapusPencipta").onclick = () => {
                Swal.fire({
                    title: 'Hapus Pencipta?',
                    text: `Anda yakin ingin menghapus pencipta ${creator.nama}?`,
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
                        div.remove();
                        if (typeof updatePenciptaLabels === "function") updatePenciptaLabels();
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
            penciptaList.appendChild(div);
        });
    }

    // Menampilkan dokumen sebelumnya
    fetch(`services/get_documents.php?registration_id=${data.id}`)
        .then(res => res.json())
        .then(docRes => {
            const fileNameSpan = document.querySelector('.file-name');
            if (docRes.success && docRes.data) {
                fileNameSpan.textContent = docRes.data.file_name;
            } else {
                fileNameSpan.textContent = 'Belum ada dokumen';
            }
        });
}