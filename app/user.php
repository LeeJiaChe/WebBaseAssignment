<?php
session_start();

// logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 1. Clear Session
    session_unset();
    session_destroy();

    // 2. DELETE THE REMEMBER ME COOKIE (Crucial!)
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', time() - 3600, '/');
        unset($_COOKIE['remember_me']);
    }

    // 3. Redirect (Use relative path)
    header('Location: login.php');
    exit;
}

// require login
if (empty($_SESSION['user_email'])) {
    header('Location: login.php'); // Relative path
    exit;
}

$_title = 'User';
require __DIR__ . '/_head.php';
?>

<section class="content" style="padding: 40px; text-align: center;">
    <h1>Welcome</h1>
    <p>Hello, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email']) ?></strong>.</p>
    <p>Your email: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
    
    <p style="margin-top: 20px;">
        <a href="user.php?action=logout" class="btn-auth" style="padding: 10px 20px; text-decoration: none; background: #dc3545;">Log out</a>
    </p>
</section>

<?php require __DIR__ . '/_foot.php';
