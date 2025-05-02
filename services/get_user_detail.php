<?php
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $query = $conn->prepare("
        SELECT u.id, u.username, u.email, u.role, 
               p.nama_lengkap, p.no_ktp, p.telephone, p.birth_date, 
               p.gender, p.nationality, p.type_of_applicant
        FROM users u
        LEFT JOIN user_profile p ON u.id = p.user_id
        WHERE u.id = ?
    ");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        // Foto profil
        $relative_path = "uploads/users/{$user_id}/profile/profile.jpg";
        $full_path = "../" . $relative_path;

        if (file_exists($full_path)) {
            $row['profile_picture'] = $relative_path . '?v=' . time();
        } else {
            $row['profile_picture'] = "assets/image/default-avatar.png";
        }

        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'User tidak ditemukan']);
    }

    $query->close();
}
?>