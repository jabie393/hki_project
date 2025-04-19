function loadContent(url) {
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memuat halaman: ' + url);
            }
            return response.text();
        })
        .then(data => {
            const container = document.getElementById("content-main");
            container.innerHTML = data;

            // Jalankan ulang semua <script> dalam konten yang dimuat
            const scripts = container.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");

                // Jika script eksternal (pakai src)
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                    newScript.async = false;
                } else {
                    // Jika inline script
                    newScript.textContent = oldScript.textContent;
                }

                document.body.appendChild(newScript);
                oldScript.remove(); // Bersihkan script lama dari kontainer
            });
        })
        .catch(error => {
            console.error('Ada masalah dengan memuat halaman:', error);
            document.getElementById("content-main").innerHTML = "<p>Gagal memuat konten.</p>";
        });
}
