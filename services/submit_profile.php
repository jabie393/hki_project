<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data lama dari database
$query = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$existing_data = $result->fetch_assoc();

// Ambil data dari form, jika kosong pakai NULL
$nama_lengkap = isset($_POST['nama_lengkap']) && $_POST['nama_lengkap'] !== '' ? $_POST['nama_lengkap'] : null;
$no_ktp = isset($_POST['no_ktp']) && $_POST['no_ktp'] !== '' ? $_POST['no_ktp'] : null;
$telephone = isset($_POST['telephone']) && $_POST['telephone'] !== '' ? $_POST['telephone'] : null;
$birth_date = isset($_POST['birth_date']) && $_POST['birth_date'] !== '' ? $_POST['birth_date'] : null;
$gender = isset($_POST['gender']) && $_POST['gender'] !== '' ? $_POST['gender'] : null;
$nationality = isset($_POST['nationality']) && $_POST['nationality'] !== '' ? $_POST['nationality'] : null;
$type_of_applicant = isset($_POST['type_of_applicant']) && $_POST['type_of_applicant'] !== '' ? $_POST['type_of_applicant'] : null;

// Direktori upload
$upload_dir = "../uploads/users/$user_id/profile/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Proses gambar profil (jika ada gambar baru diunggah)
$profile_picture = $existing_data['profile_picture'] ?? null; // Default pakai gambar lama jika ada
if (!empty($_POST['cropped_image'])) {
    $image_data = $_POST['cropped_image'];
    $image_parts = explode(";base64,", $image_data);
    $image_base64 = base64_decode($image_parts[1]);
    $target_file = $upload_dir . "profile.jpg";
    file_put_contents($target_file, $image_base64);
    $profile_picture = "profile.jpg"; // Simpan nama file baru
}

// Cek apakah user_profile sudah ada di database
if ($existing_data) {
    // Update data
    $query = $conn->prepare("UPDATE user_profile SET 
        nama_lengkap = ?, no_ktp = ?, telephone = ?, birth_date = ?, gender = ?, nationality = ?, type_of_applicant = ?, profile_picture = ?
        WHERE user_id = ?");
    $query->bind_param("ssssssssi", $nama_lengkap, $no_ktp, $telephone, $birth_date, $gender, $nationality, $type_of_applicant, $profile_picture, $user_id);
} else {
    // Insert data baru
    $query = $conn->prepare("INSERT INTO user_profile (user_id, nama_lengkap, no_ktp, telephone, birth_date, gender, nationality, type_of_applicant, profile_picture) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("issssssss", $user_id, $nama_lengkap, $no_ktp, $telephone, $birth_date, $gender, $nationality, $type_of_applicant, $profile_picture);
}

// Eksekusi query
if ($query->execute()) {
    echo "Profil berhasil diperbarui!";
    header("Location: ../edit_profile.php");
} else {
    echo "Gagal memperbarui profil.";
}

$query->close();
$conn->close();
?>