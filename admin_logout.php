<?php
session_start();

// Prevent back button access after logout
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$_SESSION = [];
session_destroy();
header('Location: admin_login.php');
exit;
?>