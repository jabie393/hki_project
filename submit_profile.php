<?php
include 'config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $no_ktp = $_POST['no_ktp'];
    $telephone = $_POST['telephone'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $nationality = $_POST['nationality'];
    $type_of_applicant = $_POST['type_of_applicant'];

    // Cek apakah profil sudah ada
    $cek = $conn->query("SELECT * FROM user_profile WHERE user_id = '$user_id'");

    if ($cek->num_rows > 0) {
        // Update profil
        $sql = "UPDATE user_profile SET 
                nama_lengkap='$nama_lengkap', 
                no_ktp='$no_ktp', 
                telephone='$telephone', 
                birth_date='$birth_date', 
                gender='$gender', 
                nationality='$nationality', 
                type_of_applicant='$type_of_applicant' 
                WHERE user_id='$user_id'";
    } else {
        // Simpan profil baru
        $sql = "INSERT INTO user_profile (user_id, nama_lengkap, no_ktp, telephone, birth_date, gender, nationality, type_of_applicant) 
                VALUES ('$user_id', '$nama_lengkap', '$no_ktp', '$telephone', '$birth_date', '$gender', '$nationality', '$type_of_applicant')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Profil berhasil disimpan!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
