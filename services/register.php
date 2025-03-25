<?php
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        // Ambil ID user yang baru dibuat
        $user_id = $stmt->insert_id;

        // Buat direktori user berdasarkan user_id
        $user_folder = "../uploads/users/" . $user_id;
        if (!file_exists($user_folder)) {
            mkdir($user_folder, 0777, true);
            mkdir($user_folder . "/profile", 0777, true); // Untuk foto profil
            mkdir($user_folder . "/files", 0777, true);   // Untuk file lain
        }

        echo "Registrasi berhasil!";
    } else {
        echo "Gagal mendaftar.";
    }
}
?>
