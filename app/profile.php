<?php

session_start();



// 1. 权限检查

if (empty($_SESSION['user_email'])) {

    header('Location: /login.php');

    exit;

}



require '_base.php';



// 2. 加载用户数据 (JSON 模式)

$usersFile = __DIR__ . '/users.json';

$users = [];

if (file_exists($usersFile)) {

    $raw = file_get_contents($usersFile);

    $users = json_decode($raw, true) ?: [];

}



// 3. 查找当前用户索引

$curIndex = null;

foreach ($users as $i => $u) {

    if (isset($u['email']) && strcasecmp($u['email'], $_SESSION['user_email']) === 0) {

        $curIndex = $i;

        break;

    }

}



if ($curIndex === null) {

    session_unset();

    session_destroy();

    header('Location: /login.php');

    exit;

}



$user = $users[$curIndex];

$messages = [];

$errors = [];



// 4. 处理表单提交

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';



    // --- 更新个人信息 ---

    if ($action === 'update_profile') {

        $name = trim($_POST['name'] ?? '');

        $email = trim($_POST['email'] ?? '');



        if ($name === '') {

            $errors[] = 'Please enter your name.';

        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $errors[] = 'Please enter a valid email address.';

        }



        if (strcasecmp($email, $user['email']) !== 0) {

            foreach ($users as $i => $u) {

                if ($i !== $curIndex && isset($u['email']) && strcasecmp($u['email'], $email) === 0) {

                    $errors[] = 'Another account already uses that email.';

                    break;

                }

            }

        }



        if (empty($errors)) {

            $users[$curIndex]['name'] = $name;

            $users[$curIndex]['email'] = $email;



            if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {

                $_SESSION['user_email'] = $email;

                $_SESSION['user_name'] = $name;

                $user = $users[$curIndex];

                $messages[] = 'Profile updated successfully.';

            } else {

                $errors[] = 'Failed to save changes.';

            }

        }



        // --- 修改密码 ---

    } elseif ($action === 'change_password') {

        $current = $_POST['current_password'] ?? '';

        $new = $_POST['new_password'] ?? '';

        $new2 = $_POST['new_password2'] ?? '';



        if (!password_verify($current, $user['password_hash'])) {

            $errors[] = 'Current password is incorrect.';

        }

        if (strlen($new) < 6) {

            $errors[] = 'New password must be at least 6 characters.';

        }

        if ($new !== $new2) {

            $errors[] = 'New passwords do not match.';

        }



        if (empty($errors)) {

            $users[$curIndex]['password_hash'] = password_hash($new, PASSWORD_DEFAULT);

            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

            $messages[] = 'Password changed successfully.';

        }



        // --- 上传头像 ---

    } elseif ($action === 'upload_photo') {

        if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {

            $errors[] = 'Please choose a file to upload.';

        } else {

            $f = $_FILES['profile_photo'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            $allowedExts = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif'];



            if (!in_array($f['type'], $allowedTypes)) {

                $errors[] = 'Only JPG, PNG, and GIF images are allowed.';

            } elseif ($f['size'] > 2 * 1024 * 1024) {

                $errors[] = 'File is too large (max 2MB).';

            } else {

                $ext = $allowedExts[$f['type']];

                $safeName = preg_replace('/[^a-z0-9._-]/i', '_', $user['email']);



                $targetDir = __DIR__ . '/images/users';

                if (!is_dir($targetDir)) {

                    mkdir($targetDir, 0755, true);

                }



                $filename = "images/users/" . $safeName . '_' . time() . $ext;

                $fullpath = __DIR__ . '/' . $filename;



                if (move_uploaded_file($f['tmp_name'], $fullpath)) {

                    if (!empty($user['photo']) && file_exists(__DIR__ . '/' . $user['photo'])) {

                        @unlink(__DIR__ . '/' . $user['photo']);

                    }

                    $users[$curIndex]['photo'] = $filename;

                    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

                    $_SESSION['user_photo'] = $filename;

                    $user = $users[$curIndex];

                    $messages[] = 'Profile photo uploaded.';

                } else {

                    $errors[] = 'Failed to save uploaded file.';

                }

            }

        }

    }

}



$_title = 'Profile - VisionX';

$_bodyClass = 'transparent-header-page';

include '_head.php';

?>



<style>

    /* 1. Header 样式修复：强制覆盖 Scroll 后的黑色字体 */

    header.main-header, 

    header.main-header.scrolled {

        background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%) !important;

        color: #fff !important;

        box-shadow: 0 4px 20px rgba(0,0,0,0.2) !important;

        border-bottom: none !important;

    }

    

    header.main-header .logo-img,

    header.main-header.scrolled .logo-img { 

        filter: brightness(0) invert(1) !important; 

    }

    

    /* 修复：只针对顶部导航链接设置为白色 */

    header.main-header nav > a,

    header.main-header.scrolled nav > a,

    header.main-header nav .dropdown > a,

    header.main-header.scrolled nav .dropdown > a { 

        color: #fff !important; 

    }

    

    /* 修复：Hover 效果 - 去除背景色，改为文字变色 (灰色) */

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



    /* 2. 容器样式修复：增加顶部 Margin 防止被 Header 遮挡 */

    .profile-container {

        max-width: 900px;

        margin: 110px auto 40px; 

        padding: 0 20px;

    }



    .profile-header {

        margin-bottom: 30px;

        border-bottom: 2px solid #eee;

        padding-bottom: 15px;

    }



    .profile-header h1 {

        font-size: 2.2rem;

        margin: 0;

        color: #333;

    }



    .message-box {

        padding: 15px;

        border-radius: 8px;

        margin-bottom: 20px;

        font-weight: 500;

    }

    .message-box.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

    .message-box.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    .message-box ul { margin: 0; padding-left: 20px; }



    .profile-layout {

        display: flex;

        gap: 50px;

        flex-wrap: wrap;

    }



    .profile-main { flex: 2; min-width: 300px; }

    .profile-sidebar { flex: 1; min-width: 250px; }



    .card {

        background: #fff;

        border: 1px solid #e0e0e0;

        border-radius: 12px;

        padding: 25px;

        margin-bottom: 25px;

        box-shadow: 0 2px 10px rgba(0,0,0,0.03);

    }



    .card h2 {

        margin-top: 0;

        font-size: 1.3rem;

        margin-bottom: 20px;

        color: #1a1a1a;

        display: flex;

        align-items: center;

        gap: 10px;

    }



    .form-group { margin-bottom: 18px; }

    .form-group label {

        display: block;

        margin-bottom: 6px;

        font-weight: 600;

        color: #555;

        font-size: 0.9rem;

    }

    .form-input {

        width: 100%;

        padding: 10px 12px;

        border: 1px solid #ccc;

        border-radius: 6px;

        transition: border-color 0.2s;

    }

    .form-input:focus {

        border-color: #2b7cff;

        outline: none;

    }



    .btn {

        background: #1a1a1a;

        color: #fff;

        padding: 12px 20px;

        border-radius: 6px;

        border: none;

        cursor: pointer;

        font-weight: 600;

        transition: background 0.2s;

    }

    .btn:hover { background: #333; }



    .avatar-preview {

        width: 100%;

        aspect-ratio: 1/1;

        object-fit: cover;

        border-radius: 12px;

        background: #f0f0f0;

        border: 2px dashed #ccc;

        display: flex;

        align-items: center;

        justify-content: center;

        color: #aaa;

        margin-bottom: 15px;

        overflow: hidden;

    }

    .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }

    

    .back-link {

        color: #666;

        text-decoration: none;

        display: inline-flex;

        align-items: center;

        gap: 5px;

        font-weight: 500;

    }

    .back-link:hover { color: #000; }

</style>



<section class="content profile-container">

    <div class="profile-header">

        <h1>My Profile</h1>

    </div>



    <?php if (!empty($messages)): ?>

        <div class="message-box success">

            <ul><?php foreach ($messages as $m) {

                echo "<li>" . htmlspecialchars($m) . "</li>";

            } ?></ul>

        </div>

    <?php endif; ?>



    <?php if (!empty($errors)): ?>

        <div class="message-box error">

            <ul><?php foreach ($errors as $e) {

                echo "<li>" . htmlspecialchars($e) . "</li>";

            } ?></ul>

        </div>

    <?php endif; ?>



    <div class="profile-layout">

        <div class="profile-main">

            <div class="card">

                <h2><i class="fas fa-user-circle"></i> Account Details</h2>

                <form method="post" action="" novalidate>

                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-group">

                        <label for="name">Full Name</label>

                        <input id="name" name="name" type="text" class="form-input" required value="<?= htmlspecialchars($user['name'] ?? '') ?>">

                    </div>

                    <div class="form-group">

                        <label for="email">Email Address</label>

                        <input id="email" name="email" type="email" class="form-input" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">

                    </div>

                    <button class="btn" type="submit">Save Changes</button>

                </form>

            </div>



            <div class="card">

                <h2><i class="fas fa-lock"></i> Security</h2>

                <form method="post" action="" novalidate>

                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">

                        <label for="current_password">Current Password</label>

                        <input id="current_password" name="current_password" type="password" class="form-input" required>

                    </div>

                    <div class="form-group">

                        <label for="new_password">New Password</label>

                        <input id="new_password" name="new_password" type="password" class="form-input" required minlength="6">

                    </div>

                    <div class="form-group">

                        <label for="new_password2">Confirm New Password</label>

                        <input id="new_password2" name="new_password2" type="password" class="form-input" required>

                    </div>

                    <button class="btn" type="submit">Update Password</button>

                </form>

            </div>

        </div>



        <div class="profile-sidebar">

            <div class="card">

                <h2>Profile Photo</h2>

                <div class="avatar-preview">

                    <?php if (!empty($user['photo']) && file_exists(__DIR__ . '/' . $user['photo'])): ?>

                        <img src="<?= htmlspecialchars($user['photo']) ?>?v=<?= time() ?>" alt="Profile">

                    <?php else: ?>

                        <i class="fas fa-camera" style="font-size: 3rem;"></i>

                    <?php endif; ?>

                </div>



                <form method="post" action="" enctype="multipart/form-data">

                    <input type="hidden" name="action" value="upload_photo">

                    <div class="form-group">

                        <input type="file" name="profile_photo" accept="image/*" class="form-input" style="padding: 6px;">

                    </div>

                    <button class="btn" type="submit" style="width:100%">Upload New Photo</button>

                </form>

            </div>

            

            <div style="text-align:center; margin-top:20px;">

                <a href="/index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>

            </div>

        </div>

    </div>

</section>



<?php require __DIR__ . '/_foot.php'; ?>
