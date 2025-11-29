<?php
session_start();

// Process login before any output so we can redirect on success
$errors = [];
$email = $_POST['email'] ?? $_GET['email'] ?? '';
$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 1) {
        $errors[] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $usersFile = __DIR__ . '/users.json';
        $users = [];
        if (file_exists($usersFile)) {
            $raw = file_get_contents($usersFile);
            $users = json_decode($raw, true) ?: [];
        }

        $found = null;
        foreach ($users as $u) {
            if (strcasecmp($u['email'], $email) === 0) {
                $found = $u;
                break;
            }
        }

        if ($found && password_verify($password, $found['password_hash'])) {
            // Successful login
            $_SESSION['user_email'] = $found['email'];
            $_SESSION['user_name'] = $found['name'] ?? '';
            header('Location: /index.php');
            exit;
        }

        $errors[] = 'Incorrect email or password.';
    }
}

// Page output
$_title = 'Login';
require __DIR__ . '/_head.php';
?>

<style>
/* Page-specific header override: make header visible on light backgrounds */
header.main-header {
    background: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    color: #000;
}
header.main-header nav a { color: #000 !important; }
header.main-header nav a:hover { background: #eeeeee !important; }
header.main-header .icon-button,
header.main-header .cart-button { color: #222 !important; }
header.main-header .logo-img { filter: none !important; }
/* Remove white frame around auth content */
main { padding: 0 !important; background: #f5f6f7; }
</style>

<section class="content auth-page">
    <div class="auth-box">
        <h1>Login</h1>

        <?php if (isset($_GET['registered'])): ?>
            <div class="notice" style="color: #0b6623; margin-bottom: 12px;">Account created — please log in.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="errors" style="color: #b00020; margin-bottom: 12px;">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div>
                <label for="email">Email</label><br>
                <input id="email" name="email" type="email" required class="form-input" value="<?= htmlspecialchars($email) ?>" autofocus>
            </div>
            <div style="margin-top:8px;">
                <label for="password">Password</label><br>
                <input id="password" name="password" type="password" required class="form-input">
            </div>
            <div style="margin-top:12px;">
                <button type="submit" class="btn">Log in</button>
            </div>
        </form>

        <p style="margin-top:12px;">Don't have an account? <a href="/signUp.php">Sign up</a></p>
    </div>
</section>

<?php require __DIR__ . '/_foot.php'; ?>
