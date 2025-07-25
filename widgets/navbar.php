<?php
include dirname(__DIR__) . '/config/config.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

$profile_picture_path = "assets/images/default-avatar.png?v=" . time(); // default dengan cache busting

if ($user_id) {
  $custom_path = "uploads/users/$user_id/profile/profile.jpg";
  if (file_exists($custom_path)) {
    $profile_picture_path = $custom_path . '?v=' . time();
  }
}
?>

<!-- NAVBAR -->
<nav>
  <i class='bx bx-menu'></i>
  <input type="checkbox" id="switch-mode" hidden>
  <label for="switch-mode" class="switch-mode"></label>

  <a href="javascript:void(0);" class="profile">
    <img src="<?= $profile_picture_path ?>" class="profileimg profilePic" data-user-id="<?= $user_id ?>"
      style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; cursor: pointer;">
  </a>
</nav>
<!-- NAVBAR -->