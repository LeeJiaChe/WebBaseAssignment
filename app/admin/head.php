<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}
// load DB for user image lookup
global $db;
if (!isset($db)) {
    $db = require_once __DIR__ . '/../lib/db.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'VISONX' ?></title>
    <link rel="shortcut icon" href="../images/visionX_logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?= '<link rel="stylesheet" href="../css/app.css">' ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/app.js"></script>
<style>
    header.main-header {
        background: black;
    }
</style>
</head>
<body<?= isset($_bodyClass) ? ' class="'.$_bodyClass.'"' : '' ?>>
    <header class="main-header">
        <div class="header-logo">
            <a href="<?= '../index.php' ?>">
                <img src="../images/VisionX.png" alt="VisionX Logo" class="logo-img">
            </a>
        </div>

        <nav class="header-nav">
            <div class="dropdown">
                <a href="javascript:void(0)" class="dropdown-toggle">Brands <span class="arrow">&#9662;</span></a>
                <div class="dropdown-menu">
                    <a href="../canon.php">Canon</a>
                    <a href="../fujifilm.php">FUJIFILM</a>
                    <a href="../dji.php">DJI</a>
                    <a href="../sony.php">Sony</a>
                    <a href="../insta360.php">Insta360</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="javascript:void(0)" class="dropdown-toggle">Categories <span class="arrow">&#9662;</span></a>
                <div class="dropdown-menu">
                    <a href="../mirrorless.php">Mirrorless Cameras</a>
                    <a href="../dslr.php">DSLR Cameras</a>
                    <a href="../action.php">Action Cameras</a>
                    <a href="../drone.php">Drones</a>
                    <a href="../accessories.php">Camera Accessories</a>
                </div>
            </div>

            <a href="../contact.php">Contact</a>

            <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <!-- admin links -->
                <div class="dropdown" style="margin-left:12px;">
                    <a href="javascript:void(0)" class="dropdown-toggle" style="font-weight:700; color:#2b7cff;">Admin Dashboard <span class="arrow">&#9662;</span></a>
                    <div class="dropdown-menu">
                        <a href="/admin/products.php">Products</a>
                        <a href="/admin/members.php">Members</a>
                        <a href="/admin/orders.php">Orders</a>
                    </div>
                </div>
            <?php endif; ?>
        </nav>

        <a href="#" class="cart-button" title="Cart">
            <i class="fas fa-shopping-cart" style="font-size:20px"></i>
            <span id="cartCount" class="hidden">0</span>
        </a>

        <?php
        $isLoggedIn = !empty($_SESSION['user_email']);
if ($isLoggedIn) {
    $photo = '';
    // try SQL first
    if (isset($db) && !empty($_SESSION['user_email'])) {
        try {
            $stmt = $db->prepare('SELECT photo FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $_SESSION['user_email']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['photo'])) {
                $photo = $row['photo'];
            }
        } catch (Exception $e) {
            // ignore DB errors and fallback to file
        }
    }
    // fallback to users.json (legacy)
    if (!$photo) {
        $usersFile = __DIR__ . '/../users.json';
        if (file_exists($usersFile)) {
            $raw = file_get_contents($usersFile);
            $users = json_decode($raw, true) ?: [];
            foreach ($users as $u) {
                if (isset($u['email']) && strcasecmp($u['email'], $_SESSION['user_email']) === 0) {
                    $photo = $u['photo'] ?? '';
                    break;
                }
            }
        }
    }
    ?>
            <div class="icon-dropdown" id="userIconDropdown">
                <button class="icon-button" id="userIconButton" style="display:flex;align-items:center;gap:8px">
                    <?php if ($photo):
                        // Normalize photo URL so it works from the admin/ folder.
                        // Keep absolute URLs and protocol-relative URLs unchanged.
                        $photoUrl = $photo;
                        if (!preg_match('~^(?:[a-z]+:)?//~i', $photoUrl) && strpos($photoUrl, '/') !== 0) {
                            // Likely a relative path like "images/..." or "./images/..."
                            $photoUrl = '../' . ltrim($photoUrl, './');
                        }
                        ?>
                        <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Profile" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <i class="fas" style="font-size:24px">&#xf406;</i>
                    <?php endif; ?>
                    <span style="font-size:14px; margin-left:6px;"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                </button>
                <div class="icon-dropdown-menu" id="userIconMenu">
                    <a href="../profile.php">Profile</a>
                    <a href="../user.php?action=logout">Log out</a>
                </div>
            </div>
        <?php } else { ?>
            <div class="icon-dropdown" id="userIconDropdown">
                <button class="icon-button" id="userIconButton">
                    <i class="fas" style="font-size:24px">&#xf406;</i>
                </button>
                <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
                <div class="icon-dropdown-menu" id="userIconMenu">
                    <a href="../login.php">Log in</a>
                    <a href="../signUp.php">Sign up</a>
                </div>
            </div>
        <?php } ?>
    </header>

    <main>
