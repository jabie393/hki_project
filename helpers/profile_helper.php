<?php
require_once __DIR__ . '/../config/config.php';

function getProfilePicture($user_id, $withTimestamp = true)
{
    $relativePath = "/uploads/users/$user_id/profile/profile.jpg";
    $absoluteFilePath = __DIR__ . "/.." . $relativePath;
    $url = BASE_URL . $relativePath;

    if (!file_exists($absoluteFilePath)) {
        return BASE_URL . "/assets/image/default-avatar.png";
    }

    return $withTimestamp ? "$url?v=" . time() : $url;
}
?>