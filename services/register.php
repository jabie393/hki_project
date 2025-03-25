<?php
require __DIR__ . '/../vendor/autoload.php';
include '../config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $otp = rand(100000, 999999); // Generate OTP 6 digit

    // Cek apakah email sudah terdaftar
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        echo "Email sudah terdaftar!";
        exit();
    }
    
    $check_email->close();

    // Simpan user dengan OTP ke database
    $query = $conn->prepare("INSERT INTO users (name, email, password, otp, is_verified) VALUES (?, ?, ?, ?, 0)");
    $query->bind_param("ssss", $name, $email, $password, $otp);

    if ($query->execute()) {
        // Ambil ID user yang baru dibuat
        $user_id = $query->insert_id;

        // Buat folder untuk user baru
        $user_folder = "../uploads/users/" . $user_id;
        if (!file_exists($user_folder)) {
            mkdir($user_folder, 0777, true);
            mkdir($user_folder . "/profile", 0777, true); // Untuk foto profil
            mkdir($user_folder . "/files", 0777, true);   // Untuk file lainnya
        }

        // Kirim email OTP ke user
        $mail = new PHPMailer(true);
        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Ganti dengan SMTP provider yang digunakan
            $mail->SMTPAuth   = true;
            $mail->Username   = 'flowhybie@gmail.com'; // Ganti dengan email pengirim
            $mail->Password   = 'bvxm kmuy mzsw mzyf'; // Ganti dengan password email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Set email pengirim dan penerima
            $mail->setFrom('Flowhybie@gmail.com', 'HKI Registration');
            $mail->addAddress($email, $name);

            // Konten email
            $mail->isHTML(true);
            $mail->Subject = 'Kode OTP Verifikasi Email';
            $mail->Body    = "<p>Hai <b>$name</b>,</p>
                              <p>Kode OTP Anda adalah: <b>$otp</b></p>
                              <p>Gunakan kode ini untuk memverifikasi akun Anda.</p>";

            $mail->send();
            echo "Registrasi berhasil! Silakan cek email Anda untuk OTP.";
        } catch (Exception $e) {
            echo "Gagal mengirim email OTP: " . $mail->ErrorInfo;
        }
    } else {
        echo "Gagal mendaftar.";
    }
}
?>
