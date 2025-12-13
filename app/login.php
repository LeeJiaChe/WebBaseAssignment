<?php

session_start();



// 如果已经登录，直接跳转

if (isset($_SESSION['user_email'])) {

    header('Location: /index.php');

    exit;

}



$errors = [];

$email = $_POST['email'] ?? $_GET['email'] ?? '';

$password = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

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

            if (isset($u['email']) && strcasecmp($u['email'], $email) === 0) {

                $found = $u;

                break;

            }

        }



        if ($found && isset($found['password_hash']) && password_verify($password, $found['password_hash'])) {

            $_SESSION['user_email'] = $found['email'];

            $_SESSION['user_name'] = $found['name'] ?? '';

            $_SESSION['user_photo'] = $found['photo'] ?? '';

            $_SESSION['user_role'] = $found['role'] ?? 'member';



            if($_SESSION['user_role'] == 'admin') {

                header('Location: /admin/products.php');

            } else {

                header('Location: /index.php');

            }

            exit;

        }



        $errors[] = 'Incorrect email or password.';

    }

}



$_title = 'Login - VisionX';

$_bodyClass = 'auth-page-body';

require __DIR__ . '/_head.php';

?>



<style>

    /* --- Black Theme for Auth Pages --- */



    /* 1. Main Header (Dark Theme - Fixed & Scrolled) */

    header.main-header, 

    header.main-header.scrolled {

        background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%) !important;

        color: #fff !important;

        box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;

        border-bottom: none !important;

    }

    

    header.main-header .logo-img,

    header.main-header.scrolled .logo-img { 

        filter: brightness(0) invert(1) !important; 

    }

    

    /* 修复：只针对顶部导航链接设置为白色，不影响 Dropdown */

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

    

    header.main-header .icon-button, 

    header.main-header .cart-button,

    header.main-header.scrolled .icon-button, 

    header.main-header.scrolled .cart-button { 

        color: #fff !important; 

    }



    /* 2. Page Background */

    main { 

        padding: 0 !important; 

        margin-top: 0px !important;

        background: #f0f2f5 !important;

        min-height: 100vh;

        display: flex;

        justify-content: center;

        align-items: center;

    }



    /* 3. Auth Box Container */

    .auth-container {

        width: 100%;

        max-width: 450px;

        padding: 20px;

    }



    .auth-box {

        background: #ffffff;

        border-radius: 12px;

        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);

        overflow: hidden;

    }



    /* 4. Auth Header */

    .auth-header {

        background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);

        padding: 25px 30px;

        text-align: center;

        color: white;

        border-radius: 6px;

    }

    .auth-header h1 { margin: 0; font-size: 1.6rem; font-weight: 600; letter-spacing: 1px; }



    /* 5. Auth Content & Inputs */

    .auth-content { padding: 30px; }



    .form-group { margin-bottom: 20px; }

    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 0.9rem; }

    

    .form-input {

        width: 100%;

        padding: 12px 15px;

        border: 2px solid #e0e0e0;

        border-radius: 8px;

        background: #fafafa;

        transition: all 0.3s ease;

        font-size: 1rem;

    }

    .form-input:focus {

        border-color: #000;

        background: #fff;

        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);

        outline: none;

    }



    /* 6. Buttons (Black) */

    .btn-auth {

        background: #1a1a1a;

        color: white;

        width: 100%;

        padding: 14px;

        border-radius: 8px;

        border: none;

        font-size: 1.05rem;

        font-weight: 600;

        cursor: pointer;

        transition: all 0.3s ease;

        box-shadow: 0 4px 15px rgba(0,0,0,0.2);

    }

    .btn-auth:hover {

        background: #000;

        transform: translateY(-2px);

        box-shadow: 0 6px 20px rgba(0,0,0,0.3);

    }



    /* 7. Messages & Links */

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

</style>



<div class="auth-container">

    <div class="auth-box">

        <div class="auth-header">

            <h1>LOGIN TO VISIONX</h1>

        </div>

        <div class="auth-content">

            <?php if (isset($_GET['registered'])): ?>

                <div class="notice-box notice-success">Account created successfully. Please log in.</div>

            <?php endif; ?>

            <?php if (isset($_GET['reset_success'])): ?>

                <div class="notice-box notice-success">Password has been reset. Please log in.</div>

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

                    <input id="email" name="email" type="email" required class="form-input" value="<?= htmlspecialchars($email) ?>" autofocus placeholder="e.g., name@example.com">

                </div>

                <div class="form-group">

                    <label for="password" class="form-label">Password</label>

                    <input id="password" name="password" type="password" required class="form-input" placeholder="Enter your password">

                </div>

                

                <div class="forgot-link">

                    <a href="reset_password_request.php">Forgot Password?</a>

                </div>



                <button type="submit" class="btn-auth">LOG IN</button>

            </form>



            <div class="auth-links">

                Don't have an account? <a href="signUp.php">Sign up now</a>

            </div>

        </div>

    </div>

</div>



<?php require __DIR__ . '/_foot.php'; ?>
