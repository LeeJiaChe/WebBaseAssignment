<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $_title ?? 'VISONX' ?></title>

    <link rel="shortcut icon" href="/images/visionX_logo.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="css/app.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="js/app.js"></script>

</head>

<body>

    <header class="main-header">

        <div class="header-logo">

            <a href="index.php">

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

                    <a href="drones.php">Drones</a>

                    <a href="accessories.php">Camera Accessories</a>

                </div>

            </div>



            <a href="contact.php">Contact</a>

        </nav>

        <a href="cart.php" class="cart-button" title="Cart">

            <i class="fas fa-shopping-cart" style="font-size:20px"></i>

            <span id="cartCount" class="hidden">0</span>

        </a>



        <div class="icon-dropdown" id="userIconDropdown">

            <button class="icon-button" id="userIconButton">

                <i class="fas" style="font-size:24px">&#xf406;</i>

            </button>

            <div class="icon-dropdown-menu" id="userIconMenu">

                <a href="login.php">Login</a>

                <a href="signUp.php">Sign up</a>

            </div>

        </div>

    </header>



    <main>
