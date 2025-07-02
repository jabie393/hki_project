<?php
session_start();
header('Content-Type: application/json');
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User belum login.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';
$isAdmin = ($user_role === 'admin');

// Periksa apakah tabel consultation_number memiliki data
$check_query = $conn->query("SELECT id FROM consultation_number LIMIT 1");
if ($check_query->num_rows === 0) {
    // Tambahkan data dummy jika tabel kosong
    $conn->query("INSERT INTO consultation_number (id, admin_number) VALUES (1, '')");
}

// Ambil data lama
$query = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$existing_data = $result->fetch_assoc();

// Ambil data dari form
function nullIfEmpty($val)
{
    return (isset($val) && trim($val) !== '') ? $val : null;
}
$nama_lengkap = nullIfEmpty($_POST['nama_lengkap'] ?? null);
$no_ktp = nullIfEmpty($_POST['no_ktp'] ?? null);
$phone_code = nullIfEmpty($_POST['phone_code'] ?? null);
$telephone = nullIfEmpty($_POST['telephone'] ?? null);
$consult_phone_code = nullIfEmpty($_POST['consult_phone_code'] ?? null);
$consultation_number = nullIfEmpty($_POST['consultation_number'] ?? null);
$birth_date = nullIfEmpty($_POST['birth_date'] ?? null);
$gender = nullIfEmpty($_POST['gender'] ?? null);
$nationality = nullIfEmpty($_POST['nationality'] ?? null);
$type_of_applicant = nullIfEmpty($_POST['type_of_applicant'] ?? null);

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
        nama_lengkap = ?, no_ktp = ?, telephone = ?, birth_date = ?, gender = ?, nationality = ?, type_of_applicant = ?, profile_picture = ?, phone_code = ?
        WHERE user_id = ?");
    $query->bind_param("sssssssssi", $nama_lengkap, $no_ktp, $telephone, $birth_date, $gender, $nationality, $type_of_applicant, $profile_picture, $phone_code, $user_id);
} else {
    $query = $conn->prepare("INSERT INTO user_profile (user_id, nama_lengkap, no_ktp, telephone, birth_date, gender, nationality, type_of_applicant, profile_picture, phone_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("isssssssss", $user_id, $nama_lengkap, $no_ktp, $telephone, $birth_date, $gender, $nationality, $type_of_applicant, $profile_picture, $phone_code);
}

// Eksekusi query
if ($query->execute()) {
    // Jika user adalah admin, update admin_number
    if ($isAdmin) {
        $admin_query = $conn->prepare("UPDATE consultation_number SET admin_number = ?, consult_phone_code = ? LIMIT 1");
        // Ubah string kosong menjadi NULL
        $admin_number_param = ($consultation_number === null || $consultation_number === '') ? null : $consultation_number;
        $consult_phone_code_param = ($consult_phone_code === null || $consult_phone_code === '') ? null : $consult_phone_code;
        $admin_query->bind_param("ss", $admin_number_param, $consult_phone_code_param);
        $admin_query->execute();
        $admin_query->close();
    }

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