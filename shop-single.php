<?php require_once 'connection.php'; ?>
<?php
// Fetch product by ID
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = null;
if ($product_id > 0) {
    $result = mysqli_query($link, "SELECT * FROM products WHERE id=$product_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    }
}
if (!$product) {
    header('Location: shop.php');
    exit;
}
$product_image = $product['image'] ?: 'assets/img/shop/product-single1.jpg';
$effective_price = $product['sale_price'] ?: $product['price'];
?>
<?php include 'header.php'; ?>
<!-- main area start  -->
<main>
    <!-- breadcrumb start -->
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title"><?php echo htmlspecialchars($product['name']); ?></h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                    <li class="breadcrumb-item"><?php echo htmlspecialchars($product['name']); ?></li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- shop single start -->
    <section class="shop-single-section pt-115 pb-385">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="product-single-wrap mb-30">
                        <div class="product_details_img">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane show active" id="home2" role="tabpanel" aria-labelledby="home-tab">
                                    <div class="pl_thumb">
                                        <img src="<?php echo htmlspecialchars($product_image); ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 product-details-col">
                    <div class="product-details mb-30">
                        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="price">
                            <?php if ($product['sale_price']): ?>
                                <span class="current">₹<?php echo number_format($product['sale_price'], 2); ?></span>
                                <span class="old">₹<?php echo number_format($product['price'], 2); ?></span>
                            <?php else: ?>
                                <span class="current">₹<?php echo number_format($product['price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($product['description'] ?: 'Premium quality health supplement from Protinut.')); ?>
                        </p>
                        <div class="product-option">
                            <form action="cart_action.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="product_name"
                                    value="<?php echo htmlspecialchars($product['name']); ?>">
                                <input type="hidden" name="product_price" value="<?php echo $effective_price; ?>">
                                <input type="hidden" name="product_image"
                                    value="<?php echo htmlspecialchars($product['image']); ?>">
                                <div class="product-row">
                                    <div>
                                        <input class="product-count" type="text" value="1" name="quantity">
                                    </div>
                                    <div class="add-to-cart-btn">
                                        <button class="xb-btn" type="submit"><i class="far fa-shopping-bag"></i>Add to
                                            cart</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="thb-product-meta-after mt-20">
                            <div class="product_meta">
                                <?php if ($product['category']): ?>
                                    <span class="posted_in">Category: <a
                                            href="#!"><?php echo htmlspecialchars($product['category']); ?></a></span>
                                <?php endif; ?>
                                <span class="product-share-wrap ul_li">Share:
                                    <a href="#!"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#!"><i class="fab fa-instagram"></i></a>
                                    <a href="#!"><i class="fab fa-twitter"></i></a>
                                    <a href="#!"><i class="fab fa-linkedin "></i></a>
                                </span>
                            </div>
                        </div>


                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->

            <div class="row">
                <div class="col col-xs-12">
                    <div class="single-product-info">
                        <!-- Nav tabs -->
                        <div class="tablist">
                            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                <li><button class="active" id="pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#tb-01">Product Details</button></li>
                                <li><button id="tab-two" data-bs-toggle="pill" data-bs-target="#tb-02">Additional
                                        imformation</button></li>
                                <!-- <li><button id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#tb-03">Review
                                        (09)</button></li> -->
                            </ul>
                        </div>

                        <!-- Tab panes -->
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="tb-01">
                                <p>Travelling salesman and above it there hung a picture that he had recently cut out of
                                    an illustrated magazine and housed in a nice, gilded frame. It showed a lady fitted
                                    out with a fur hat and fur boa who sat upright, raising a heavy fur muff that
                                    covered the whole of her lower arm towards the viewer</p>
                                <p> waved about helplessly as he looked. "What's happened to me?" he thought. It wasn't
                                    a dream. His room, a proper human room although a little too small, lay peacefully
                                    between its four familiar wallstrated magazine and housed in a nice, gilded frame.
                                    It showed a lady fitted out with a fur hat and fur boa who sat upright, raising a
                                    heavy fur muff that covered the whole of her lower arm towards the viewer. Gregor
                                    then turned to look out the window at the dull weather</p>
                            </div>
                            <div class="tab-pane fade" id="tb-02">
                                <p>Travelling salesman and above it there hung a picture that he had recently cut out of
                                    an illustrated magazine and housed in a nice, gilded frame. It showed a lady fitted
                                    out with a fur hat and fur boa who sat upright, raising a heavy fur muff that
                                    covered the whole of her lower arm towards the viewer</p>
                                <p> waved about helplessly as he looked. "What's happened to me?" he thought. It wasn't
                                    a dream. His room, a proper human room although a little too small, lay peacefully
                                    between its four familiar wallstrated magazine and housed in a nice, gilded frame.
                                    It showed a lady fitted out with a fur hat and fur boa who sat upright, raising a
                                    heavy fur muff that covered the whole of her lower arm towards the viewer. Gregor
                                    then turned to look out the window at the dull weather</p>
                            </div>
                            <div class="tab-pane fade" id="tb-03">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 col-xs-12">
                                        <div class="client-rv">
                                            <div class="client-pic">
                                                <img src="assets/img/avatar/shop_avatar1.jpg" alt>
                                            </div>
                                            <div class="details">
                                                <div class="name-rating-time">
                                                    <div class="name-rating">
                                                        <div>
                                                            <h4>Mice</h4>
                                                        </div>
                                                        <div class="rating">
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="time">
                                                        <span>1 day ago</span>
                                                    </div>
                                                </div>
                                                <div class="review-body">
                                                    <p>Helplessly as he looked What's happened to me he thought. It
                                                        wasn't a dreamtrated magazine and housed in a nice, gilded
                                                        frame. It showed a lady fitted</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="client-rv">
                                            <div class="client-pic">
                                                <img src="assets/img/avatar/shop_avatar2.jpg" alt>
                                            </div>
                                            <div class="details">
                                                <div class="name-rating-time">
                                                    <div class="name-rating">
                                                        <div>
                                                            <h4>Hone</h4>
                                                        </div>
                                                        <div class="rating">
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="time">
                                                        <span>1 day ago</span>
                                                    </div>
                                                </div>
                                                <div class="review-body">
                                                    <p>Helplessly as he looked What's happened to me he thought. It
                                                        wasn't a dreamtrated magazine and housed in a nice, gilded
                                                        frame. It showed a lady fitted</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="client-rv">
                                            <div class="client-pic">
                                                <img src="assets/img/avatar/shop_avatar3.jpg" alt>
                                            </div>
                                            <div class="details">
                                                <div class="name-rating-time">
                                                    <div class="name-rating">
                                                        <div>
                                                            <h4>Piloa</h4>
                                                        </div>
                                                        <div class="rating">
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                            <i class="fi flaticon-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="time">
                                                        <span>2 days ago</span>
                                                    </div>
                                                </div>
                                                <div class="review-body">
                                                    <p>Helplessly as he looked What's happened to me he thought. It
                                                        wasn't a dreamtrated magazine and housed in a nice, gilded
                                                        frame. It showed a lady fitted</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->

                                    <div class="col-lg-6 col-sm-12 col-xs-12 review-form-wrapper">
                                        <div class="review-form">
                                            <h4>Here you can review the item</h4>
                                            <form>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Name *"
                                                        required>
                                                </div>
                                                <div>
                                                    <input type="email" class="form-control" placeholder="Email *"
                                                        required>
                                                </div>
                                                <div>
                                                    <textarea class="form-control" placeholder="Review *"></textarea>
                                                </div>
                                                <div class="rating-wrapper">
                                                    <div class="rating">
                                                        <a href="#!" class="star-1">
                                                            <i class="fal fa-star"></i>
                                                        </a>
                                                        <a href="#!" class="star-1">
                                                            <i class="fal fa-star"></i>
                                                        </a>
                                                        <a href="#!" class="star-1">
                                                            <i class="fal fa-star"></i>
                                                        </a>
                                                        <a href="#!" class="star-1">
                                                            <i class="fal fa-star"></i>
                                                        </a>
                                                        <a href="#!" class="star-1">
                                                            <i class="fal fa-star"></i>
                                                        </a>
                                                    </div>
                                                    <div class="submit">
                                                        <button class="thm-btn thm-btn--black" type="submit">
                                                            <span class="btn-wrap">
                                                                <span>Submit</span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end row -->

            <div class="row">
                <div class="col col-xs-12">
                    <div class="realted-porduct">
                        <h3 class="title">Related Products</h3>
                        <div class="shop-area">
                            <div class="products">
                                <div class="row mb-none-30">
                                    <?php
                                    $related_result = mysqli_query($link, "SELECT * FROM products WHERE status='active' AND id != $product_id ORDER BY RAND() LIMIT 4");
                                    if ($related_result && mysqli_num_rows($related_result) > 0) {
                                        while ($rel = mysqli_fetch_assoc($related_result)) {
                                            $rel_image = $rel['image'] ?: 'assets/img/shop/product_05.png';
                                            $rel_price = $rel['sale_price'] ?: $rel['price'];
                                            ?>
                                            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                                <div class="product product-item text-center">
                                                    <div class="xb-item--img">
                                                        <a href="shop-single.php?id=<?php echo $rel['id']; ?>"><img
                                                                src="<?php echo htmlspecialchars($rel_image); ?>"
                                                                alt="<?php echo htmlspecialchars($rel['name']); ?>"></a>
                                                    </div>
                                                    <div class="xb-item--holder">
                                                        <h3 class="xb-item--title"><a
                                                                href="shop-single.php?id=<?php echo $rel['id']; ?>"><?php echo htmlspecialchars($rel['name']); ?></a>
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
                                                    <div class="xb-item--action ul_li mt-20">
                                                        <span
                                                            class="xb-item--price">₹<?php echo number_format($rel_price, 2); ?></span>
                                                        <a href="shop-single.php?id=<?php echo $rel['id']; ?>"><span
                                                                class="xb-item--cart-icon"><img src="assets/img/icon/bag.svg"
                                                                    alt=""></span><span class="xb-item--cart">view
                                                                product</span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                    } else {
                                        echo '<div class="col-12 text-center"><p>No related products found.</p></div>';
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- end of container -->
    </section>
    <!-- shop single start -->

</main>
<!-- main area end  -->
<?php include 'footer.php'; ?>
