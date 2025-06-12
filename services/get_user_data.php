<?php
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $query = $conn->prepare("SELECT username, email, role FROM users WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    }
    $query->close();
}
?>
