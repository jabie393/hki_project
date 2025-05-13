<!-- ALL -->
<?php
include 'config/config.php';
session_start();

// Jika user sudah login, arahkan ke dashboard
if (isset($_SESSION['user_username'])) {
    header("Location: dashboard");
    exit();
}

$result = $conn->query("SELECT * FROM announcement");
$images = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="assets/icon/fcompany.png" type="image/x-icon">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/sign.css">
    <link rel="stylesheet" href="css/header&footer.css">

    <!-- Tailwind & font-awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <!-- Background Image -->
    <div class="background-image">
        <img src="assets/image/bg.png" alt="Background image of a university building">
    </div>

    <div class="header"></div>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Masukkan Akun Anda</h2>
        <form action="services/login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
            </div>

            <button type="submit" class="btn">Masuk</button>
        </form>
        <div class="register-link">Belum Punya Akun? <a href="register">Daftar</a></div>
    </div>

    <div class="footer"></div>
    <script src="js/index.js"></script>

    <!-- SweetAlert Messages -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Login',
                text: '<?= $_SESSION['error_message']; ?>',
                confirmButtonText: 'OK'
            });
        </script>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

</body>
</html>