document.addEventListener("DOMContentLoaded", function () {
    const switchMode = document.getElementById("switch-mode");

    // Cek preferensi dark mode dari localStorage
    if (localStorage.getItem("darkMode") === "enabled") {
        document.body.classList.add("dark-mode");
        if (switchMode) switchMode.checked = true;
    }

    // Toggle dark mode saat switch diubah
    if (switchMode) {
        switchMode.addEventListener("change", function () {
            if (this.checked) {
                document.body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "enabled");
            } else {
                document.body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "disabled");
            }
        });
    }
});