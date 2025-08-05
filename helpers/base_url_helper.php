<?php
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    // Ambil root project (tanpa subfolder)
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $basePath = preg_replace('#/services.*$#', '', dirname($scriptName)); // hapus /services dan setelahnya
    define('BASE_URL', $protocol . $host . $basePath);
}
?>