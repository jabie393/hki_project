<?php
include 'config/config.php';
session_start();

// Jika user sudah login, arahkan ke dashboard
$dashboardPage = isset($_SESSION['user_username']) ? 'dashboard' : 'login';

$result = $conn->query("SELECT * FROM announcement");
$images = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hak Kekayaan Intelektual</title>
  <link rel="shortcut icon" href="assets/icons/fcompany.png" type="image/x-icon">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Css -->
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/header&footer.css">
  <link rel="stylesheet" href="css/modal_announcement.css">

  <!-- bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <!-- Background Image -->
  <div class="background-image">
    <img src="assets/images/bg.png" alt="Background image of a university building" />
    <div class="bg-overlay"></div>
  </div>

  <div class="header"></div>

  <h1>Hak Kekayaan Intelektual</h1>
  <div class="menu">
    <button onclick="location.href='rekapitulasi'">REKAPITULASI</button>
    <button onclick="location.href='<?php echo $dashboardPage; ?>'">PENGAJUAN HAK CIPTA</button>
    <a href="assets/guide/USER MANUAL HAK CIPTA UNIRA MALANG.pdf" download>
  <button type="button">PETUNJUK PENGAJUAN</button>
</a>
  </div>

  <div class="footer"></div>

  <!-- Modal Pop-up -->
  <div id="announcementModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Pengumuman</h2>
      </div>

      <div class="modal-body">
        <?php if (count($images) > 1): ?>
          <div id="carouselPengumuman" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
              <?php foreach ($images as $index => $image): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <img src="<?= $image['image_path'] ?>" class="d-block w-100" alt="Pengumuman">
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else: ?>
          <img src="<?= $images[0]['image_path'] ?? 'uploads/announcement/default.png' ?>" class="w-100">
        <?php endif; ?>
      </div>

      <!-- Footer dengan desain lebih menarik -->
      <div class="modal-footer">
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselPengumuman" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="close-btn" onclick="closeModal()">Tutup</button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselPengumuman" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </div>

  <script src="js/index.js"></script>
</body>

</html>