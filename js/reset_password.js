$(document).ready(function () {
    $('#userSelect').select2({
        placeholder: "Cari User...",
        allowClear: true,
        width: '100%',
        dropdownAutoWidth: true
    });

    $('#userSelect').on('select2:open', function () {
        document.querySelector('.select2-search__field').focus();
    });

    $('#userSelect').change(function () {
        var userId = $(this).val();
        if (userId) {
            $.ajax({
                url: "services/get_user_data.php",
                type: "POST",
                data: { user_id: userId },
                dataType: "json",
                success: function (data) {
                    $('#new_username').val(data.username);
                    $('#new_email').val(data.email);

                    // Set role pada Select2 dan ubah placeholder
                    $('#new_role')
                        .val(data.role) // Set nilai role
                        .trigger('change') // Perbarui Select2
                        .select2({
                            placeholder: `Role saat ini: ${data.role}`, // Ubah placeholder
                            allowClear: true,
                            width: '100%',
                            minimumResultsForSearch: Infinity // Nonaktifkan search bar
                        });
                }
            });
        } else {
            $('#new_username').val('');
            $('#new_email').val('');
            $('#new_role')
                .val('') // Reset nilai role
                .trigger('change') // Perbarui Select2
                .select2({
                    placeholder: "Pilih User Terlebih Dahulu", // Kembalikan placeholder default
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });
        }
    });

    $('#new_role').select2({
        placeholder: "Pilih User Terlebih Dahulu",
        allowClear: true,
        width: '100%',
        minimumResultsForSearch: Infinity
    });

    // Menangani submit form reset password
    $('#resetUserForm').on('submit', function (e) {
        e.preventDefault(); // Mencegah form submit biasa
        const formData = $(this).serialize(); // Ambil data form

        // Validasi username tidak mengandung spasi
        const newUsername = $('#new_username').val();
        if (newUsername.includes(' ')) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Username tidak boleh mengandung spasi.',
                showConfirmButton: true,
                confirmButtonText: 'Oke, paham!'
            });
            return;
        }

        // Validasi password baru (jika ada)
        const newPassword = $('input[name="new_password"]').val();
        if (newPassword && (newPassword.length < 8 || /\s/.test(newPassword))) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Password baru harus minimal 8 karakter dan tidak boleh mengandung spasi.',
                showConfirmButton: true,
                confirmButtonText: 'Oke, paham!'
            });
            return;
        }

        // Mengirim data ke server menggunakan AJAX
        $.ajax({
            url: 'services/reset_password_process.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                // Menampilkan SweetAlert sesuai dengan hasil dari server
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: response.message,
                        showConfirmButton: false, // Tidak ada tombol confirm
                        timer: 2000 // Menunggu 2 detik
                    }).then(() => {
                        // Ambil username baru dari form sebelum mengosongkan input
                        const newUsername = $('#new_username').val();
                        const selectedUserId = $('#userSelect').val(); // Ambil ID user yang dipilih

                        // Reset form jika sukses
                        $('#userSelect').val('').trigger('change');
                        $('#new_username').val('');
                        $('#new_email').val('');
                        $('input[name="new_password"]').val('');

                        // Memuat ulang halaman reset_password.php secara dinamis
                        loadContent('reset_password.php');

                        // Update nama di sidebar jika admin mengganti username miliknya sendiri
                        const loggedInUserId = $('#loggedInUserId').val(); // Ambil ID user yang sedang login
                        if (selectedUserId === loggedInUserId) {
                            const sidebarName = document.getElementById('sidebar-username');
                            if (sidebarName) {
                                sidebarName.innerHTML = `Halo, ${newUsername}! <i class="fas fa-check-circle verified-icon" title="Admin"></i>`;
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                        showConfirmButton: true,
                        confirmButtonText: 'Oke!',
                        customClass: {
                            confirmButton: 'swal2-error'
                        },
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    showConfirmButton: true,
                    confirmButtonText: 'Oke!',
                    customClass: {
                        confirmButton: 'swal2-error'
                    },
                });
            }
        });
    });
});

// Tampilkan tombol Detail setelah user dipilih
$('#userSelect').change(function () {
    const userId = $(this).val();
    if (userId) {
        $('#detailButtonWrapper').show();
    } else {
        $('#detailButtonWrapper').hide();
        $('#userDetailContent').html('');
        closeProfileModal();
    }
});

// Ketika tombol "Detail User" diklik
$('#showUserDetailBtn').click(function () {
    const userId = $('#userSelect').val();
    if (!userId) return;

    $.ajax({
        url: 'services/get_user_detail.php',
        type: 'POST',
        data: { user_id: userId },
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                $('#userDetailContent').html(`<p>${data.error}</p>`);
            } else {
                const html = `
                    <div id="modal-page">
                        <div>
                            <div class="profile-center">
                                <img src="${data.profile_picture}" alt="Foto Profil" class="profile-img"
                                    onerror="this.src='assets/image/default-avatar.png'">
                            </div>
                            <p class="profile-row">
                                <span class="profile-label">Username:</span>
                                <span class="profile-value">${data.username}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">Email:</span>
                                <span class="profile-value">${data.email}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">Role:</span>
                                <span class="profile-value">${data.role}</span>
                            </p>
                            <hr>
                            <p class="profile-row">
                                <span class="profile-label">Nama Lengkap:</span>
                                <span class="profile-value">
                                ${data.nama_lengkap || '-'}
                                ${data.role === 'admin' ? '<i class="fas fa-check-circle check-icon"></i>' : ''}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">No KTP:</span>
                                <span class="profile-value">${data.no_ktp || '-'}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">Telepon:</span>
                                <span class="profile-value">${data.telephone || '-'}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">Tanggal Lahir:</span>
                                <span class="profile-value">${data.birth_date || '-'}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">Jenis Kelamin:</span>
                                <span class="profile-value">${data.gender || '-'}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">Kewarganegaraan:</span>
                                <span class="profile-value">${data.nationality || '-'}</span>
                            </p>
                            <p class="profile-row">
                                <span class="profile-label">Tipe Pengaju:</span>
                                <span class="profile-value">${data.type_of_applicant || '-'}</span>
                            </p>
                        </div>
                    </div>
                `;
                $('#userDetailContent').html(html);
            }
            showProfileModal();
        },

        error: function () {
            $('#userDetailContent').html('<p>Terjadi kesalahan saat mengambil detail user.</p>');
            showProfileModal();
        }
    });
});

// Fungsi untuk menampilkan modal
function showProfileModal() {
    $('#userDetailModal')
        .removeClass('modal-hidden')
        .addClass('modal-visible');
}

// Fungsi untuk menyembunyikan modal
function closeProfileModal() {
    $('#userDetailModal')
        .removeClass('modal-visible')
        .addClass('modal-hidden');
}

// Tombol tutup modal (X)
$(document).on('click', '.close', function () {
    closeProfileModal();
});
