document.getElementById("updateForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch("services/update_account_process.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            icon: data.success ? "success" : "error",
            title: data.success ? "Berhasil!" : "Gagal!",
            text: data.message,
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Terjadi kesalahan saat memperbarui data."
        });
    });
});