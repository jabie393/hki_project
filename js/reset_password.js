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
