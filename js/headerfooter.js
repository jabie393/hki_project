        // Memuat header dan footer secara dinamis
        fetch("widgets/header.html")
            .then(response => response.text())
            .then(data => document.getElementById("header").innerHTML = data);

        fetch("widgets/footer.html")
            .then(response => response.text())
            .then(data => document.getElementById("footer").innerHTML = data);