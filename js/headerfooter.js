        // Memuat header dan footer secara dinamis
        fetch("header.html")
            .then(response => response.text())
            .then(data => document.getElementById("header").innerHTML = data);

        fetch("footer.html")
            .then(response => response.text())
            .then(data => document.getElementById("footer").innerHTML = data);