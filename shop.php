<?php require_once 'connection.php'; ?>
<?php include 'header.php'; ?>
<!-- main area start  -->
<main>
    <!-- breadcrumb start -->
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title">Shop</h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item">Shop</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- shop start -->
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

        /* Ensure actions don't disappear on hover */
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

        /* Sidebar Styling Fixes */
        .shop-sidebar .widget {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .widget__title {
            font-size: 18px;
            font-weight: 700;
            border-bottom: 2px solid #f7941d;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
            text-transform: uppercase;
        }

        .widget__search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .widget__search input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
            font-size: 14px;
        }

        .widget__search input:focus {
            border-color: #f7941d;
        }

        .widget__search button {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 45px;
            background: #f7941d;
            color: #fff;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            transition: 0.3s;
        }

        .widget__search button:hover {
            background: #d67a12;
        }

        .widget__category li {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #eee;
        }

        .widget__category li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .widget__category li a {
            display: flex;
            justify-content: space-between;
            color: #555;
            transition: 0.2s;
            font-weight: 500;
            text-decoration: none;
        }

        .widget__category li a:hover {
            color: #f7941d;
            padding-left: 5px;
        }

        .widget__category li a span {
            background: #f5f5f5;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: #888;
        }

        .tagcloud {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tagcloud a {
            display: inline-block;
            padding: 6px 15px;
            background: #f5f5f5;
            color: #555;
            border-radius: 4px;
            font-size: 13px;
            transition: 0.3s;
            border: 1px solid #eee;
            text-decoration: none;
            text-transform: capitalize;
        }

        .tagcloud a:hover {
            background: #f7941d;
            color: #fff;
            border-color: #f7941d;
        }
    </style>
    <section class="shop pt-115 pb-385">
        <div class="container">
            <div class="row mt-none-60">
                <div class="col-lg-9 mt-60">
                    <div class="woocommerce-content-wrap">
                        <?php
                        // Fetch all active products
                        $where_clauses = ["status='active'"];
                        $search = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';
                        $category = isset($_GET['category']) ? mysqli_real_escape_string($link, $_GET['category']) : '';
                        $tag = isset($_GET['tag']) ? mysqli_real_escape_string($link, $_GET['tag']) : '';

                        if ($search) {
                            $where_clauses[] = "(name LIKE '%$search%' OR description LIKE '%$search%')";
                        }
                        if ($category) {
                            $where_clauses[] = "category='$category'";
                        }
                        if ($tag) {
                            $where_clauses[] = "(name LIKE '%$tag%' OR description LIKE '%$tag%')";
                        }

                        $where_sql = implode(' AND ', $where_clauses);
                        $products_result = mysqli_query($link, "SELECT * FROM products WHERE $where_sql ORDER BY id DESC");
                        $total_products = $products_result ? mysqli_num_rows($products_result) : 0;
                        ?>
                        <div class="woocommerce-toolbar-top ul_li_between">
                            <p class="woocommerce-result-count">Showing <?php echo $total_products; ?> products</p>
                        </div>
                        <div class="woocommerce-content-inner">
                            <div class="products">
                                <div class="row">
                                    <?php if ($total_products > 0): ?>
                                        <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                                                <div class="product product-item text-center">
                                                    <div class="xb-item--img">
                                                        <a href="shop-single.php?id=<?php echo $product['id']; ?>">
                                                            <img src="<?php echo htmlspecialchars($product['image'] ?: 'assets/img/shop/product_05.png'); ?>"
                                                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                        </a>
                                                    </div>
                                                    <div class="xb-item--holder">
                                                        <h3 class="xb-item--title">
                                                            <a
                                                                href="shop-single.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
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
                                                    </div>
                                                    <div class="xb-item--action mt-20">
                                                        <div class="price-box">
                                                            <?php if ($product['sale_price']): ?>
                                                                <span
                                                                    style="text-decoration:line-through;color:#999;">₹<?php echo number_format($product['price'], 2); ?></span>
                                                                <span
                                                                    style="color:#e74c3c;">₹<?php echo number_format($product['sale_price'], 2); ?></span>
                                                            <?php else: ?>
                                                                <span>₹<?php echo number_format($product['price'], 2); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <form action="cart_action.php" method="POST">
                                                            <input type="hidden" name="action" value="add">
                                                            <input type="hidden" name="product_id"
                                                                value="<?php echo $product['id']; ?>">
                                                            <input type="hidden" name="product_name"
                                                                value="<?php echo htmlspecialchars($product['name']); ?>">
                                                            <input type="hidden" name="product_price"
                                                                value="<?php echo $product['sale_price'] ?: $product['price']; ?>">
                                                            <input type="hidden" name="product_image"
                                                                value="<?php echo htmlspecialchars($product['image']); ?>">

                                                            <div class="qty-cart-wrap">
                                                                <div class="qty-box">
                                                                    <button type="button" class="qty-btn"
                                                                        onclick="let inp=this.nextElementSibling; if(inp.value>1) inp.value--;">-</button>
                                                                    <input type="number" name="quantity" class="qty-input"
                                                                        value="1" min="1" max="100">
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
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="col-12 text-center py-5">
                                            <h4>No products available yet.</h4>
                                            <p>Check back soon!</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mt-60">
                            <div class="shop-sidebar sidebar-area mt-none-40">
                                <div class="widget mt-40">
                                    <h2 class="widget__title">Search</h2>
                                    <div class="widget__inner">
                                        <form class="widget__search" action="shop.php" method="GET">
                                            <input type="text" name="search" placeholder="Search..."
                                                value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                            <?php if ($category): ?><input type="hidden" name="category"
                                                    value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>
                                            <button type="submit"><i class="far fa-search"></i></button>
                                        </form>
                                    </div>
                                </div>
                                <div class="widget mt-40">
                                    <h2 class="widget__title">
                                        <span>Product Categories</span>
                                    </h2>
                                    <div class="widget__inner">
                                        <ul class="widget__category list-unstyled">
                                            <li><a href="shop.php" <?php if (empty($category))
                                                echo 'style="color:#f7941d;"'; ?>>All Products</a></li>
                                            <?php
                                            $cat_result = mysqli_query($link, "SELECT category, COUNT(*) as cnt FROM products WHERE status='active' AND category IS NOT NULL AND category != '' GROUP BY category ORDER BY category");
                                            if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                                                while ($cat = mysqli_fetch_assoc($cat_result)) {
                                                    $active_class = ($category == $cat['category']) ? 'style="color:#f7941d;"' : '';
                                                    echo '<li><a href="shop.php?category=' . urlencode($cat['category']) . '" ' . $active_class . '>' . htmlspecialchars($cat['category']) . ' <span>(' . $cat['cnt'] . ')</span></a></li>';
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="widget mt-40">
                                    <h2 class="widget__title">
                                        <span>Tags</span>
                                    </h2>
                                    <div class="widget__inner">
                                        <div class="tagcloud">
                                            <a href="shop.php?tag=energy" <?php if ($tag == 'energy')
                                                echo 'style="background:#f7941d;color:#fff;"'; ?>>energy</a>
                                            <a href="shop.php?tag=fitness" <?php if ($tag == 'fitness')
                                                echo 'style="background:#f7941d;color:#fff;"'; ?>>fitness</a>
                                            <a href="shop.php?tag=healthy" <?php if ($tag == 'healthy')
                                                echo 'style="background:#f7941d;color:#fff;"'; ?>>healthy</a>
                                            <a href="shop.php?tag=powders" <?php if ($tag == 'powders')
                                                echo 'style="background:#f7941d;color:#fff;"'; ?>>powders</a>
                                            <a href="shop.php?tag=nutrition" <?php if ($tag == 'nutrition')
                                                echo 'style="background:#f7941d;color:#fff;"'; ?>>nutrition</a>
                                            <a href="shop.php?tag=snacks" <?php if ($tag == 'snacks')
                                                echo 'style="background:#f7941d;color:#fff;"'; ?>>snacks</a>
                                            <a href="shop.php?tag=wellness" <?php if ($tag == 'wellness')
                                                echo 'style="background:#f7941d;color:#fff;"'; ?>>wellness</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </section>
    <!-- shop end -->

</main>
<!-- main area end  -->
<?php include 'footer.php'; ?>
