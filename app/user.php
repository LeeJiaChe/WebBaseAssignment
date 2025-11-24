<?php
session_start();

// logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: /login.php');
    exit;
}

// require login
if (empty($_SESSION['user_email'])) {
    header('Location: /login.php');
    exit;
}

$_title = 'User';
require __DIR__ . '/_head.php';
?>

<section class="content">
    <h1>Welcome</h1>
    <p>Hello, <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email']) ?>.</p>
    <p>Your email: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
    <p><a href="/user.php?action=logout">Log out</a></p>
</section>

<?php require __DIR__ . '/_foot.php';
