<?php
//=== BASE URL UNTUK FOTO PROFIL ===//
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    // Menghapus '/widgets' dari path
    $basePath = str_replace('/widgets', '', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
    define('BASE_URL', $protocol . $host . $basePath);
}

//=== FUNGSI UNTUK MENDAPATKAN FOTO PROFIL ===//
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