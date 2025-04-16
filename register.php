<!-- ALL -->
<?php
session_start();

// session role yang menunjukkan peran pengguna
$dashboardPage = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'admin.php' : 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="/hki_project/css/register.css" />
</head>

<body>
    <!-- Background Image -->
    <div class="background-image">
        <img src="/hki_project/assets/image/bg.png" alt="Background image of a university building" />
    </div>

    <!-- Header -->
    <div class="header">
        <div class="logo-container">
            <img src="/hki_project/assets/image/logo.png" alt="University logo" />
        </div>
    </div>

    <!-- Register Form -->
    <div class="login-container">
        <h2>Masukkan Biodata Diri Anda</h2>
        <form action="services/register.php" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan Username Anda" required autocomplete="off" 
                    value="<?= isset($_SESSION['input_username']) ? $_SESSION['input_username'] : ''; ?>" />
            </div>
            <?php if (isset($_SESSION['error_username'])): ?>
                <div class="mt-2 mb-3 text-red-500 text-sm">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_username']; ?>
                </div>
                <?php unset($_SESSION['error_username']); ?>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Masukkan Email Anda" required autocomplete="off" 
                    value="<?= isset($_SESSION['input_email']) ? $_SESSION['input_email'] : ''; ?>" />
            </div>
            <?php if (isset($_SESSION['error_email'])): ?>
                <div class="mt-2 mb-3 text-red-500 text-sm">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_email']; ?>
                </div>
                <?php unset($_SESSION['error_email']); ?>
            <?php endif; ?>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required autocomplete="new-password" />
            </div>

            <button type="submit" class="btn-daftar">Daftar</button>
        </form>
        <div class="register-link">Sudah Punya Akun? <a href="login.php">Masuk</a></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="rekapitulasi.php">REKAPITULASI</a>
        <a href="index.php">BERANDA</a>
        <a href="<?php echo $dashboardPage; ?>">PENGAJUAN HKI</a>
    </div>
</body>

</html>