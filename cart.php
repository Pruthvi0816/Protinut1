<?php
require_once 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'header.php';

// Initialize total
$total = 0;
// Make sure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!-- main area start  -->
<main>
    <!-- breadcrumb start -->
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title">Cart</h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item">Cart</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- start cart-section -->
    <section class="cart-section woocommerce-cart pt-115 pb-385">
        <div class="container">
            <div class="row">
                <div class="col col-xs-12">
                    <div class="woocommerce">
                        <table class="shop_table shop_table_responsive cart">
                            <thead>
                                <tr style="background:#f9f9f9; border-bottom:2px solid #ddd;">
                                    <th class="product-remove">&nbsp;</th>
                                    <th class="product-thumbnail">&nbsp;</th>
                                    <th class="product-name" style="padding:15px; font-weight:600;">Product</th>
                                    <th class="product-price" style="padding:15px; font-weight:600;">Price</th>
                                    <th class="product-quantity" style="padding:15px; font-weight:600;">Quantity</th>
                                    <th class="product-subtotal" style="padding:15px; font-weight:600;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($_SESSION['cart'])): ?>
                                    <tr>
                                        <td colspan="6" class="text-center" style="padding:50px 0;">
                                            <h4 style="margin-bottom: 20px;">Your cart is currently empty.</h4>
                                            <a href="shop.php" class="xb-btn">Return to Shop</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                        <?php
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                        ?>
                                        <tr class="cart_single" style="border-bottom:1px solid #eee;">
                                            <td class="product-remove" style="text-align:center; padding:15px;">
                                                <form action="cart_action.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="remove" title="Remove this item"
                                                        style="background:none;border:none;font-size:24px;color:#e74c3c;">&times;</button>
                                                </form>
                                            </td>
                                            <td class="product-thumbnail" style="padding:15px;">
                                                <a href="shop-single.php?id=<?php echo $item['id']; ?>">
                                                    <img width="80" height="80"
                                                        style="object-fit:contain; border:1px solid #ddd; border-radius:4px; padding:5px;"
                                                        src="<?php echo htmlspecialchars($item['image']); ?>"
                                                        alt="<?php echo htmlspecialchars($item['name']); ?>" />
                                                </a>
                                            </td>
                                            <td class="product-name" data-title="Product" style="padding:15px;">
                                                <a href="shop-single.php?id=<?php echo $item['id']; ?>"
                                                    style="color:#333; font-weight:500;"><?php echo htmlspecialchars($item['name']); ?></a>
                                            </td>
                                            <td class="product-price" data-title="Price" style="padding:15px;">
                                                <span
                                                    class="woocommerce-Price-amount amount">₹<?php echo number_format($item['price'], 2); ?></span>
                                            </td>
                                            <td class="product-quantity" data-title="Quantity" style="padding:15px;">
                                                <div class="quantity-control" style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: fit-content; background: #fff; margin: 0 auto;">
                                                    <button type="button" class="qty-btn minus" style="width: 35px; height: 40px; background: #f9f9f9; border: none; cursor: pointer; transition: 0.3s; font-size: 18px; color: #333; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ddd;">-</button>
                                                    <input type="number" step="1" min="1" name="quantity" value="<?php echo $item['quantity']; ?>" title="Qty" class="qty-input" style="width: 45px; height: 40px; text-align: center; border: none; padding: 0; font-weight: 600; font-size: 15px; -moz-appearance: textfield; pointer-events: none;" readonly />
                                                    <button type="button" class="qty-btn plus" style="width: 35px; height: 40px; background: #f9f9f9; border: none; cursor: pointer; transition: 0.3s; font-size: 18px; color: #333; display: flex; align-items: center; justify-content: center; border-left: 1px solid #ddd;">+</button>
                                                </div>
                                                <form action="cart_action.php" method="post" class="qty-form" style="display:none;">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <input type="hidden" name="quantity" class="hidden-qty" value="<?php echo $item['quantity']; ?>">
                                                </form>
                                            </td>
                                            <td class="product-subtotal" data-title="Total" style="padding:15px;">
                                                <span class="woocommerce-Price-amount amount"
                                                    style="color:#f7941d;font-weight:bold;">₹<?php echo number_format($subtotal, 2); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <?php if (!empty($_SESSION['cart'])): ?>
                            <div class="row justify-content-end" style="margin-top: 50px;">
                                <div class="col-lg-5 col-md-8 col-sm-12">
                                    <div class="cart-collaterals">
                                        <div class="cart_totals calculated_shipping"
                                            style="background:#f9f9f9; padding:30px; border-radius:8px; border:1px solid #eee;">
                                            <h2
                                                style="margin-bottom:20px; border-bottom:2px solid #f7941d; padding-bottom:10px; font-size: 24px;">
                                                Cart Totals</h2>
                                            <table class="shop_table shop_table_responsive" style="width:100%;">
                                                <tr class="cart-subtotal" style="border-bottom:1px dashed #ddd;">
                                                    <th style="padding:15px 0; color:#555;">Subtotal</th>
                                                    <td data-title="Subtotal" style="text-align:right;"><span
                                                            class="woocommerce-Price-amount amount"
                                                            style="font-weight:600;">
                                                            ₹<?php echo number_format($total, 2); ?></span>
                                                    </td>
                                                </tr>
                                                <tr class="shipping" style="border-bottom:1px dashed #ddd;">
                                                    <th style="padding:15px 0; color:#555;">Shipping</th>
                                                    <td data-title="Shipping" style="text-align:right; color:#555;">Free
                                                        Shipping</td>
                                                </tr>
                                                <tr class="order-total">
                                                    <th style="padding:20px 0; font-size:18px; color:#333;">Total</th>
                                                    <td data-title="Total" style="text-align:right;"><strong><span
                                                                class="woocommerce-Price-amount amount"
                                                                style="color:#f7941d; font-size:22px;">₹<?php echo number_format($total, 2); ?></span></strong>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="wc-proceed-to-checkout"
                                                style="margin-top: 30px; text-align: center;">
                                                <a href="checkout.php" class="xb-btn"
                                                    style="width:100%; display:block; padding:15px; font-size:18px; text-align:center;">
                                                    Proceed to checkout</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end cart-section -->
</main>
<!-- main area end  -->
<?php
$pageFooterScripts = <<<'HTML'
<script>
    $(document).ready(function () {
        $('.qty-btn.plus').on('click', function () {
            var parent = $(this).closest('td');
            var input = parent.find('.qty-input');
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal)) {
                input.val(currentVal + 1);
                updateCart(parent);
            }
        });

        $('.qty-btn.minus').on('click', function () {
            var parent = $(this).closest('td');
            var input = parent.find('.qty-input');
            var currentVal = parseInt(input.val());
            if (!isNaN(currentVal) && currentVal > 1) {
                input.val(currentVal - 1);
                updateCart(parent);
            }
        });

        function updateCart(parent) {
            var newQty = parent.find('.qty-input').val();
            var form = parent.find('.qty-form');
            form.find('.hidden-qty').val(newQty);
            form.submit();
        }
    });
</script>

<style>
    .qty-btn:hover {
        background-color: #f0f0f0 !important;
        color: #f7941d !important;
    }

    .quantity-control {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
    }

    .quantity-control:hover {
        border-color: #f7941d !important;
    }
</style>
HTML;
include 'footer.php';
?>
