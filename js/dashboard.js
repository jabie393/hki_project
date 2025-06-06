// Modal Profile (Navbar)
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal-profile');
    const modalContent = document.getElementById('userProfileContent');

    document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('profileimg')) {
            const userId = e.target.getAttribute('data-user-id');

            fetch(`modal_user_profile.php?id=${userId}`)
                .then(res => res.text())
                .then(html => {
                    modalContent.innerHTML = html;
                    modal.style.display = 'flex';

                    // Setelah konten dimuat
                    if (typeof refreshAllProfilePics === 'function') {
                        refreshAllProfilePics();
                    }
                })
                .catch(err => {
                    modalContent.innerHTML = '<p style="color:red;">Gagal memuat data profil.</p>';
                    modal.style.display = 'flex';
                });
        }

        if (e.target.id === 'close-modal' || e.target.id === 'modal-profile') {
            modal.style.display = 'none';
        }
    });
});
