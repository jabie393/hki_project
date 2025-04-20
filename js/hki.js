//script detail profil user
function showProfile(userId) {
    fetch('profile_details.php?id=' + userId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('profileDetails').innerHTML = data;
            document.getElementById('profileModal').style.display = 'flex';
        })
        .catch(error => console.error('Error:', error));
}

function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

//script detail deskripsi
function openDescriptionModal(description) {
    document.getElementById('descriptionDetails').innerText = description;
    document.getElementById('descriptionModal').style.display = 'flex';
}

function closeDescriptionModal() {
    document.getElementById('descriptionModal').style.display = 'none';
}

//script detail pencipta
function openModal(id) {
    fetch('widgets/creator_details.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('creatorDetails').innerHTML = data;
            document.getElementById('creatorModal').style.display = 'flex';
        })
        .catch(error => console.error('Error:', error));
}

function closeModal() {
    document.getElementById('creatorModal').style.display = 'none';
}

//script detail pencipta (rekapitulasi.php)
function showCreator(id) {
    fetch(`widgets/rekapitulasi_creator_details.php?id=${id}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById("creatorDetails").innerHTML = data;
            document.getElementById("creatorModal").style.display = "flex";
        });
}

function closeModal() {
    document.getElementById("creatorModal").style.display = "none";
}