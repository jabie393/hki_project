<?php
include 'config/config.php';
session_start();

// Jika user sudah login, arahkan ke dashboard
$dashboardPage = isset($_SESSION['user_username']) ? 'dashboard' : 'login';

$result = $conn->query("SELECT * FROM announcement");
$images = $result->fetch_all(MYSQLI_ASSOC);

// Ambil nomor telepon admin dari database
$phone = '';
$phoneQuery = $conn->query("SELECT consult_phone_code, admin_number FROM consultation_number LIMIT 1");
if ($phoneQuery && $row = $phoneQuery->fetch_assoc()) {
    $phone = $row['consult_phone_code'] . ' ' . $row['admin_number'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang - Hak Cipta UNIRA Malang</title>
    <link rel="shortcut icon" href="assets/icon/fcompany.png" type="image/x-icon">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/header&footer.css">
    <link rel="stylesheet" href="css/about.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Background Image -->
    <div class="background-image">
        <img src="assets/image/bg.png" alt="Gambar latar belakang gedung UNIRA">
        <div class="bg-overlay"></div>
    </div>

    <div class="header"></div>

    <div class="about-container">
        <div class="about-card">
            <h1>Tentang Sistem Hak Cipta UNIRA Malang</h1>

            <div class="about-content">
                <p>
                    Sistem ini merupakan platform digital yang dikembangkan untuk mempermudah sivitas akademika
                    Universitas Islam Raden Rahmat Malang dalam melakukan pengajuan dan pengelolaan Hak Cipta secara
                    online. Sistem ini dikelola oleh Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)
                    melalui Sentra Hak Kekayaan Intelektual (Sentra HKI).
                </p>

                <h2>Fitur Utama</h2>
                <ul>
                    <li>Pengajuan Hak Cipta secara mandiri</li>
                    <li>Upload dokumen persyaratan secara daring</li>
                    <li>Monitoring status pengajuan (Pending, Ditinjau, Terdaftar)</li>
                    <li>Notifikasi digital dan riwayat pengajuan</li>
                    <li>Unduh template dan surat pernyataan</li>
                </ul>

                <h2>Tim Pengembang</h2>
                <p>
                    Aplikasi ini dikembangkan oleh mahasiswa Praktik Kerja Lapangan (PKL) dari Program Studi Teknik
                    Informatika,
                    Universitas Islam Raden Rahmat Malang:
                </p>
                <ul>
                    <li>Mochammad Fahd Wahyu Rajaby (22552021020)</li>
                    <li>Abdul Aziis Arifiyanto (22552021002)</li>
                </ul>

                <div class="contact-info">
                    <h3>Kontak Kami</h3>
                    <p>Email: lppm@uniramalang.ac.id</p>
                    <p>Telepon: <?php echo htmlspecialchars($phone); ?></p>
                    <p>Alamat: Jl. Raya Mojosari No.2, Kepanjen, Malang</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer"></div>

    <script src="js/index.js"></script>
</body>

</html>