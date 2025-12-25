<?php
session_start();
require '_base.php';

if (isset($_SESSION['user_email'])) {
    header('Location: /index.php');
    exit;
}

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        // Check DB instead of JSON
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            $expiry = time() + (15 * 60); // 15 mins expiry

            // Save OTP to DB
            $update = $db->prepare("UPDATE users SET reset_otp = ?, reset_otp_expiry = ? WHERE id = ?");
            $update->execute([$otp, $expiry, $user['id']]);

            // Send Email
            try {
                $mail = get_mail();
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset OTP - VisionX';
                $mail->isHTML(true);
                $mail->Body = "
                    <h2>Password Reset Request</h2>
                    <p>Your OTP code is: <strong>$otp</strong></p>
                    <p>This code expires in 15 minutes.</p>
                ";
                $mail->send();

                // Save email to session for next step
                $_SESSION['reset_email'] = $email;
                header('Location: verify_otp.php');
                exit;

            } catch (Exception $e) {
                $errors[] = "Failed to send email. Error: " . $mail->ErrorInfo;
            }
        } else {
            // Security: Don't reveal if user doesn't exist, but for UX we might say sent
            $message = "If an account matches that email, an OTP has been sent.";
        }
    }
}

$_title = 'Forgot Password - VisionX';
require __DIR__ . '/_head.php';
?>

<style>
    /* Paste the same CSS styles as in app/login.php here */
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
    .notice-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .notice-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .notice-box ul { margin: 0; padding-left: 20px; }
    .auth-links { margin-top: 15px; text-align: center; font-size: 0.95rem; color: #666; }
    .auth-links a { color: #000; text-decoration: none; font-weight: 600; }
    .auth-links a:hover { text-decoration: underline; }
</style>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header"><h1>REQUEST OTP</h1></div>
        <div class="auth-content">
            <p style="color: #666; margin-bottom: 25px; text-align: center;">
                Enter your email address to receive a verification OTP.
            </p>

            <?php if ($message): ?>
                <div class="notice-box notice-success"><?= $message ?></div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="notice-box notice-error">
                    <ul><?php foreach ($errors as $e) {
                        echo "<li>" . htmlspecialchars($e) . "</li>";
                    } ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" action="" novalidate>
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" name="email" type="email" required class="form-input" placeholder="name@example.com">
                </div>
                <button type="submit" class="btn-auth">SEND OTP</button>
            </form>
            <div class="auth-links"><a href="login.php">Back to Log In</a></div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/_foot.php'; ?>
