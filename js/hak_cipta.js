//== Pagination user.php ==//
function pagination() {
    function initDataTable(selector) {
        $(selector).DataTable({
            dom: '<"dtable-header"lf>rtip',
            scrollX: true,
            autoWidth: false,
            order: [[1, 'desc']],
            language: {
                search: "",
                lengthMenu: "_MENU_",
                paginate: {
                    previous: '&laquo;',
                    next: '&raquo;'
                },
                info: "Menampilkan _START_ sampai _END_ dari total _TOTAL_ hak cipta",
                infoEmpty: "Hak cipta tidak ditemukan",
                infoFiltered: "(Hasil pencarian dari _MAX_ total hak cipta)",
            },
            initComplete: function () {
                console.log("Init table:", this.api().table().node().id);
                $(this).closest('.dataTables_wrapper').find('.dataTables_filter input[type="search"]').attr('placeholder', 'Cari hak cipta...');
            },
        });
    }

    initDataTable('#pendingTable');
    initDataTable('#reviewTable');
    initDataTable('#rejectTable');
    initDataTable('#approvedTable');
};

function initSelect2() {
    // ================== INITIALIZE SELECT2 ================== //
    $('#tahun_pengajuan').select2({
        width: '100%',
        minimumResultsForSearch: 5
    });
    $('#tahun_pengajuan').next('.select2-container').addClass('select2-export');
}

//== Modal ==//
// script detail profil user
function showProfile(userId) {
    fetch('widgets/profile_details.php?id=' + userId)
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

// script detail hak cipta
function openDetailCiptaanModal(id) {
    fetch('widgets/detail_ciptaan.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailCiptaanDetails').innerHTML = data;
            document.getElementById('detailCiptaanModal').style.display = 'flex';
        })
        .catch(error => console.error('Error:', error));
}

function closeDetailCiptaanModal() {
    document.getElementById('detailCiptaanModal').style.display = 'none';
}

// script detail pencipta
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

// script detail pencipta (rekapitulasi.php)
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
//== Modal ==//

//== Validasi file saat diinput ==//
document.querySelectorAll('input[type="file"]').forEach(function (input) {
    input.addEventListener('change', function (e) {
        const allowedExtensions = ['pdf', 'doc', 'docx', 'zip', 'rar', '7z', 'tar', 'gz', 'jpg', 'jpeg', 'png'];
        const file = e.target.files[0];
        const fileId = e.target.id.split('_').pop();
        const fileNameElement = document.getElementById('file-name-' + fileId);

        if (file) {
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Tidak Valid',
                    text: `Hanya file dengan ekstensi berikut yang diizinkan: ${allowedExtensions.join(', ')}.`,
                    showConfirmButton: true,
                    confirmButtonText: 'Oke, paham!'
                });
                e.target.value = ''; // Reset input file
                if (fileNameElement) fileNameElement.textContent = "Tidak ada file yang dipilih";
            } else {
                if (fileNameElement) fileNameElement.textContent = file.name;
            }
        } else {
            if (fileNameElement) fileNameElement.textContent = "Tidak ada file yang dipilih";
        }
    });
});

//== Hide search param from URL after search ==//
document.addEventListener('DOMContentLoaded', function () {
    const url = new URL(window.location.href);
    if (url.searchParams.has('search')) {
        url.searchParams.delete('search');
        window.history.replaceState({}, document.title, url.pathname + url.search);
    }
});
//== End hide search param ==//