<?php
include 'config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "Registrasi berhasil! Silakan <a href='login.html'>login</a>.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
