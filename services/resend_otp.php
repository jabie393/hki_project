<?php
include '../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_otp = rand(100000, 999999);

    // Update OTP baru di database
    $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_otp, $email);
    if ($stmt->execute()) {
        sendOTP($email, $new_otp);
        echo "Kode OTP baru telah dikirim!";
    } else {
        echo "Gagal mengirim ulang OTP.";
    }
}

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'HKI Registration');
        $mail->addAddress($email);
        $mail->Subject = "Kode OTP Baru";
        $mail->Body = "Kode OTP Anda yang baru adalah: $otp";

        $mail->send();
    } catch (Exception $e) {
        echo "Email gagal dikirim: {$mail->ErrorInfo}";
    }
}
?>
