<?php require_once 'connection.php'; ?>
<?php include 'header.php'; ?>
<!-- main area start  -->
<main>
    <!-- hero start -->
    <?php
    $hero_res = mysqli_query($link, "SELECT * FROM hero_settings LIMIT 1");
    $hero = mysqli_fetch_assoc($hero_res);
    ?>
    <section class="hero hero-style-one bg_img hero-height d-flex align-items-center"
        data-background="<?php echo htmlspecialchars($hero['bg_image']); ?>">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero__content sec-title">
                        <span class="sub-title wow fadeInUp"
                            data-wow-duration=".6s"><?php echo htmlspecialchars($hero['subtitle']); ?></span>
                        <h1 class="title mb-25 wow fadeInUp" data-wow-delay="150ms" data-wow-duration=".6s">
                            <?php echo htmlspecialchars($hero['title']); ?></h1>
                        <div class="hero__action ul_li wow fadeInUp" data-wow-delay="300ms" data-wow-duration=".6s">
                            <a class="thm-btn mr-45 mt-30"
                                href="<?php echo htmlspecialchars($hero['button_link']); ?>"><?php echo htmlspecialchars($hero['button_text']); ?></a>
                            <div class="hero__cta ul_li mt-30">
                                <span class="icon">
                                    <img src="assets/img/icon/call.svg" alt="">
                                </span>
                                <div class="info">
                                    <span><?php echo htmlspecialchars($hero['contact_text']); ?></span>
                                    <h4><?php echo htmlspecialchars($hero['contact_number']); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero__product text-md-end">
                        <img class="wow fadeInRight" data-wow-delay="300ms" data-wow-duration=".6s"
                            src="<?php echo htmlspecialchars($hero['product_image']); ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-shape">
            <img src="assets/img/bg/hero_shape.png" alt="">
        </div>
    </section>
    <!-- hero end -->

    <div class="bg_img position-botttom bottom--105 pb-70" data-background="assets/img/bg/pp_bg.png">
        <!-- popular product start -->
        <section class="popular-product pt-120 pb-120">
            <div class="container">
                <div class="sec-title text-center mb-30">
                    <span class="sub-title">Shop</span>
                    <h2 class="title">our popular product</h2>
                </div>
                <div class="row">
                    <div class="col-lg-8 pb-col-8">
                        <div class="row g-20">
                            <?php
                            $popular_result = mysqli_query($link, "SELECT * FROM products WHERE status='active' ORDER BY is_best_seller DESC, id DESC LIMIT 4");
                            if ($popular_result && mysqli_num_rows($popular_result) > 0) {
                                while ($pp = mysqli_fetch_assoc($popular_result)) {
                                    $pp_image = $pp['image'] ?: 'assets/img/shop/product_02.png';
                                    $pp_price = $pp['sale_price'] ?: $pp['price'];
                                    ?>
                                    <div class="col-lg-6 col-md-6 mt-20">
                                        <div class="popular-product-item ul_li">
                                            <div class="xb-item--img">
                                                <a href="shop-single.php?id=<?php echo $pp['id']; ?>">
                                                    <img src="<?php echo htmlspecialchars($pp_image); ?>"
                                                        alt="<?php echo htmlspecialchars($pp['name']); ?>"
                                                        style="height: 120px; object-fit: contain;">
                                                </a>
                                            </div>
                                            <div class="xb-item--holder">
                                                <h3 class="xb-item--title"><a
                                                        href="shop-single.php?id=<?php echo $pp['id']; ?>"><?php echo htmlspecialchars($pp['name']); ?></a>
                                                </h3>
                                                <div class="xb-item--rating-inner ul_li">
                                                    <ul class="xb-item--rating ul_li">
                                                        <li><img src="assets/img/icon/star.png" alt=""></li>
                                                        <li><img src="assets/img/icon/star.png" alt=""></li>
                                                        <li><img src="assets/img/icon/star.png" alt=""></li>
                                                        <li><img src="assets/img/icon/star.png" alt=""></li>
                                                        <li><img src="assets/img/icon/star.png" alt=""></li>
                                                    </ul>
                                                </div>
                                                <div class="xb-item--action ul_li_between">
                                                    <h4 class="xb-item--price">₹<?php echo number_format($pp_price, 2); ?></h4>

                                                    <form action="cart_action.php" method="POST" style="margin:0;">
                                                        <input type="hidden" name="action" value="add">
                                                        <input type="hidden" name="product_id" value="<?php echo $pp['id']; ?>">
                                                        <input type="hidden" name="product_name"
                                                            value="<?php echo htmlspecialchars($pp['name']); ?>">
                                                        <input type="hidden" name="product_price"
                                                            value="<?php echo $pp_price; ?>">
                                                        <input type="hidden" name="product_image"
                                                            value="<?php echo htmlspecialchars($pp['image']); ?>">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="xb-item--cart"
                                                            style="border:none; cursor:pointer;" title="Add to Cart">
                                                            <img src="assets/img/icon/bag.svg" alt="Add to Cart">
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "<p>No popular products found.</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <!--<div class="col-lg-4 pb-col-4">-->
                    <!--    <div class="popular-product__img mt-20">-->
                    <!--        <img src="assets/img/bg/pp_img.jpg" alt="">-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
            </div>
        </section>
        <!-- popular product end -->

        <!-- about start -->
        <section class="about pb-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="sec-title mb-30">
                            <span class="sub-title">about Protinut</span>
                            <h2 class="title">Protinut Your health journey begins!</h2>
                        </div>
                        <div class="about-experience">
                            <span>since</span>
                            <h2>2026</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-40">
                        <div class="about-content">
                            <p>At Protinut, we're dedicated to helping you on a transformative health journey. Our
                                mission is to provide you with the highest quality supplements, backed by science and
                                crafted with care. Here's what you can expect from us.</p>
                            <ul class="about-list ul_li mt-10">
                                <li><img src="assets/img/icon/check.svg" alt="">Natural Ingredients</li>
                                <li><img src="assets/img/icon/check.svg" alt="">Fishbone Diagram</li>
                                <li><img src="assets/img/icon/check.svg" alt="">Flower Formula</li>
                                <li><img src="assets/img/icon/check.svg" alt="">Increased Energy</li>
                                <li><img src="assets/img/icon/check.svg" alt="">Drug Interactions</li>
                                <li><img src="assets/img/icon/check.svg" alt="">100% Fat Blasting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- about end -->

    </div>

    <!-- feature start -->
    <section id="features" class="feature bg_img pt-200 pb-120" data-background="assets/img/bg/feature_bg.jpg">
        <div class="container">
            <div class="row align-items-center mt-none-30">
                <div class="col-lg-4 col-md-6 mt-30">
                    <ul class="feature-list list-unstyled">
                        <li>
                            <span class="xb-item--icon">
                                <img src="assets/img/icon/ft_01.svg" alt="">
                            </span>
                            <div class="xb-item--holder">
                                <h3 class="xb-item--title">Enhancing Joint Blood Flow</h3>
                                <p class="xb-item--desc">Your joints play a crucial role in your daily mobility and
                                    overall well.</p>
                            </div>
                        </li>
                        <li>
                            <span class="xb-item--icon">
                                <img src="assets/img/icon/ft_02.svg" alt="">
                            </span>
                            <div class="xb-item--holder">
                                <h3 class="xb-item--title">Help Reduce <br> Inflammation</h3>
                                <p class="xb-item--desc">Inflammation is a common factor in many chronic health.</p>
                            </div>
                        </li>
                        <li>
                            <span class="xb-item--icon">
                                <img src="assets/img/icon/ft_03.svg" alt="">
                            </span>
                            <div class="xb-item--holder">
                                <h3 class="xb-item--title">Helps You Stick To <br> Your Diet</h3>
                                <p class="xb-item--desc">Maintaining a healthy diet can be a challenging but essential
                                </p>
                            </div>
                        </li>
                        <li>
                            <span class="xb-item--icon">
                                <img src="assets/img/icon/ft_04.svg" alt="">
                            </span>
                            <div class="xb-item--holder">
                                <h3 class="xb-item--title">Ingredients To Fuel <br> Your Body</h3>
                                <p class="xb-item--desc">Proper nutrition is the cornerstone of a healthy lifestyle. In
                                    this guide.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-5 offset-lg-3 col-md-6 mt-30">
                    <div class="feature-content">
                        <div class="sec-title sec-title--big sec-title--white">
                            <span class="sub-title">100% PREMIUM QUALITY</span>
                            <h2 class="title mb-25">advanced formula for our health</h2>
                            <p>At Protinut, we're dedicated to helping you on a health journey. Our mission is to provide
                                .</p>
                            <a class="thm-btn mt-45" href="#!">LEARN MORE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- feature end -->

    <style>
        .product-item {
            transition: all 0.3s ease;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #fff;
            position: relative;
        }

        .product-item:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(-5px);
        }

        .product-item:hover .xb-item--holder,
        .product-item:hover .xb-item--action {
            opacity: 1 !important;
            visibility: visible !important;
        }

        .xb-item--img a img {
            height: 200px;
            width: 100%;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .xb-item--action {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .price-box {
            display: flex;
            gap: 10px;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
        }

        .qty-cart-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .qty-box {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            height: 40px;
        }

        .qty-btn {
            background: #f8f9fa;
            border: none;
            width: 30px;
            height: 100%;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            color: #333;
        }

        .qty-btn:hover {
            background: #e9ecef;
        }

        .qty-input {
            width: 40px;
            height: 100%;
            border: none;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            text-align: center;
            font-weight: 600;
            -moz-appearance: textfield;
            color: #333;
        }

        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .add-cart-btn {
            background: #f7941d;
            color: #fff;
            border: none;
            padding: 0 15px;
            height: 40px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .add-cart-btn:hover {
            background: #d67a12;
            color: #fff;
        }

        .add-cart-btn img {
            width: 16px;
            filter: brightness(0) invert(1);
        }
    </style>
    <section id="shop" class="product bg_img pt-120 pb-130" data-background="assets/img/bg/shop_bg.jpg">
        <div class="container">
            <div class="sec-title text-center mb-55">
                <span class="sub-title">products</span>
                <h2 class="title">best selling products</h2>
            </div>
            <div class="product-slider swiper-container">
                <div class="swiper-wrapper">
                    <?php
                    $slider_result = mysqli_query($link, "SELECT * FROM products WHERE status='active' ORDER BY is_best_seller DESC, id DESC LIMIT 8");
                    if ($slider_result && mysqli_num_rows($slider_result) > 0) {
                        while ($sp = mysqli_fetch_assoc($slider_result)) {
                            $sp_image = $sp['image'] ?: 'assets/img/shop/product_05.png';
                            $sp_price = $sp['sale_price'] ?: $sp['price'];
                            ?>
                            <div class="swiper-slide product-item text-center">
                                <div class="xb-item--img">
                                    <a href="shop-single.php?id=<?php echo $sp['id']; ?>"><img
                                            src="<?php echo htmlspecialchars($sp_image); ?>"
                                            alt="<?php echo htmlspecialchars($sp['name']); ?>"></a>
                                </div>
                                <div class="xb-item--holder">
                                    <h3 class="xb-item--title"><a
                                            href="shop-single.php?id=<?php echo $sp['id']; ?>"><?php echo htmlspecialchars($sp['name']); ?></a>
                                    </h3>
                                    <div class="xb-item--rating-inner ul_li_center">
                                        <ul class="xb-item--rating ul_li">
                                            <li><img src="assets/img/icon/star.png" alt=""></li>
                                            <li><img src="assets/img/icon/star.png" alt=""></li>
                                            <li><img src="assets/img/icon/star.png" alt=""></li>
                                            <li><img src="assets/img/icon/star.png" alt=""></li>
                                            <li><img src="assets/img/icon/star.png" alt=""></li>
                                        </ul>
                                    </div>
                                    <?php if ($sp['sale_price']): ?>
                                        <span class="xb-item--badge">sale!</span>
                                    <?php endif; ?>
                                </div>
                                <div class="xb-item--action mt-20">
                                    <div class="price-box">
                                        <?php if ($sp['sale_price']): ?>
                                            <span
                                                style="text-decoration:line-through;color:#999;">₹<?php echo number_format($sp['price'], 2); ?></span>
                                            <span style="color:#e74c3c;">₹<?php echo number_format($sp['sale_price'], 2); ?></span>
                                        <?php else: ?>
                                            <span>₹<?php echo number_format($sp['price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <form action="cart_action.php" method="POST">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $sp['id']; ?>">
                                        <input type="hidden" name="product_name"
                                            value="<?php echo htmlspecialchars($sp['name']); ?>">
                                        <input type="hidden" name="product_price" value="<?php echo $sp_price; ?>">
                                        <input type="hidden" name="product_image"
                                            value="<?php echo htmlspecialchars($sp['image']); ?>">

                                        <div class="qty-cart-wrap">
                                            <div class="qty-box">
                                                <button type="button" class="qty-btn"
                                                    onclick="let inp=this.nextElementSibling; if(inp.value>1) inp.value--;">-</button>
                                                <input type="number" name="quantity" class="qty-input" value="1" min="1"
                                                    max="100">
                                                <button type="button" class="qty-btn"
                                                    onclick="this.previousElementSibling.value++;">+</button>
                                            </div>
                                            <button type="submit" class="add-cart-btn">
                                                <img src="assets/img/icon/bag.svg" alt="">
                                                Add to Cart
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php }
                    } ?>
                </div>
            </div>
        </div>
    </section>
    <!-- product end -->

    <!-- testimonial start -->
    <!--<section class="testimonial bg_img pt-120 pb-115" data-background="assets/img/bg/tm_bg.jpg">-->
    <!--    <div class="container">-->
    <!--        <div class="sec-title sec-title--white text-center mb-60">-->
    <!--            <span class="sub-title">OVERALL RATING</span>-->
    <!--            <h2 class="title">CUSTOMER REVIEWS</h2>-->
    <!--        </div>-->
    <!--        <div class="testimonial-slider swiper-container">-->
    <!--            <div class="swiper-wrapper">-->
    <!--                <div class="swiper-slide xb-testimonial">-->
    <!--                    <div class="xb-item--author ul_li">-->
    <!--                        <div class="xb-item--avatar">-->
    <!--                            <img src="assets/img/avatar/tst_01.png" alt="">-->
    <!--                            <div class="xb-item--quote">-->
    <!--                                <img src="assets/img/icon/quote.svg" alt="">-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        <div class="xb-item--holder">-->
    <!--                            <h3 class="xb-item--name">Richard Thomas</h3>-->
    <!--                            <span class="xb-item--date">October 17, 2024</span>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="xb-item--desc mt-35">-->
    <!--                        "I've been using Protinut for a few months, and it has significantly boosted my energy-->
    <!--                        levels. It's a game-changer for my daily routine. Thank you, Protinut!"-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="swiper-slide xb-testimonial">-->
    <!--                    <div class="xb-item--author ul_li">-->
    <!--                        <div class="xb-item--avatar">-->
    <!--                            <img src="assets/img/avatar/tst_02.png" alt="">-->
    <!--                            <div class="xb-item--quote">-->
    <!--                                <img src="assets/img/icon/quote.svg" alt="">-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        <div class="xb-item--holder">-->
    <!--                            <h3 class="xb-item--name">Richard Thomas</h3>-->
    <!--                            <span class="xb-item--date">October 17, 2024</span>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="xb-item--desc mt-35">-->
    <!--                        "Since I started using Protinut, my energy levels have skyrocketed. I'm more alert and-->
    <!--                        focused, and it's transformed my daily productivity. Highly recommended!"-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="swiper-slide xb-testimonial">-->
    <!--                    <div class="xb-item--author ul_li">-->
    <!--                        <div class="xb-item--avatar">-->
    <!--                            <img src="assets/img/avatar/tst_03.png" alt="">-->
    <!--                            <div class="xb-item--quote">-->
    <!--                                <img src="assets/img/icon/quote.svg" alt="">-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        <div class="xb-item--holder">-->
    <!--                            <h3 class="xb-item--name">Richard Thomas</h3>-->
    <!--                            <span class="xb-item--date">October 17, 2024</span>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="xb-item--desc mt-35">-->
    <!--                        "As an active person, I've had joint discomfort. Protinut has made a remarkable difference,-->
    <!--                        allowing me to enjoy daily activities. Thank you, Protinut!"-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="swiper-slide xb-testimonial">-->
    <!--                    <div class="xb-item--author ul_li">-->
    <!--                        <div class="xb-item--avatar">-->
    <!--                            <img src="assets/img/avatar/tst_01.png" alt="">-->
    <!--                            <div class="xb-item--quote">-->
    <!--                                <img src="assets/img/icon/quote.svg" alt="">-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        <div class="xb-item--holder">-->
    <!--                            <h3 class="xb-item--name">Richard Thomas</h3>-->
    <!--                            <span class="xb-item--date">October 17, 2024</span>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="xb-item--desc mt-35">-->
    <!--                        "I've been using Protinut for a few months, and it has significantly boosted my energy-->
    <!--                        levels. It's a game-changer for my daily routine. Thank you, Protinut!"-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="swiper-pagination"></div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->
    <!-- testimonial end -->

    <!-- pricing removed -->

    <!-- faq start -->
    <section class="faq" data-bg-color="#fff">
        <div class="container">
            <div class="accordion-inner bg_img" data-background="assets/img/bg/faq_bg.jpg">
                <div class="sec-title sec-title--white text-center mb-60">
                    <span class="sub-title">faq</span>
                    <h2 class="title">product information</h2>
                </div>
                <ul class="xb-accordion accordion_box clearfix">
                    <li class="accordion block">
                        <div class="acc-btn">
                            Why should I take dietary supplements?
                            <span class="arrow"></span>
                        </div>
                        <div class="acc_body">
                            <div class="content">
                                <p>When addressing whether your supplements are suitable for vegans or vegetarians, you
                                    can following points:</p>
                                <ul>
                                    <li>Vegan-Friendly Ingredients</li>
                                    <li>No Animal-Derived Ingredients</li>
                                    <li>Alternative Capsules</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block active-block">
                        <div class="acc-btn">
                            Are your supplements suitable for vegans or vegetarians?
                            <span class="arrow"></span>
                        </div>
                        <div class="acc_body current">
                            <div class="content">
                                <p>When addressing whether your supplements are suitable for vegans or vegetarians, you
                                    can following points:</p>
                                <ul>
                                    <li>Vegan-Friendly Ingredients</li>
                                    <li>No Animal-Derived Ingredients</li>
                                    <li>Alternative Capsules</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn">
                            Can I take multiple supplements together?
                            <span class="arrow"></span>
                        </div>
                        <div class="acc_body">
                            <div class="content">
                                <p>When addressing whether your supplements are suitable for vegans or vegetarians, you
                                    can following points:</p>
                                <ul>
                                    <li>Vegan-Friendly Ingredients</li>
                                    <li>No Animal-Derived Ingredients</li>
                                    <li>Alternative Capsules</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn">
                            Can I take supplements with medications or other dietary products?
                            <span class="arrow"></span>
                        </div>
                        <div class="acc_body">
                            <div class="content">
                                <p>When addressing whether your supplements are suitable for vegans or vegetarians, you
                                    can following points:</p>
                                <ul>
                                    <li>Vegan-Friendly Ingredients</li>
                                    <li>No Animal-Derived Ingredients</li>
                                    <li>Alternative Capsules</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn">
                            What is the recommended daily dosage for your supplements?
                            <span class="arrow"></span>
                        </div>
                        <div class="acc_body">
                            <div class="content">
                                <p>When addressing whether your supplements are suitable for vegans or vegetarians, you
                                    can following points:</p>
                                <ul>
                                    <li>Vegan-Friendly Ingredients</li>
                                    <li>No Animal-Derived Ingredients</li>
                                    <li>Alternative Capsules</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn">
                            Are your supplements tested for quality and purity?
                            <span class="arrow"></span>
                        </div>
                        <div class="acc_body">
                            <div class="content">
                                <p>When addressing whether your supplements are suitable for vegans or vegetarians, you
                                    can following points:</p>
                                <ul>
                                    <li>Vegan-Friendly Ingredients</li>
                                    <li>No Animal-Derived Ingredients</li>
                                    <li>Alternative Capsules</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <!-- faq end -->

    <!-- brand start -->
    <section class="brand pt-110 md-pb-0 pb-90" data-bg-color="#fff">
        <div class="container">
            <div class="sec-title text-center mb-35">
                <span class="sub-title">Perfect Brand is Featured on</span>
            </div>
            <div class="xb-swiper-sliders brand-slider">
                <div class="xb-carousel-inner">
                    <div class="xb-swiper-container swiper-container">
                        <div class="xb-swiper-wrapper swiper-wrapper">
                            <div class="swiper-slide xb-swiper-slide">
                                <a href="#!"><img src="assets/img/brand/img_01.png" alt=""></a>
                            </div>
                            <div class="swiper-slide xb-swiper-slide">
                                <a href="#!"><img src="assets/img/brand/img_02.png" alt=""></a>
                            </div>
                            <div class="swiper-slide xb-swiper-slide">
                                <a href="#!"><img src="assets/img/brand/img_03.png" alt=""></a>
                            </div>
                            <div class="swiper-slide xb-swiper-slide">
                                <a href="#!"><img src="assets/img/brand/img_04.png" alt=""></a>
                            </div>
                            <div class="swiper-slide xb-swiper-slide">
                                <a href="#!"><img src="assets/img/brand/img_05.png" alt=""></a>
                            </div>
                            <div class="swiper-slide xb-swiper-slide">
                                <a href="#!"><img src="assets/img/brand/img_06.png" alt=""></a>
                            </div>
                            <div class="swiper-slide xb-swiper-slide">
                                <a href="#!"><img src="assets/img/brand/img_01.png" alt=""></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- brand end -->

    <!-- blog start -->
    <section id="blog" class="blog position-top blog-pb bg_img pt-180" data-background="assets/img/bg/blog_bg.jpg">
        <div class="container">
            <div class="ul_li_between align-items-end mb-25">
                <div class="sec-title mb-30">
                    <span class="sub-title">blog</span>
                    <h2 class="title">latest blog</h2>
                </div>
                <a class="border-btn mb-30" href="blog.php">view all blog</a>
            </div>
            <div class="row mt-none-30 justify-content-center">
                <?php
                $blog_res = mysqli_query($link, "SELECT * FROM blogs ORDER BY id DESC LIMIT 3");
                if ($blog_res && mysqli_num_rows($blog_res) > 0) {
                    while ($blog = mysqli_fetch_assoc($blog_res)) {
                        $blog_image = $blog['image'] ?: 'assets/img/blog/img_01.jpg';
                        $blog_date = date('d', strtotime($blog['created_at']));
                        $blog_month = date('M', strtotime($blog['created_at']));
                        ?>
                        <div class="col-lg-4 col-md-6 mt-30">
                            <div class="xb-blog">
                                <div class="xb-item--img">
                                    <a href="blog-single.php?id=<?php echo $blog['id']; ?>">
                                        <?php if ($blog['media_type'] == 'video'): ?>
                                            <video src="<?php echo htmlspecialchars($blog_image); ?>" muted loop playsinline onmouseover="this.play()" onmouseout="this.pause()" style="width:100%; height:100%; object-fit:cover;"></video>
                                        <?php else: ?>
                                            <img src="<?php echo htmlspecialchars($blog_image); ?>" alt="">
                                        <?php endif; ?>
                                    </a>
                                    <div class="xb-item--date"><?php echo $blog_date; ?> <br><span><?php echo $blog_month; ?></span>
                                    </div>
                                </div>
                                <div class="xb-item--holder">
                                    <span class="xb-item--author"><img src="assets/img/icon/user.svg"
                                            alt=""><?php echo htmlspecialchars($blog['author']); ?></span>
                                    <h3 class="xb-item--title border-effect"><a
                                            href="blog-single.php?id=<?php echo $blog['id']; ?>"><?php echo htmlspecialchars($blog['title']); ?></a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else {
                    echo "<p class='text-center mt-30'>No blog posts yet.</p>";
                } ?>
            </div>
        </div>
    </section>
    <!-- blog end -->

</main>
<!-- main area end  -->

<?php include 'footer.php'; ?>            