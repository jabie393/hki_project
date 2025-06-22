<!-- ALL -->
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
    <link rel="shortcut icon" href="assets/icon/fcompany.png" type="image/x-icon">

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
        <img src="assets/image/bg.png" alt="Background image of a university building" />
        <div class="bg-overlay"></div>
    </div>

    <div class="header"></div>

    <div class="">
        <h1>Maintenance</h1>
    </div>

    <div class="footer"></div>

    <script src="js/index.js"></script>
</body>

</html>