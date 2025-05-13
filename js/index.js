// header & footer
function loadPartial(selector, file) {
    fetch(file)
        .then(res => res.text())
        .then(html => {
            document.querySelector(selector).innerHTML = html;
        });
}

document.addEventListener("DOMContentLoaded", () => {
    loadPartial(".header", "widgets/header.php");
    loadPartial(".footer", "widgets/footer.php");
});

// modal
document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById('announcementModal');

    if (modal) {
        modal.style.display = "flex";
    }
});

function closeModal() {
    document.getElementById('announcementModal').style.display = 'none';
}