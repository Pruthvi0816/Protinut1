
                <!doctype html>
<html lang="zxx">

<head>

    <!--========= Required meta tags =========-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Protinut - Health Supplement</title>

 <link rel="icon" type="image/png" href="assets/img/favicon-96x96.png?v=2" sizes="96x96" />
<link rel="shortcut icon" href="assets/img/favicon.ico?v=2" />
<link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg" />


<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png" />

<link rel="manifest" href="assets/img/site.webmanifest" />

    <!-- css include -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/swiper.min.css">
    <link rel="stylesheet" href="assets/css/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>

    <!-- backtotop - start -->
    <div class="xb-backtotop">
        <a href="#" class="scroll">
            <i class="far fa-arrow-up"></i>
        </a>
    </div>
    <!-- backtotop - end -->

    <!-- preloader removed to prevent hanging -->

    <div class="body_wrap">

        <!-- header start -->
        <header id="home" class="header-area header-default is-sticky">
            <div class="xb-header stricky">
                <div class="container">
                    <div class="header__wrap ul_li_between">
                        <div class="header-logo">
                            <a href="index.php" class="site-logo-text">Protinut</a>
                        </div>
                        <div class="main-menu__wrap ul_li navbar navbar-expand-lg">
                            <nav class="main-menu collapse navbar-collapse">
                                <ul>
                                    <li class="active">
                                        <a class="section-link" href="index.php"><span>Home</span></a>
                                    </li>
                                    <li><a class="section-link" href="index.php#features"><span>Features</span></a></li>
                                    <li class="menu-item-has-children">
                                        <a class="section-link" href="index.php#shop"><span>Shop</span></a>
                                        <ul class="submenu">
                                            <li><a href="shop.php"><span>Products</span></a></li>
                                            <li><a href="shop-single.php"><span>Single Product</span></a></li>
                                            <li><a
                                                    href="<?php echo isset($_SESSION['user_id']) ? 'cart.php' : 'login.php'; ?>"><span>Cart</span></a>
                                            </li>
                                            <li><a href="checkout.php"><span>Checkout</span></a></li>
                                        </ul>
                                    </li>
                                    <li class="menu-item-has-children">
                                        <a class="section-link" href="index.php#blog"><span>Blog</span></a>
                                        <ul class="submenu">
                                            <li><a href="blog.php"><span>Blog</span></a></li>
                                            <li><a href="blog-single.php"><span>Blog Details</span></a></li>
                                        </ul>
                                    </li>
                                    <li><a class="section-link" href="index.php#contact"><span>Contact</span></a></li>
                                    <?php if (isset($_SESSION["user_id"])): ?>
                                        <li class="menu-item-has-children" style="position: relative;">
                                            <a class="section-link" href="javascript:void(0);"
                                                style="display:flex; align-items:center; gap:8px;">
                                                <i class="far fa-user-circle"
                                                    style="font-size:20px; color:var(--color-primary);"></i>
                                                <span>Profile</span>
                                            </a>
                                            <ul class="submenu" style="min-width: 200px; padding: 15px 0;">
                                                <li
                                                    style="padding: 10px 20px; border-bottom: 1px solid #eee; margin-bottom: 5px;">
                                                    <span
                                                        style="display:block; font-weight:bold; color:#060606; font-family:var(--font-heading);"><?php echo htmlspecialchars($_SESSION["user_name"] ?? ''); ?></span>
                                                    <span
                                                        style="font-size: 13px; color:#666; word-break: break-all;"><?php echo htmlspecialchars($_SESSION["user_email"] ?? 'User'); ?></span>
                                                </li>
                                                <li><a href="my_orders.php"><span>My Orders</span></a></li>
                                                <li><a href="cart.php"><span>My Cart</span></a></li>
                                                <li><a href="checkout.php"><span>Checkout</span></a></li>
                                                <li><a href="logout.php" style="color: red;"><span>Logout</span></a></li>
                                            </ul>
                                        </li>
                                    <?php else: ?>
                                        <li><a class="section-link" href="login.php"><span>Login / Register</span></a></li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header__right d-none d-lg-block" style="position: absolute; right: 5%; top: 30px;">
                <div class="ul_li">
                    <div class="header-shop-cart">
                        <a href="<?php echo isset($_SESSION['user_id']) ? 'cart.php' : 'login.php'; ?>">
                            <img src="assets/img/icon/bag.svg" alt="">
                            <span
                                class="mini-cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0'; ?></span>
                        </a>
                    </div>
                    <a class="header__bar offcanvas-sidebar-btn" href="javascript:void(0);">
                        <div class="header__bar-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="header-bar-mobile d-lg-none"
                style="position: absolute; right: 5%; top: 25px; display: flex; align-items: center; gap: 20px;">
                <div class="header-shop-cart">
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'cart.php' : 'login.php'; ?>"
                        style="position: relative; display: inline-block;">
                        <img src="assets/img/icon/bag.svg" alt="" style="width: 26px;">
                        <span class="mini-cart-count"
                            style="position: absolute; top: -5px; right: -10px; background-color: var(--color-primary); color: #fff; width: 22px; height: 22px; line-height: 22px; text-align: center; border-radius: 50%; font-size: 11px; font-weight: bold;"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0'; ?></span>
                    </a>
                </div>
                <a class="header__bar xb-nav-mobile" href="javascript:void(0);">
                    <div class="header__bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
            </div>
        </header>

        <div class="xb-header-menu">
            <div class="xb-header-menu-scroll">
                <div class="xb-menu-close xb-hide-xl xb-close"
                    style="top: 20px; right: 20px; cursor: pointer; color: white;">
                    <i class="far fa-times" style="font-size: 24px;"></i>
                </div>
                <div class="xb-logo-mobile xb-hide-xl" style="margin-bottom: 30px;">
                    <a href="index.php" class="site-logo-text"
                        style="color: white; font-size: 28px; font-weight: bold; letter-spacing: -0.5px; font-family: var(--font-heading);">Protinut</a>
                </div>
                <nav class="xb-header-nav">
                    <ul class="xb-menu-primary clearfix">
                        <li class="active"><a class="section-link" href="index.php"><span>Home</span></a></li>
                        <li><a class="section-link" href="index.php#features"><span>Features</span></a></li>
                        <li class="menu-item menu-item-has-children">
                            <a href="javascript:void(0);"><span>Shop</span></a>
                            <ul class="sub-menu">
                                <li><a href="shop.php"><span>Products</span></a></li>
                                <li><a href="shop-single.php"><span>Single Product</span></a></li>
                                <li><a
                                        href="<?php echo isset($_SESSION['user_id']) ? 'cart.php' : 'login.php'; ?>"><span>Cart</span></a>
                                </li>
                                <li><a href="checkout.php"><span>Checkout</span></a></li>
                            </ul>
                        </li>
                        <li class="menu-item menu-item-has-children">
                            <a href="javascript:void(0);"><span>Blog</span></a>
                            <ul class="sub-menu">
                                <li><a href="blog.php"><span>Blog</span></a></li>
                                <li><a href="blog-single.php"><span>Blog Details</span></a></li>
                            </ul>
                        </li>
                        <li><a class="section-link" href="index.php#contact"><span>Contact</span></a></li>
                        <?php if (isset($_SESSION["user_id"])): ?>
                            <li class="menu-item menu-item-has-children">
                                <a href="javascript:void(0);" style="display:flex; align-items:center; gap:8px;">
                                    <i class="far fa-user-circle" style="font-size:20px; color:var(--color-primary);"></i>
                                    <span>Profile</span>
                                </a>
                                <ul class="sub-menu">
                                    <li
                                        style="padding: 10px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 5px;">
                                        <span
                                            style="display:block; font-weight:bold; color:#fff; font-family:var(--font-heading);"><?php echo htmlspecialchars($_SESSION["user_name"] ?? ''); ?></span>
                                        <span
                                            style="font-size: 13px; color:#aaa; word-break: break-all;"><?php echo htmlspecialchars($_SESSION["user_email"] ?? 'User'); ?></span>
                                    </li>
                                    <li><a href="my_orders.php"><span>My Orders</span></a></li>
                                    <li><a href="cart.php"><span>My Cart</span></a></li>
                                    <li><a href="checkout.php"><span>Checkout</span></a></li>
                                    <li><a href="logout.php" style="color: red;"><span>Logout</span></a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a class="section-link" href="login.php"><span>Login / Register</span></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="xb-header-menu-backdrop"></div>
        <!-- header end -->

        <!-- sidebar-info start -->
        <div class="offcanvas-sidebar">
            <div class="sidebar-menu-close">
                <a class="xb-close" href="javascript:void(0);"></a>
            </div>
            <div class="sidebar-top mb-65">
                <div class="sidebar-logo mb-40">
                    <a href="index.php" class="site-logo-text">Protinut</a>
                </div>
                <div class="sidebar-content">
                    Achieving optimal nutrition is a complex endeavor without the inclusion of supplementary support
                </div>
            </div>

            <div class="sidebar-contact-info mb-65">
                <h4 class="sidebar-heading">Contact Information</h4>
                <ul class="sidebar-info-list list-unstyled">
                    <li><span><img src="assets/img/icon/i_star.svg" alt=""></span>PLOT NO 3 NEAR FILTER TAKI MIDC,DHARASHIV-413501,MAHARASHTRA. </li>
                    <li><a href="#!"><span><img src="assets/img/icon/i_star.svg" alt=""></span>+91 830 830 230 1</a>
                    </li>
                    <li><a href="#!"><span><img src="assets/img/icon/i_star.svg" alt=""></span>contact@Protinut.in</a>
                    </li>
                </ul>
            </div>
            <div class="xb-content-wrap d-flex">
                <div class="xb-title col-auto">Call us:</div>
                <div class="xb-inf-content-wrap col">
                    <div class="xb-item-wrap row">
                        <div class="xb-item col-auto ">
                            <span class="item-content"><a href="tel:02456787535" class="tel">+91 830 830 230 1</a></span>
                        </div>
                        <div class="xb-item col-auto "> <span class="item-content"><a
                                    href="mailto:support@gmail.com">Protinut.in@gmail.com</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar-socials-wrap mt-30">
                <a class="social-item" href="https://www.instagram.com/protinut.in?igsh=MW1lZDFkYnFuYWtleA%3D%3D" target="_blank">INSTAGRAM</a>
                <!--<a class="social-item" href="https://www.behance.net/" target="_blank">Behance</a>-->
                <!--<a class="social-item" href="#" target="_blank">Telegram</a>-->
                <!--<a class="social-item" href="https://dribbble.com/" target="_blank">Dribbble</a>-->
            </div>

        </div>
        <!-- sidebar-info end -->
        <div class="body-overlay"></div>

        <!-- Auth Popup Modal -->
        <div id="authModal"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999;">
            <div id="authModalOverlay"
                style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);">
            </div>
            <div
                style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; border-radius:16px; padding:40px 36px 36px; max-width:420px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); z-index:100000;">
                <button id="authModalClose"
                    style="position:absolute; top:12px; right:16px; background:none; border:none; font-size:24px; cursor:pointer; color:#999; line-height:1;">&times;</button>
                <div style="margin-bottom:10px;">
                    <img src="assets/img/logo/preloader.png" alt="Protinut" style="width:60px; height:auto;">
                </div>
                <h3 style="font-family:var(--font-heading); font-size:22px; color:#060606; margin-bottom:8px;">Welcome
                    to Protinut</h3>
                <p style="color:#807474; font-size:14px; margin-bottom:28px;">Please sign in or create an account to
                    continue shopping.</p>
                <a href="login.php" class="thm-btn btn-filled"
                    style="display:block; width:100%; text-align:center; margin-bottom:14px; border:none; cursor:pointer; font-size:15px; padding:14px 20px; transition: none; transform: none;">LOGIN</a>
                <a href="register.php" 
                    style="display:block; width:100%; text-align:center; padding:12px 20px; background-color: var(--color-primary); color: #fff; font-weight:700; font-size:15px; text-transform:uppercase; border-radius:0; cursor:pointer; font-family:var(--font-heading); letter-spacing:1px; transition:none; transform: none; border: none;">REGISTER</a>
            </div>
        </div>

        <!-- Pass login status to JS -->
        <script>var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;</script>          