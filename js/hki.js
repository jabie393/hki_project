//== Modal ==//
// script detail profil user
function showProfile(userId) {
    fetch('profile_details.php?id=' + userId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('profileDetails').innerHTML = data;
            document.getElementById('profileModal').style.display = 'flex';
        })
        .catch(error => console.error('Error:', error));
}

function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

// script detail deskripsi
function openDescriptionModal(description) {
    document.getElementById('descriptionDetails').innerText = description;
    document.getElementById('descriptionModal').style.display = 'flex';
}

function closeDescriptionModal() {
    document.getElementById('descriptionModal').style.display = 'none';
}

// script detail pencipta
function openModal(id) {
    fetch('widgets/creator_details.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('creatorDetails').innerHTML = data;
            document.getElementById('creatorModal').style.display = 'flex';
        })
        .catch(error => console.error('Error:', error));
}

function closeModal() {
    document.getElementById('creatorModal').style.display = 'none';
}

// script detail pencipta (rekapitulasi.php)
function showCreator(id) {
    fetch(`widgets/rekapitulasi_creator_details.php?id=${id}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById("creatorDetails").innerHTML = data;
            document.getElementById("creatorModal").style.display = "flex";
        });
}

function closeModal() {
    document.getElementById("creatorModal").style.display = "none";
}
//== Modal ==//

//== Ajax (Dashboard & Rekap_hki(Admin)) ==//
// services/approve.php
document.querySelectorAll('.approve-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const row = this.closest('tr');

        const nomor_permohonan = row.querySelector("input[name='nomor_permohonan']").value;
        const nomor_sertifikat = row.querySelector("input[name='nomor_sertifikat']").value;
        const certificateInput = row.querySelector(`#certificate_${id}`);

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nomor_permohonan', nomor_permohonan);
        formData.append('nomor_sertifikat', nomor_sertifikat);

        if (certificateInput && certificateInput.files.length > 0) {
            formData.append('certificate', certificateInput.files[0]);
        }

        Swal.fire({
            title: 'Yakin ingin menyetujui?',
            text: "Data akan diperbarui sebagai 'Terdaftar'",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses Persetujuan...",
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

                        xhr.upload.addEventListener("progress", function (e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                const progressBar = document.getElementById("progress-bar");
                                const progressText = document.getElementById("progress-text");

                                progressBar.style.width = `${percentComplete}%`;
                                progressText.textContent = `${percentComplete}%`;
                            }
                        });

                        xhr.open("POST", "services/approve.php", true);
                        xhr.onload = function () {
                            const elapsedTime = Date.now() - startTime; // Hitung waktu yang telah berlalu
                            const delay = Math.max(1500 - elapsedTime, 0); // Hitung delay agar minimal 1,5 detik

                            setTimeout(() => {
                                Swal.close();

                                if (xhr.responseText.includes("Pengajuan telah disetujui")) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Pengajuan disetujui!',
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then(() => {
                                        loadContent('admin.php');
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan: ' + xhr.responseText
                                    });
                                }
                            }, delay);
                        };

                        xhr.onerror = function () {
                            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                        };

                        xhr.send(formData);
                    }
                });
            }
        });
    });
});

// services/delete_hki.php
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const rowId = this.dataset.row;
        const rowElement = document.getElementById(rowId);

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data dan file terkait akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`services/delete_hki.php?id=${id}`)
                    .then(res => res.text())
                    .then(response => {
                        if (response.includes("berhasil")) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus',
                                text: 'Hak Cipta berhasil dihapus.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Hapus baris dari tabel
                            if (rowElement) {
                                rowElement.remove();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Gagal menghapus data.', 'error');
                    });
            }
        });
    });
});

// Edit Nomor Permohonan
document.querySelectorAll('.edit-nomor-permohonan-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const input = document.querySelector(`#nomor_permohonan_${id}`);
        const value = input.value.trim();

        if (value === "") {
            Swal.fire('Peringatan', 'Nomor Permohonan tidak boleh kosong!', 'warning');
            return;
        }

        fetch('services/edit_nomor_permohonan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&nomor_permohonan=${encodeURIComponent(value)}`
        })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: data.status,
                    title: data.status === 'success' ? 'Berhasil' : 'Gagal',
                    text: data.message,
                    showConfirmButton: false, // Tidak ada tombol confirm
                    timer: 2000 // Menunggu 2 detik
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
            });
    });
});

// Edit Nomor Sertifikat
document.querySelectorAll('.edit-nomor-sertifikat-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const input = document.querySelector(`#nomor_sertifikat_${id}`);
        const value = input.value.trim();

        if (value === "") {
            Swal.fire('Peringatan', 'Nomor Sertifikat tidak boleh kosong!', 'warning');
            return;
        }

        fetch('services/edit_nomor_sertifikat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&nomor_sertifikat=${encodeURIComponent(value)}`
        })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: data.status,
                    title: data.status === 'success' ? 'Berhasil' : 'Gagal',
                    text: data.message,
                    showConfirmButton: false, // Tidak ada tombol confirm
                    timer: 2000 // Menunggu 2 detik
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
            });
    });
});

// Edit Sertifikat
document.querySelectorAll('.edit-certificate-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const input = document.querySelector(`#edit_certificate_${id}`);

        if (!input || input.files.length === 0) {
            Swal.fire('Peringatan', 'Silahkan pilih file sertifikat baru.', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('new_certificate', input.files[0]);

        Swal.fire({
            title: "Mengunggah Sertifikat...",
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

                xhr.upload.addEventListener("progress", function (e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        const progressBar = document.getElementById("progress-bar");
                        const progressText = document.getElementById("progress-text");

                        progressBar.style.width = `${percentComplete}%`;
                        progressText.textContent = `${percentComplete}%`;
                    }
                });

                xhr.open("POST", "services/edit_certificate.php", true);
                xhr.onload = function () {
                    const elapsedTime = Date.now() - startTime; // Hitung waktu yang telah berlalu
                    const delay = Math.max(1500 - elapsedTime, 0); // Hitung delay agar minimal 1,5 detik

                    setTimeout(() => {
                        Swal.close();

                        const response = JSON.parse(xhr.responseText);
                        Swal.fire({
                            icon: response.status,
                            title: response.status === 'success' ? 'Berhasil' : 'Gagal',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });

                        if (response.status === 'success' && response.new_path) {
                            const container = document.querySelector(`#certificate-container-${id}`);
                            if (container) {
                                container.innerHTML = `
                                    <a href="${response.new_path}" class="btn btn-download" download>Download</a>
                                `;
                            }
                        }
                    }, delay);
                };

                xhr.onerror = function () {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengunggah sertifikat.', 'error');
                };

                xhr.send(formData);
            }
        });
    });
});

// Validasi file saat diinput
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function () {
        const allowedExtensions = ['pdf', 'doc', 'docx', 'zip', 'rar', '7z', 'tar', 'gz', 'jpg', 'jpeg', 'png'];
        const file = this.files[0];

        if (file) {
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Sertifikat Tidak Valid',
                    text: `Hanya Sertifikat dengan ekstensi berikut yang diizinkan: ${allowedExtensions.join(', ')}.`,
                });
                this.value = ''; // Reset input file
            }
        }
    });
});
//== Ajax (Dashboard & Rekap_hki(Admin)) ==//

//== Script Ajax (status_pengajuan) ==//
$(document).on('click', '.cancel-btn', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Yakin ingin membatalkan?',
        text: 'Tindakan ini tidak bisa dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, batalkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'services/cancel_hki.php',
                type: 'POST',
                data: { id: id },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Dibatalkan!',
                        text: response,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // Reload tabel dengan data terbaru
                        $('#hki-table').load('status_pengajuan.php #hki-table > *'); 
                    });
                },
                error: function () {
                    Swal.fire('Oops...', 'Gagal membatalkan pendaftaran.', 'error');
                }
            });
        }
    });
});
//== Script Ajax (status_pengajuan) ==//