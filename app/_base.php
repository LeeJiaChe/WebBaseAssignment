<?php

ob_start();

// app/_base.php

// Start session only if not already active to avoid duplicate notices on pages that call session_start()
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); // 统一开启 Session
}



// 初始化数据库连接并赋值给 $db

$db = require __DIR__ . '/lib/db.php';



date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    // Cookie format: user_id:token (Base64 encoded)
    $cookie_data = base64_decode($_COOKIE['remember_me']);
    $parts = explode(':', $cookie_data);

    if (count($parts) === 2) {
        $user_id = $parts[0];
        $token_input = $parts[1];

        // Fetch user based on ID
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        // Verify token matches the hash in DB
        if ($user && !empty($user['remember_token']) && password_verify($token_input, $user['remember_token'])) {
            // Log the user in
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_role']  = $user['role'] ?? 'user';
        }
    }
}

function get_mail()
{
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'leedemoweb123@gmail.com';
    $m->Password = 'cmjg wwdg yyms jjeh';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, 'VisionX');

    return $m;
}
