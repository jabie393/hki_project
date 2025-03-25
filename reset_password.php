<?php
session_start();
include 'config/config.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_name = $_POST['new_name'];
    $new_email = $_POST['new_email'];
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;

    // Cek apakah email baru sudah digunakan oleh user lain
    $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $email_check->bind_param("si", $new_email, $user_id);
    $email_check->execute();
    $email_check->store_result();

    if ($email_check->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan oleh user lain!'); window.location.href='reset_password.php';</script>";
        exit();
    }
    $email_check->close();

    // Update name dan email
    $query = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $query->bind_param("ssi", $new_name, $new_email, $user_id);
    $query->execute();

    // Update password jika diisi
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
    
    <!-- Tambahkan CSS Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    
    <!-- Tambahkan jQuery (Wajib sebelum Select2) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<body>
    <h2>Kelola User</h2>
    <form method="post">
        <label for="user_id">Pilih User:</label>
        <select id="userSelect" name="user_id" required style="width: 100%;">
            <option value="">-- Pilih User --</option>
            <?php
            include 'config/config.php';
            $result = $conn->query("SELECT id, name FROM users");

            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <br><br>

        <label for="new_name">Nama:</label>
        <input type="text" id="new_name" name="new_name" required>

        <br><br>

        <label for="new_email">Email:</label>
        <input type="email" id="new_email" name="new_email" required>

        <br><br>

        <label for="new_password">Password Baru (Opsional):</label>
        <input type="password" name="new_password">

        <br><br>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <!-- Pindahkan script Select2 ke sini setelah jQuery dimuat -->
    <script>
    $(document).ready(function() {
        $('#userSelect').select2({
            placeholder: "Cari User...",
            allowClear: true
        });

        // Ambil data user secara otomatis setelah memilih user
        $('#userSelect').change(function() {
            var userId = $(this).val();
            if (userId) {
                $.ajax({
                    url: "services/get_user_data.php",
                    type: "POST",
                    data: { user_id: userId },
                    dataType: "json",
                    success: function(data) {
                        $('#new_name').val(data.name);
                        $('#new_email').val(data.email);
                    }
                });
            } else {
                $('#new_name').val('');
                $('#new_email').val('');
            }
        });
    });
    </script>
</body>
</html>
