<?php
session_start();
header('Content-Type: application/json');
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User belum login.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data lama
$query = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$existing_data = $result->fetch_assoc();

// Ambil data dari form
$nama_lengkap = $_POST['nama_lengkap'] ?? null;
$no_ktp = $_POST['no_ktp'] ?? null;
$telephone = $_POST['telephone'] ?? null;
$birth_date = $_POST['birth_date'] ?? null;
$gender = $_POST['gender'] ?? null;
$nationality = $_POST['nationality'] ?? null;
$type_of_applicant = $_POST['type_of_applicant'] ?? null;

// Direktori upload
$upload_dir = "../uploads/users/$user_id/profile/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Proses gambar profil (jika ada)
$profile_picture = $existing_data['profile_picture'] ?? null;
$new_profile_picture_uploaded = false;

if (!empty($_POST['cropped_image'])) {
    $image_data = $_POST['cropped_image'];
    $image_parts = explode(";base64,", $image_data);
    $image_base64 = base64_decode($image_parts[1]);
    $target_file = $upload_dir . "profile.jpg";
    file_put_contents($target_file, $image_base64);
    $profile_picture = "profile.jpg";
    $new_profile_picture_uploaded = true;
}

// Insert atau Update
if ($existing_data) {
    $query = $conn->prepare("UPDATE user_profile SET
        nama_lengkap = ?, no_ktp = ?, telephone = ?, birth_date = ?, gender = ?, nationality = ?, type_of_applicant = ?, profile_picture = ?
        WHERE user_id = ?");
    $query->bind_param("ssssssssi", $nama_lengkap, $no_ktp, $telephone, $birth_date, $gender, $nationality, $type_of_applicant, $profile_picture, $user_id);
} else {
    $query = $conn->prepare("INSERT INTO user_profile (user_id, nama_lengkap, no_ktp, telephone, birth_date, gender, nationality, type_of_applicant, profile_picture)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("issssssss", $user_id, $nama_lengkap, $no_ktp, $telephone, $birth_date, $gender, $nationality, $type_of_applicant, $profile_picture);
}

// Eksekusi query
if ($query->execute()) {
    // Siapkan URL foto baru (kalau ada upload baru)
    $profile_picture_url = null;
    if ($new_profile_picture_uploaded) {
        // Hapus ../ supaya url relatif dari root web
        $profile_picture_url = str_replace("../", "", $upload_dir) . $profile_picture;
        // Tambahkan timestamp supaya browser tidak cache
        $profile_picture_url .= '?t=' . time();
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Profil berhasil diperbarui!',
        'profile_picture' => $profile_picture_url // <-- balikin URL gambar kalau ada upload
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui profil.']);
}

$query->close();
$conn->close();
?>
