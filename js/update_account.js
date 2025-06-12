document.getElementById("updateForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    // Validasi username tidak mengandung spasi
    const newUsername = formData.get("new_username");
    if (newUsername.includes(" ")) {
        Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Username tidak boleh mengandung spasi.",
            showConfirmButton: true,
            confirmButtonText: 'Oke, paham!'
        });
        return;
    }

    // Validasi password baru (jika ada)
    const newPassword = formData.get("new_password");
    if (newPassword && (newPassword.length < 8 || /\s/.test(newPassword))) {
        Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Password baru harus minimal 8 karakter dan tidak boleh mengandung spasi.",
            showConfirmButton: true,
            confirmButtonText: 'Oke, paham!'
        });
        return;
    }

    fetch("services/update_account_process.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success && data.message.includes("Username sudah digunakan")) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: "Username sudah digunakan oleh user lain.",
                    showConfirmButton: true,
                    confirmButtonText: 'Oke, paham!'
                });
                return;
            }

            Swal.fire({
                icon: data.success ? "success" : "error",
                title: data.success ? "Berhasil!" : "Gagal!",
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });

            if (data.success) {
                // Ambil nilai username baru dari form
                const newUsername = document.querySelector('[name="new_username"]').value;

                // Update nama di sidebar
                const sidebarName = document.getElementById('sidebar-username');
                if (sidebarName) {
                    sidebarName.textContent = "Halo, " + newUsername + "!";
                }
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Terjadi kesalahan saat memperbarui data.",
                showConfirmButton: true,
                confirmButtonText: 'Oke!'
            });
        });
});