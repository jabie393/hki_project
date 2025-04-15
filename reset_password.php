<!-- Flow FE -->
<?php
session_start();
include 'config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['new_username'];
    $new_email = $_POST['new_email'];
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;

    $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $email_check->bind_param("si", $new_email, $user_id);
    $email_check->execute();
    $email_check->store_result();

    if ($email_check->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan oleh user lain!'); window.location.href='reset_password.php';</script>";
        exit();
    }
    $email_check->close();

    $username_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $username_check->bind_param("si", $new_username, $user_id);
    $username_check->execute();
    $username_check->store_result();
    if ($username_check->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan oleh user lain!'); window.location.href='reset_password.php';</script>";
        exit();
    }
    $username_check->close();

    $query = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $query->bind_param("ssi", $new_username, $new_email, $user_id);
    $query->execute();

    if ($new_password) {
        $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $query->bind_param("si", $new_password, $user_id);
        $query->execute();
    }

    echo "<script>alert('Data user berhasil diperbarui!'); window.location.href='reset_password.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Custom Style -->
    <link rel="stylesheet" href="css/reset_password.css">
</head>

<body>
    <div class="container">
        <h2>Kelola User</h2>
        <form method="post">
            <div class="mb-3">
                <label for="user_id" class="form-label">Pilih User</label>
                <select id="userSelect" name="user_id" style="width: 100%;">
                    <option value="">Cari User...</option>
                    <?php
                    include 'config/config.php';
                    $result = $conn->query("SELECT id, username FROM users");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['username']}</option>";
                    }
                    ?>
                </select>

            </div>

            <div class="mb-3">
                <label for="new_username" class="form-label">Username</label>
                <input type="text" id="new_username" name="new_username" class="form-control" required
                    autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="new_email" class="form-label">Email</label>
                <input type="email" id="new_email" name="new_email" class="form-control" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru (Opsional)</label>
                <input type="password" name="new_password" class="form-control" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>

        <div class="nav-links mt-4">
            <a href="profile.php">Profil</a> |
            <a href="admin.php">Dashboard</a> |
            <a href="rekap_hki.php">Rekap HKI</a> |
            <a href="announcement.php">Pengumuman</a> |
            <a href="template.php">Template Dokumen</a> |
            <a href="services/logout.php">Logout</a>
        </div>
    </div>

    <!-- jQuery dan Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#userSelect').select2({
                placeholder: "Cari User...",
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true
            });

            // Fokus ke kolom pencarian saat dropdown dibuka
            $('#userSelect').on('select2:open', function () {
                document.querySelector('.select2-search__field').focus();
            });

            $('#userSelect').change(function () {
                var userId = $(this).val();
                if (userId) {
                    $.ajax({
                        url: "services/get_user_data.php",
                        type: "POST",
                        data: { user_id: userId },
                        dataType: "json",
                        success: function (data) {
                            $('#new_username').val(data.username);
                            $('#new_email').val(data.email);
                        }
                    });
                } else {
                    $('#new_username').val('');
                    $('#new_email').val('');
                }
            });
        });
    </script>
</body>

</html>