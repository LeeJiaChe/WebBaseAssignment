<?php
require '_base.php';

// If already logged in, redirect
if (isset($_SESSION['user_email'])) {
    $redirect = $_GET['redirect'] ?? 'index.php';
    header('Location: ' . $redirect);
    exit;
}

$max_attempts = 3;
$lockout_time = 30;
$errors = [];
$is_locked = false;

// Lockout Logic
if (isset($_SESSION['lockout_timestamp'])) {
    $time_passed = time() - $_SESSION['lockout_timestamp'];
    if ($time_passed < $lockout_time) {
        $is_locked = true;
        $remaining = $lockout_time - $time_passed;
        $errors = ["Too many attempts. Please wait $remaining seconds."];
    } else {
        unset($_SESSION['attempt_count']);
        unset($_SESSION['lockout_timestamp']);
    }
}

$email = $_POST['email'] ?? $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // Check if box is checked

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 1) {
        $errors[] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $found = $stmt->fetch();

        if ($found && password_verify($password, $found['password_hash'])) {
            unset($_SESSION['attempt_count']);
            unset($_SESSION['lockout_timestamp']);

            $_SESSION['user_id']    = $found['id'];
            $_SESSION['user_email'] = $found['email'];
            $_SESSION['user_name']  = $found['name'];
            $_SESSION['user_role']  = $found['role'] ?? 'user';

            // --- REMEMBER ME IMPLEMENTATION ---
            if ($remember) {
                // Generate a secure random token
                $token_raw = bin2hex(random_bytes(16));
                $token_hash = password_hash($token_raw, PASSWORD_DEFAULT);

                // Save hash to DB
                $upd = $db->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $upd->execute([$token_hash, $found['id']]);

                // Save ID:Token to Cookie (Expires in 30 days)
                $cookie_value = base64_encode($found['id'] . ':' . $token_raw);
                setcookie('remember_me', $cookie_value, time() + (86400 * 30), "/", "", false, true);
            }
            // ----------------------------------

            $redirect = $_POST['redirect'] ?: $_GET['redirect'] ?: 'index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            if (!isset($_SESSION['attempt_count'])) {
                $_SESSION['attempt_count'] = 0;
            }
            $_SESSION['attempt_count']++;

            if ($_SESSION['attempt_count'] >= $max_attempts) {
                $_SESSION['lockout_timestamp'] = time();
                header("Refresh:0");
                exit;
            }
            $remaining = $max_attempts - $_SESSION['attempt_count'];
            $errors[] = "Incorrect email or password. ($remaining attempts remaining)";
        }
    }
}

$_title = 'Login - VisionX';
require __DIR__ . '/_head.php';
?>

<style>
    /* ... (Keep your existing CSS styles from the original file here) ... */
    header.main-header, header.main-header.scrolled { background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%) !important; color: #fff !important; box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important; border-bottom: none !important; }
    header.main-header .logo-img, header.main-header.scrolled .logo-img { filter: brightness(0) invert(1) !important; }
    header.main-header nav > a, header.main-header.scrolled nav > a, header.main-header nav .dropdown > a, header.main-header.scrolled nav .dropdown > a { color: #fff !important; }
    header.main-header nav > a:hover, header.main-header.scrolled nav > a:hover, header.main-header nav .dropdown > a:hover, header.main-header.scrolled nav .dropdown > a:hover { background: transparent !important; color: #8c8c8c !important; }
    header.main-header .icon-button, header.main-header .cart-button, header.main-header.scrolled .icon-button, header.main-header.scrolled .cart-button { color: #fff !important; }
    main { padding: 0 !important; margin-top: 0px !important; background: #f0f2f5 !important; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
    .auth-container { width: 100%; max-width: 450px; padding: 20px; }
    .auth-box { background: #ffffff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; }
    .auth-header { background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%); padding: 25px 30px; text-align: center; color: white; border-radius: 6px; }
    .auth-header h1 { margin: 0; font-size: 1.6rem; font-weight: 600; letter-spacing: 1px; }
    .auth-content { padding: 30px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 0.9rem; }
    .form-input { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; background: #fafafa; transition: all 0.3s ease; font-size: 1rem; }
    .form-input:focus { border-color: #000; background: #fff; box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1); outline: none; }
    .form-input:disabled { background: #e9ecef; cursor: not-allowed; }
    .btn-auth { background: #1a1a1a; color: white; width: 100%; padding: 14px; border-radius: 8px; border: none; font-size: 1.05rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .btn-auth:hover { background: #000; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
    .btn-auth:disabled { background: #ccc !important; color: #666 !important; cursor: not-allowed; transform: none !important; box-shadow: none !important; }
    .notice-box { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem; }
    .notice-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .notice-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .notice-box ul { margin: 0; padding-left: 20px; }
    .auth-links { margin-top: 15px; text-align: center; font-size: 0.95rem; color: #666; }
    .auth-links a { color: #000; text-decoration: none; font-weight: 600; }
    .auth-links a:hover { text-decoration: underline; }
    .forgot-link { text-align: right; margin-top: -10px; margin-bottom: 20px; font-size: 0.9rem; }
    .forgot-link a { color: #666; text-decoration: none; }
    .forgot-link a:hover { color: #000; text-decoration: underline; }
    
    /* New Checkbox Style */
    .remember-me { display: flex; align-items: center; margin-bottom: 20px; font-size: 0.95rem; color: #333; }
    .remember-me input { width: 18px; height: 18px; margin-right: 10px; accent-color: #000; cursor: pointer; }
</style>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header"><h1>LOGIN TO VISIONX</h1></div>
        <div class="auth-content">
            <?php if (isset($_GET['registered'])): ?>
                <div class="notice-box notice-success">Account created successfully. Please log in.</div>
            <?php endif; ?>
            <?php if (isset($_GET['reset_success'])): ?>
                <div class="notice-box notice-success">Password reset successfully. Please log in.</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="notice-box notice-error">
                    <ul><?php foreach ($errors as $e) {
                        echo "<li>" . htmlspecialchars($e) . "</li>";
                    } ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" action="" novalidate>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" name="email" type="email" required class="form-input" value="<?= htmlspecialchars($email) ?>" <?= empty($email) ? 'autofocus' : '' ?>>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" required class="form-input" <?= $is_locked ? 'disabled' : '' ?>>
                </div>

                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember" value="1">
                    <label for="remember">Remember me</label>
                </div>

                <div class="forgot-link"><a href="reset_password_request.php">Forgot Password?</a></div>
                <button type="submit" class="btn-auth" <?= $is_locked ? 'disabled' : '' ?>>LOG IN</button>
            </form>
            <div class="auth-links">Don't have an account? <a href="signUp.php">Sign up now</a></div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/_foot.php'; ?>
