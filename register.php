<!-- ALL -->
<?php
include 'config/config.php';
session_start();

// Jika user sudah login, arahkan ke dashboard.php
$dashboardPage = isset($_SESSION['user_username']) ? 'dashboard.php' : 'login.php';

$result = $conn->query("SELECT * FROM announcement");
$images = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="shortcut icon" href="assets/icon/fcompany.png" type="image/x-icon">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Css -->
    <link rel="stylesheet" href="css/sign.css">
    <link rel="stylesheet" href="css/header&footer.css">

    <!-- Tailwind & font-awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body>
    <!-- Background Image -->
    <div class="background-image">
        <img src="assets/image/bg.png" alt="Background image of a university building" />
    </div>

    <div class="header"></div>

    <!-- Register Form -->
    <div class="login-container">
        <form action="services/register.php" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan Username Anda" required
                    autocomplete="off"
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
                <input type="email" id="email" name="email" placeholder="Masukkan Email Anda" required
                    autocomplete="off"
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
                <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required
                    autocomplete="new-password" />
            </div>

            <button type="submit" class="btn">Daftar</button>
        </form>
        <div class="register-link">Sudah Punya Akun? <a href="login.php">Masuk</a></div>
    </div>

    <div class="footer"></div>

    <script src="js/index.js"></script>
</body>

</html>