<?php
// logout.php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect to login page
header('location:admin_login.php');
exit();
?>