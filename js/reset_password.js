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
                }
            });
        } else {
            $('#new_username').val('');
            $('#new_email').val('');
        }
    });

    // Menangani submit form reset password
    $('#resetUserForm').on('submit', function (e) {
        e.preventDefault(); // Mencegah form submit biasa
        const formData = $(this).serialize(); // Ambil data form

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
                        customClass: {
                            confirmButton: 'swal2-success',  // Kelas kustom untuk tombol sukses
                        },
                        didOpen: () => {
                            const confirmButton = Swal.getConfirmButton();
                            confirmButton.style.backgroundColor = '#28a745';
                            confirmButton.style.borderColor = '#28a745';
                        }
                    }).then(() => {
                        // Reset form jika sukses
                        $('#userSelect').val('').trigger('change');
                        $('#new_username').val('');
                        $('#new_email').val('');
                        $('input[name="new_password"]').val('');

                        // Memuat ulang halaman reset_password.php secara dinamis
                        loadContent('reset_password.php');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                        customClass: {
                            confirmButton: 'swal2-error'
                        },
                        didOpen: () => {
                            const confirmButton = Swal.getConfirmButton();
                            confirmButton.style.backgroundColor = '#dc3545';
                            confirmButton.style.borderColor = '#dc3545';
                        }
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    customClass: {
                        confirmButton: 'swal2-error'
                    },
                    didOpen: () => {
                        const confirmButton = Swal.getConfirmButton();
                        confirmButton.style.backgroundColor = '#dc3545';
                        confirmButton.style.borderColor = '#dc3545';
                    }
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
                <div class="profile-center">
                <img src="${data.profile_picture}" alt="Foto Profil" class="profile-img"
                onerror="this.src='assets/image/default-avatar.png'">
            </div>
                    <p><strong>Username:</strong> ${data.username}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Role:</strong> ${data.role}</p>
                    <hr>
                    <p><strong>Nama Lengkap:</strong> 
                    ${data.nama_lengkap || '-'} 
                    ${data.role === 'admin' ? '<i class="fas fa-check-circle check-icon"></i>' : ''}
                </p>
                    <p><strong>No KTP:</strong> ${data.no_ktp || '-'}</p>
                    <p><strong>Telepon:</strong> ${data.telephone || '-'}</p>
                    <p><strong>Tanggal Lahir:</strong> ${data.birth_date || '-'}</p>
                    <p><strong>Jenis Kelamin:</strong> ${data.gender || '-'}</p>
                    <p><strong>Kewarganegaraan:</strong> ${data.nationality || '-'}</p>
                    <p><strong>Tipe Pemohon:</strong> ${data.type_of_applicant || '-'}</p>
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
