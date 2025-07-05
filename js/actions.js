//== Action dropdown ==//
function toggleDropdown(btn) {
    const dropdown = btn.nextElementSibling;
    dropdown.classList.toggle("show");
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdown) menu.classList.remove("show");
    });
}
window.addEventListener("click", function (e) {
    if (!e.target.closest(".action-dropdown")) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove("show");
        });
    }
});
function handleAction(action, id) {
    alert(`Aksi "${action}" pada ID: ${id}`);
}

//===== Action Primary Buttons =====//
//=== actions/reject.php (admin) ===//
document.querySelectorAll('#reject-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const rowId = this.dataset.row;
        const rowElement = document.getElementById(rowId);

        Swal.fire({
            title: 'Yakin ingin menolak?',
            html: `
                    <p>Status pengajuan akan menjadi 'Ditolak' selama 7 hari!</p>
                    <div class="swal2-file-wrapper">
                        <input type="file" id="rejectFile" class="swal2-file-input">
                        <label id="customFileBtn" class="swal2-file-btn">
                            Pilih File Penolakan
                        </label>
                    <span id="fileName" class="swal2-name-text">Tidak ada file yang dipilih</span>
                    </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, tolak!',
            cancelButtonText: 'Batal',
            focusConfirm: false,
            preConfirm: () => {
                const fileInput = document.getElementById('rejectFile');
                if (!fileInput.files.length) {
                    Swal.showValidationMessage('File Penolakan wajib diunggah!');
                    return false;
                }
                return fileInput.files[0];
            },
            didOpen: () => {
                const fileInput = document.getElementById('rejectFile');
                const fileBtn = document.getElementById('customFileBtn');
                const fileName = document.getElementById('fileName');
                fileBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', function () {
                    fileName.textContent = this.files.length ? this.files[0].name : 'Tidak ada file yang dipilih';
                });
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const file = result.value;
                const formData = new FormData();
                formData.append('file', file);
                formData.append('id', id);

                // Ambil nomor_pengajuan dari input di baris yang sama
                const row = document.getElementById(rowId);
                if (row) {
                    const nomorPengajuanInput = row.querySelector('input[name="nomor_pengajuan"]');
                    if (nomorPengajuanInput) {
                        formData.append('nomor_pengajuan', nomorPengajuanInput.value);
                    }
                }

                fetch(`actions/reject.php`, {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ditolak',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            if (rowElement) {
                                rowElement.remove();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonText: 'Oke, paham!'
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Gagal menolak pengajuan.', 'error');
                    });
            }
        });
    });
});

//=== actions/delete.php (admin) ===//
document.querySelectorAll('#delete-btn').forEach(button => {
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
                fetch(`actions/delete.php?id=${id}`)
                    .then(res => res.text())
                    .then(response => {
                        if (response.includes("berhasil")) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus',
                                text: 'Hak cipta berhasil dihapus!',
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
                                text: response,
                                showConfirmButton: true,
                                confirmButtonText: 'Oke, paham!'
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

//== actions/review.php (Tombol Tinjau) (admin) ==//
document.querySelectorAll('#review-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const row = this.closest('tr');

        const nomor_pengajuan = row.querySelector("input[name='nomor_pengajuan']").value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nomor_pengajuan', nomor_pengajuan);

        Swal.fire({
            title: 'Tandai sebagai Ditinjau?',
            text: "Status pengajuan akan menjadi 'Ditinjau'.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, tinjau!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses...",
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
                        const startTime = Date.now();

                        xhr.upload.addEventListener("progress", function (e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                const progressBar = document.getElementById("progress-bar");
                                const progressText = document.getElementById("progress-text");

                                progressBar.style.width = `${percentComplete}%`;
                                progressText.textContent = `${percentComplete}%`;
                            }
                        });

                        xhr.open("POST", "actions/review.php", true);
                        xhr.onload = function () {
                            const elapsedTime = Date.now() - startTime;
                            const delay = Math.max(1200 - elapsedTime, 0);

                            setTimeout(() => {
                                Swal.close();
                                Swal.close();
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2000
                                        }).then(() => {
                                            // Update badge status di tabel
                                            const statusTd = row.querySelector('.status-td .badge');
                                            if (statusTd) {
                                                statusTd.textContent = "Ditinjau";
                                                statusTd.className = "badge badge-ditinjau";
                                            }
                                            // Reset file name display & input
                                            const fileNameElement = document.getElementById('file-name-' + id);
                                            if (fileNameElement) fileNameElement.textContent = "Tidak ada file yang dipilih";
                                            // Kosongkan input nomor_sertifikat berdasarkan ID
                                            const nomorSertifikatInput = document.getElementById('nomor_sertifikat_' + id);
                                            if (nomorSertifikatInput) nomorSertifikatInput.value = '';
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Oke!'
                                        });
                                    }
                                } catch (e) {
                                    Swal.fire('Error', 'Terjadi kesalahan: ' + xhr.responseText, 'error');
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

//== actions/approve.php (admin) ==//
document.querySelectorAll('#approve-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const row = this.closest('tr');

        const nomor_pengajuan = row.querySelector("input[name='nomor_pengajuan']").value;
        const nomor_sertifikat = row.querySelector("input[name='nomor_sertifikat']").value;
        const certificateInput = row.querySelector(`#certificate_${id}`);

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nomor_pengajuan', nomor_pengajuan);
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

                        xhr.open("POST", "actions/approve.php", true);
                        xhr.onload = function () {
                            const elapsedTime = Date.now() - startTime;
                            const delay = Math.max(1500 - elapsedTime, 0);

                            setTimeout(() => {
                                Swal.close();

                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2000
                                        }).then(() => {
                                            // Hapus baris dari tabel
                                            if (row) {
                                                row.remove();
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Oke!'
                                        });
                                    }
                                } catch (e) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan: ' + xhr.responseText,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Oke!'
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

//== actions/cancel.php (user) ==//
document.querySelectorAll('#cancel-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const rowId = this.dataset.row;
        const rowElement = document.getElementById(rowId);

        Swal.fire({
            title: 'Yakin ingin membatalkan?',
            text: 'Tindakan ini tidak bisa dibatalkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('actions/cancel.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${encodeURIComponent(id)}`
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dibatalkan!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            if (rowElement) {
                                rowElement.remove();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonText: 'Oke!'
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Oops...', 'Gagal membatalkan pendaftaran.', 'error');
                    });
            }
        });
    });
});

//=== actions/revise.php (user) ===//
document.querySelectorAll('#revise-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        loadContent(`revisi.php?revisi_id=${id}`, function () {
            fetch(`actions/revise.php?id=${id}`) // Mengambil data revisi
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Tunggu negara selesai di-load, baru autofill
                        if (typeof loadCountriesForReviseMainForm === "function") {
                            loadCountriesForReviseMainForm(function () {
                                autofillPengajuanForm(data.data);
                            });
                        } else {
                            autofillPengajuanForm(data.data);
                        }
                    }
                });
        });
    });
});
//===== End Of Action Primary Buttons =====//

//===== Action Secondary Buttons =====//
//== actions/review.php (Tombol Update Tinjauan di tinjau_pengajuan.php beda di alert & delete row logic) (admin) ==//
document.querySelectorAll('#update_review-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const row = this.closest('tr');

        const nomor_pengajuan = row.querySelector("input[name='nomor_pengajuan']").value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nomor_pengajuan', nomor_pengajuan);

        Swal.fire({
            title: 'Perbarui data hak cipta?',
            text: "Data hak cipta akan diperbarui sesuai input!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, perbarui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses...",
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
                        const startTime = Date.now();

                        xhr.upload.addEventListener("progress", function (e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                const progressBar = document.getElementById("progress-bar");
                                const progressText = document.getElementById("progress-text");

                                progressBar.style.width = `${percentComplete}%`;
                                progressText.textContent = `${percentComplete}%`;
                            }
                        });

                        xhr.open("POST", "actions/review.php", true);
                        xhr.onload = function () {
                            const elapsedTime = Date.now() - startTime;
                            const delay = Math.max(1200 - elapsedTime, 0);

                            setTimeout(() => {
                                Swal.close();
                                Swal.close();
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: "Tinjauan berhasil diperbarui.",
                                            showConfirmButton: false,
                                            timer: 2000
                                        }).then(() => {
                                            // Update badge status di tabel
                                            const statusTd = row.querySelector('.status-td .badge');
                                            if (statusTd) {
                                                statusTd.textContent = "Ditinjau";
                                                statusTd.className = "badge badge-ditinjau";
                                            }
                                            // Reset file name display & input
                                            const fileNameElement = document.getElementById('file-name-' + id);
                                            if (fileNameElement) fileNameElement.textContent = "Tidak ada file yang dipilih";
                                            // Kosongkan input nomor_sertifikat berdasarkan ID
                                            const nomorSertifikatInput = document.getElementById('nomor_sertifikat_' + id);
                                            if (nomorSertifikatInput) nomorSertifikatInput.value = '';
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Oke!'
                                        });
                                    }
                                } catch (e) {
                                    Swal.fire('Error', 'Terjadi kesalahan: ' + xhr.responseText, 'error');
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

//== actions/review.php (Tombol Tinjau di manage_rekapitulasi & pengajuan_ditolak ) (admin) ==//
document.querySelectorAll('#manage_review-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const row = this.closest('tr');

        const nomor_pengajuan = row.querySelector("input[name='nomor_pengajuan']").value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nomor_pengajuan', nomor_pengajuan);

        Swal.fire({
            title: 'Tinjau ulang?',
            text: "Status pengajuan akan menjadi 'Ditinjau'.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, tinjau!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses...",
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
                        const startTime = Date.now();

                        xhr.upload.addEventListener("progress", function (e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                const progressBar = document.getElementById("progress-bar");
                                const progressText = document.getElementById("progress-text");

                                progressBar.style.width = `${percentComplete}%`;
                                progressText.textContent = `${percentComplete}%`;
                            }
                        });

                        xhr.open("POST", "actions/review.php", true);
                        xhr.onload = function () {
                            const elapsedTime = Date.now() - startTime;
                            const delay = Math.max(1200 - elapsedTime, 0);

                            setTimeout(() => {
                                Swal.close();
                                Swal.close();
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2000
                                        }).then(() => {
                                            // Update badge status di tabel
                                            const statusTd = row.querySelector('.status-td .badge');
                                            if (statusTd) {
                                                statusTd.textContent = "Ditinjau";
                                                statusTd.className = "badge badge-ditinjau";
                                            }
                                            // Hapus baris dari tabel
                                            if (row) {
                                                row.remove();
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Oke!'
                                        });
                                    }
                                } catch (e) {
                                    Swal.fire('Error', 'Terjadi kesalahan: ' + xhr.responseText, 'error');
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

//=== actions/update_approve.php (admin)===//
document.querySelectorAll('#update_approve-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const row = document.getElementById('row_' + id);

        const nomor_pengajuan = row.querySelector("input[name='nomor_pengajuan']").value;
        const nomor_sertifikat = row.querySelector("input[name='nomor_sertifikat']").value;
        const certificateInput = row.querySelector(`#certificate_${id}`);

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nomor_pengajuan', nomor_pengajuan);
        formData.append('nomor_sertifikat', nomor_sertifikat);

        if (certificateInput && certificateInput.files.length > 0) {
            formData.append('certificate', certificateInput.files[0]);
        }

        Swal.fire({
            title: 'Perbarui data hak cipta?',
            text: "Data hak cipta akan diperbarui sesuai input!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, perbarui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses...",
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
                        const startTime = Date.now();

                        xhr.upload.addEventListener("progress", function (e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                const progressBar = document.getElementById("progress-bar");
                                const progressText = document.getElementById("progress-text");

                                progressBar.style.width = `${percentComplete}%`;
                                progressText.textContent = `${percentComplete}%`;
                            }
                        });

                        xhr.open("POST", "actions/update_approve.php", true);
                        xhr.onload = function () {
                            const elapsedTime = Date.now() - startTime;
                            const delay = Math.max(1200 - elapsedTime, 0);

                            setTimeout(() => {
                                Swal.close();

                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: response.message,
                                            showConfirmButton: false,
                                            timer: 2000
                                        });

                                        // Update sertifikat jika ada file baru
                                        if (response.new_certificate_path) {
                                            const downloadSpan = document.querySelector(`#certificate-download-${id}`);
                                            if (downloadSpan) {
                                                downloadSpan.innerHTML = `
                                                    <a href="${response.new_certificate_path}" class="btn btn-download" download>Download</a>
                                                `;
                                            }
                                            // Reset file name display & input
                                            const fileNameElement = document.getElementById('file-name-' + id);
                                            if (fileNameElement) fileNameElement.textContent = "Tidak ada file yang dipilih";
                                            if (certificateInput) certificateInput.value = '';
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Oke!'
                                        });
                                    }
                                } catch (e) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan: ' + xhr.responseText,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Oke!'
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

//=== actions/update_reject.php (admin) ===//
document.querySelectorAll('#update_reject-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const rowId = this.dataset.row;

        Swal.fire({
            title: 'Perbarui data hak cipta?',
            html: `
                    <p>Data hak cipta akan diperbarui sesuai input!</p>
                    <div class="swal2-file-wrapper">
                        <input type="file" id="rejectFile" class="swal2-file-input">
                        <label id="customFileBtn" class="swal2-file-btn">
                            Update File Penolakan (Opsional)
                        </label>
                    <span id="fileName" class="swal2-name-text">Tidak ada file yang dipilih</span>
                    </div>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, perbarui!',
            cancelButtonText: 'Batal',
            focusConfirm: false,
            preConfirm: () => {
                const fileInput = document.getElementById('rejectFile');
                // Tidak wajib, jadi langsung return file jika ada, atau null jika tidak ada
                return fileInput.files.length ? fileInput.files[0] : null;
            },
            didOpen: () => {
                const fileInput = document.getElementById('rejectFile');
                const fileBtn = document.getElementById('customFileBtn');
                const fileName = document.getElementById('fileName');
                fileBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', function () {
                    fileName.textContent = this.files.length ? this.files[0].name : 'Tidak ada file yang dipilih';
                });
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const file = result.value;
                const formData = new FormData();
                if (file) {
                    formData.append('file', file);
                    formData.append('has_new_file', '1');
                } else {
                    formData.append('has_new_file', '0');
                }
                formData.append('id', id);

                // Ambil nomor_pengajuan dari input di baris yang sama
                const row = document.getElementById(rowId);
                if (row) {
                    const nomorPengajuanInput = row.querySelector('input[name="nomor_pengajuan"]');
                    if (nomorPengajuanInput) {
                        formData.append('nomor_pengajuan', nomorPengajuanInput.value);
                    }
                }

                Swal.fire({
                    title: "Memproses...",
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
                        const startTime = Date.now();

                        xhr.upload.addEventListener("progress", function (e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                const progressBar = document.getElementById("progress-bar");
                                const progressText = document.getElementById("progress-text");

                                progressBar.style.width = `${percentComplete}%`;
                                progressText.textContent = `${percentComplete}%`;
                            }
                        });

                        xhr.open("POST", "actions/update_reject.php", true);
                        xhr.onload = function () {
                            const elapsedTime = Date.now() - startTime;
                            const delay = Math.max(1200 - elapsedTime, 0);

                            setTimeout(() => {
                                Swal.close();

                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        });

                                        // Update file penolakan jika ada file baru
                                        if (response.new_rejection_path) {
                                            const container = document.querySelector(`#rejection-container-${id}`);
                                            if (container) {
                                                container.innerHTML = `
                                                    <a href="${response.new_rejection_path}" class="btn btn-download" download>Download</a>
                                                `;
                                            }
                                            // Reset file name display & input jika ada
                                            const fileNameElement = document.getElementById('fileName');
                                            if (fileNameElement) fileNameElement.textContent = "Tidak ada file yang dipilih";
                                            const fileInput = document.getElementById('rejectFile');
                                            if (fileInput) fileInput.value = '';
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message,
                                            showConfirmButton: true,
                                            confirmButtonText: 'Oke, paham!'
                                        });
                                    }
                                } catch (e) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan: ' + xhr.responseText,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Oke, paham!'
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
//===== End Of Action Secondary Buttons =====//