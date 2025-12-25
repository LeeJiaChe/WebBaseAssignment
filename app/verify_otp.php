<?php
session_start();
require '_base.php';

// Ensure user has started the reset process
if (!isset($_SESSION['reset_email'])) {
    header('Location: reset_password_request.php');
    exit;
}

$errors = [];
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_input = trim($_POST['otp'] ?? '');

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['reset_otp'] === $otp_input) {
        if ($user['reset_otp_expiry'] > time()) {
            // OTP Valid
            $_SESSION['can_reset_password'] = true;
            header('Location: reset_password.php');
            exit;
        } else {
            $errors[] = 'OTP has expired. Please request a new one.';
        }
    } else {
        $errors[] = 'Invalid OTP code.';
    }
}

$_title = 'Verify OTP - VisionX';
require __DIR__ . '/_head.php';
?>

<style>
    /* Paste the same CSS styles here as well */
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
    .btn-auth { background: #1a1a1a; color: white; width: 100%; padding: 14px; border-radius: 8px; border: none; font-size: 1.05rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .btn-auth:hover { background: #000; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
    .notice-box { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem; }
    .notice-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .notice-box ul { margin: 0; padding-left: 20px; }
    .auth-links { margin-top: 15px; text-align: center; font-size: 0.95rem; color: #666; }
</style>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header"><h1>VERIFY OTP</h1></div>
        <div class="auth-content">
            <p style="text-align: center;">Enter the code sent to <strong><?= htmlspecialchars($email) ?></strong></p>

            <?php if (!empty($errors)): ?>
                <div class="notice-box notice-error">
                    <ul><?php foreach ($errors as $e) {
                        echo "<li>" . htmlspecialchars($e) . "</li>";
                    } ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" action="" novalidate>
                <div class="form-group">
                    <label for="otp" class="form-label">OTP Code</label>
                    <input id="otp" name="otp" type="text" required class="form-input" placeholder="123456" maxlength="6" autofocus>
                </div>
                <button type="submit" class="btn-auth">VERIFY & PROCEED</button>
            </form>
            <div class="auth-links"><a href="reset_password_request.php">Resend Code</a></div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/_foot.php'; ?>
