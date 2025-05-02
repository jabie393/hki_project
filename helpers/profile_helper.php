<?php
function getProfilePicture($user_id, $withTimestamp = true) {
    $default = "assets/image/default-avatar.png";
    $path = "uploads/users/$user_id/profile/profile.jpg";

    if (!file_exists($path)) {
        return $default;
    }

    return $withTimestamp ? "$path?v=" . time() : $path;
}
