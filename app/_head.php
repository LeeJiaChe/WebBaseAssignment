<?php

if (session_status() !== PHP_SESSION_ACTIVE) {

    @session_start();

}

// load DB for user image lookup

global $db;
if (!isset($db)) {
    $db = require_once __DIR__ . '/lib/db.php';
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $_title ?? 'VISONX' ?></title>

    <link rel="shortcut icon" href="/images/visionX_logo.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <?= '<link rel="stylesheet" href="css/app.css">' ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="js/app.js"></script>

</head>

<body<?= isset($_bodyClass) ? ' class="'.$_bodyClass.'"' : '' ?>>

    <header class="main-header">

        <div class="header-logo">

            <a href="<?= '/index.php' ?>">

                <img src="images/VisionX.png" alt="VisionX Logo" class="logo-img">

            </a>

        </div>



        <nav class="header-nav">

            <div class="dropdown">

                <a href="javascript:void(0)" class="dropdown-toggle">Brands <span class="arrow">&#9662;</span></a>

                <div class="dropdown-menu">

                    <a href="canon.php">Canon</a>

                    <a href="fujifilm.php">FUJIFILM</a>

                    <a href="dji.php">DJI</a>

                    <a href="sony.php">Sony</a>

                    <a href="insta360.php">Insta360</a>

                </div>

            </div>



            <div class="dropdown">

                <a href="javascript:void(0)" class="dropdown-toggle">Categories <span class="arrow">&#9662;</span></a>

                <div class="dropdown-menu">

                    <a href="mirrorless.php">Mirrorless Cameras</a>

                    <a href="dslr.php">DSLR Cameras</a>

                    <a href="action.php">Action Cameras</a>

                    <a href="drone.php">Drones</a>

                    <a href="accessories.php">Camera Accessories</a>

                </div>

            </div>



            <a href="contact.php">Contact</a>



            <?php if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>

                <a href="/admin/products.php" style="margin-left:12px; font-weight:700; color:#2b7cff;">Admin Dashboard</a>

            <?php endif; ?>

        </nav>



        <a href="cart.php" class="cart-button" title="Cart">

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

        $usersFile = __DIR__ . '/users.json';

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

                    <?php if ($photo): ?>

                        <img src="<?= htmlspecialchars($photo) ?>" alt="Profile" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">

                    <?php else: ?>

                        <i class="fas" style="font-size:24px">&#xf406;</i>

                    <?php endif; ?>

                </button>

                <div class="icon-dropdown-menu" id="userIconMenu">

                  <div class="dropdown-user-header">

                    <div style="font-size: 1.1em;"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>

                </div>

    

                <a href="profile.php"><i class="fas fa-user" style="margin-right:8px; width:16px; text-align:center;"></i> Profile</a>

    

                <a href="orders.php"><i class="fas fa-history" style="margin-right:8px; width:16px; text-align:center;"></i> Order History</a>

    

                <a href="user.php?action=logout"><i class="fas fa-sign-out-alt" style="margin-right:8px; width:16px; text-align:center;"></i> Log out</a>

              </div>

            </div>

        <?php } else { ?>

            <div class="icon-dropdown" id="userIconDropdown">

                <button class="icon-button" id="userIconButton">

                    <i class="fas" style="font-size:24px">&#xf406;</i>

                </button>

                <div class="icon-dropdown-menu" id="userIconMenu">

                    <a href="login.php">Log in</a>

                    <a href="signUp.php">Sign up</a>

                </div>

            </div>

        <?php } ?>

    </header>



    <main>
