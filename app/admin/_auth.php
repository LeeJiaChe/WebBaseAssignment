<?php
session_start();

// require login and admin role
if (empty($_SESSION['user_email']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    // if not logged in, redirect to login
    header('Location: /login.php');
    exit;
}

// optional: refresh user info from users.json
?>
