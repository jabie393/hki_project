<!-- ALL -->
<?php
session_start();

// session role yang menunjukkan peran pengguna
$dashboardPage = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'admin.php' : 'user.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="/hki_project/css/login.css">
</head>

<body>
    <!-- Background Image -->
    <div class="background-image">
        <img src="/hki_project/assets/image/bg.png" alt="Background image of a university building">
    </div>

    <!-- Header -->
    <div class="header">
        <div class="logo-container">
            <img src="/hki_project/assets/image/logo.png" alt="University logo">
        </div>
    </div>

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

            <!-- Flash Message Permanen -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="mt-2 mb-4 text-red-500 text-sm">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_message']; ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <button type="submit" class="btn-masuk">Masuk</button>
        </form>
        <div class="register-link">Belum Punya Akun? <a href="register.php">Daftar</a></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="rekapitulasi.php">REKAPITULASI</a>
        <a href="index.php">BERANDA</a>
        <a href="<?php echo $dashboardPage; ?>">PENGAJUAN HKI</a>
    </div>

</body>

</html>