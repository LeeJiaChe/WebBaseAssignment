<?php

session_start();



if (isset($_SESSION['user_email'])) {

    header('Location: /index.php');

    exit;

}



$errors = [];

$email = $_GET['email'] ?? '';

$token = $_GET['token'] ?? '';

$tokenValid = false;

$userIndex = null;



$usersFile = __DIR__ . '/users.json';

$users = file_exists($usersFile) ? (json_decode(file_get_contents($usersFile), true) ?: []) : [];



if (!empty($email) && !empty($token)) {

    foreach ($users as $i => $u) {

        if (isset($u['email']) && strcasecmp($u['email'], $email) === 0) {

            if (isset($u['reset_token']) && hash_equals($u['reset_token'], $token)) {

                if (isset($u['reset_expiry']) && $u['reset_expiry'] > time()) {

                    $tokenValid = true;

                    $userIndex = $i;

                } else {

                    $errors[] = 'This reset link has expired.';

                }

            } else {

                $errors[] = 'Invalid reset token.';

            }

            break;

        }

    }

} else {

    $errors[] = 'Missing email or token required for password reset.';

}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {

    $pass1 = $_POST['password'] ?? '';

    $pass2 = $_POST['confirm_password'] ?? '';



    if (strlen($pass1) < 6) {

        $errors[] = 'New password must be at least 6 characters.';

    }

    if ($pass1 !== $pass2) {

        $errors[] = 'New passwords do not match.';

    }



    if (empty($errors)) {

        $users[$userIndex]['password_hash'] = password_hash($pass1, PASSWORD_DEFAULT);

        unset($users[$userIndex]['reset_token']);

        unset($users[$userIndex]['reset_expiry']);



        if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {

            header('Location: /login.php?reset_success=1');

            exit;

        } else {

            $errors[] = 'Failed to save new password. Please try again.';

        }

    }

}



$_title = 'Set New Password - VisionX';

require __DIR__ . '/_head.php';

?>



<style>

    /* --- Black Theme & Scroll Fix --- */

    header.main-header, 

    header.main-header.scrolled {

        background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%) !important;

        color: #fff !important;

        box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;

        border-bottom: none !important;

    }

    header.main-header .logo-img, header.main-header.scrolled .logo-img { filter: brightness(0) invert(1) !important; }

    

    /* 修复：只针对顶部导航链接设置为白色 */

    header.main-header nav > a,

    header.main-header.scrolled nav > a,

    header.main-header nav .dropdown > a,

    header.main-header.scrolled nav .dropdown > a { 

        color: #fff !important; 

    }

    

    /* 修复：Hover 效果 - 透明背景 + 灰色字体 */

    header.main-header nav > a:hover,

    header.main-header.scrolled nav > a:hover,

    header.main-header nav .dropdown > a:hover,

    header.main-header.scrolled nav .dropdown > a:hover { 

        background: transparent !important; 

        color: #8c8c8c !important;

    }

    

    header.main-header .icon-button, header.main-header .cart-button,

    header.main-header.scrolled .icon-button, header.main-header.scrolled .cart-button { color: #fff !important; }



    main { padding: 0 !important; margin-top: 0px !important; background: #f0f2f5 !important; min-height: 100vh; display: flex; justify-content: center; align-items: center; }

    .auth-container { width: 100%; max-width: 450px; padding: 20px; }

    .auth-box { background: #ffffff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; }

    .auth-header { background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%); padding: 25px 30px; text-align: center; color: white; border-radius: 6px }

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

    .auth-links a { color: #000; text-decoration: none; font-weight: 600; }

    .auth-links a:hover { text-decoration: underline; }

</style>



<div class="auth-container">

    <div class="auth-box">

        <div class="auth-header">

            <h1>SET NEW PASSWORD</h1>

        </div>

        <div class="auth-content">

            <?php if (!empty($errors)): ?>

                <div class="notice-box notice-error">

                    <ul><?php foreach ($errors as $e) {

                        echo "<li>" . htmlspecialchars($e) . "</li>";

                    } ?></ul>

                </div>

            <?php endif; ?>



            <?php if ($tokenValid): ?>

            <form method="post" action="" novalidate>

                <div class="form-group">

                    <label for="password" class="form-label">New Password (Min 6 chars)</label>

                    <input id="password" name="password" type="password" required class="form-input" minlength="6">

                </div>

                <div class="form-group">

                    <label for="confirm_password" class="form-label">Confirm New Password</label>

                    <input id="confirm_password" name="confirm_password" type="password" required class="form-input">

                </div>

                

                <button type="submit" class="btn-auth">UPDATE PASSWORD</button>

            </form>

            <?php else: ?>

                <p style="text-align: center;">Please request a new reset link.</p>

            <?php endif; ?>



            <div class="auth-links">

                <a href="login.php">Back to Log In</a>

            </div>

        </div>

    </div>

</div>



<?php require __DIR__ . '/_foot.php'; ?>
