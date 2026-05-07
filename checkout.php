<?php
require_once 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$user_res = mysqli_query($link, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_res);

include 'header.php';

// Initialize total
$total = 0;
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

$checkoutError = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);
?>

<!-- main area start  -->
<main>
    <!-- breadcrumb start -->
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title">Checkout</h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- start checkout-section -->
    <section class="checkout-section pt-115 pb-385">
        <div class="container">
            <div class="row">
                <div class="col col-xs-12">
                    <div class="woocommerce">
                        <?php if ($checkoutError): ?>
                            <div class="alert alert-danger" role="alert"
                                style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; margin-bottom: 20px; padding: 15px; border-radius: 4px;">
                                <?php echo htmlspecialchars($checkoutError, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>
                        <form name="checkout" method="post" class="checkout woocommerce-checkout"
                            action="checkout_action.php">
                            
                            <div class="address-selection-wrap mb-4" style="background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #eee; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                                <h4 style="font-size: 20px; margin-bottom: 20px; color: #060606; font-weight: 700; border-left: 4px solid #f7941d; padding-left: 15px;">Shipping Preference</h4>
                                <div style="display: flex; gap: 40px; flex-wrap: wrap;">
                                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-weight: 600; font-size: 16px; color: #333;">
                                        <input type="radio" name="address_option" value="saved" checked style="width: 20px; height: 20px; accent-color: #f7941d;"> Use Saved Address
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-weight: 600; font-size: 16px; color: #333;">
                                        <input type="radio" name="address_option" value="custom" style="width: 20px; height: 20px; accent-color: #f7941d;"> Enter Custom Address
                                    </label>
                                </div>
                            </div>

                            <?php
                            $full_name = $user['name'] ?? '';
                            $parts = explode(' ', trim($full_name));
                            $f_name = $parts[0] ?? '';
                            $l_name = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
                            ?>

                            <div class="col2-set" id="customer_details"
                                style="display:flex; flex-wrap:wrap; gap:30px; margin-bottom: 40px;">
                                <div class="coll-1" style="flex:1; min-width: 300px;">
                                    <div class="woocommerce-billing-fields">
                                        <h3
                                            style="margin-bottom: 20px; font-size: 24px; border-bottom: 2px solid #f7941d; padding-bottom: 10px;">
                                            Billing Details</h3>
                                        <p class="form-row form-row form-row-first validate-required">
                                            <label>First Name <abbr class="required" style="color:red;">*</abbr></label>
                                            <input type="text" class="input-text" name="billing_first_name" required
                                                value="<?php echo htmlspecialchars($f_name); ?>"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:15px;" />
                                        </p>
                                        <p class="form-row form-row form-row-last validate-required">
                                            <label>Last Name <abbr class="required" style="color:red;">*</abbr></label>
                                            <input type="text" class="input-text" name="billing_last_name" required
                                                value="<?php echo htmlspecialchars($l_name); ?>"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:15px;" />
                                        </p>
                                        <p class="form-row form-row form-row-wide validate-required validate-email">
                                            <label>Email Address <abbr class="required"
                                                    style="color:red;">*</abbr></label>
                                            <input type="email" class="input-text" name="billing_email" required
                                                value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:15px;" />
                                        </p>
                                        <p class="form-row form-row form-row-wide validate-required validate-phone">
                                            <label>Phone <abbr class="required" style="color:red;">*</abbr></label>
                                            <input type="tel" class="input-text" name="billing_phone" required
                                                value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:15px;" />
                                        </p>
                                        <p class="form-row form-row form-row-wide validate-required">
                                            <label>Country <abbr class="required" style="color:red;">*</abbr></label>
                                            <select name="billing_country" id="billing_country" class="country_to_state country_select"
                                                required
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:15px;">
                                                <option value="">Select a country...</option>
                                                <option value="US" <?php echo (($user['country'] ?? '') == 'US') ? 'selected' : ''; ?>>United States</option>
                                                <option value="UK" <?php echo (($user['country'] ?? '') == 'UK') ? 'selected' : ''; ?>>United Kingdom</option>
                                                <option value="CA" <?php echo (($user['country'] ?? '') == 'CA') ? 'selected' : ''; ?>>Canada</option>
                                                <option value="IN" <?php echo (($user['country'] ?? '') == 'IN' || !isset($user['country'])) ? 'selected' : ''; ?>>India</option>
                                            </select>
                                        </p>
                                        <p class="form-row form-row form-row-wide validate-required">
                                            <label>Street Address <abbr class="required"
                                                    style="color:red;">*</abbr></label>
                                            <input type="text" class="input-text" name="billing_address_1"
                                                value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                                                placeholder="House number and street name" required
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:15px;" />
                                        </p>
                                        <p class="form-row form-row form-row-wide validate-required">
                                            <label>Town / City <abbr class="required"
                                                    style="color:red;">*</abbr></label>
                                            <input type="text" class="input-text" name="billing_city" required
                                                value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:15px;" />
                                        </p>
                                        <p class="form-row form-row form-row-wide validate-required validate-postcode">
                                            <label>Postcode / ZIP <abbr class="required"
                                                    style="color:red;">*</abbr></label>
                                            <input type="text" class="input-text" name="billing_postcode" required
                                                value="<?php echo htmlspecialchars($user['postcode'] ?? ''); ?>"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; margin-bottom:20px;" />
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <h3 id="order_review_heading"
                                style="font-size: 24px; border-bottom: 2px solid #f7941d; padding-bottom: 10px; margin-bottom:20px;">
                                Your order</h3>
                            <div id="order_review" class="woocommerce-checkout-review-order"
                                style="background:#f9f9f9; padding:30px; border-radius:8px; border:1px solid #eee;">
                                <table class="shop_table woocommerce-checkout-review-order-table" style="width:100%;">
                                    <thead>
                                        <tr style="border-bottom:1px solid #ddd; text-align:left;">
                                            <th class="product-name" style="padding:15px 0;">Product</th>
                                            <th class="product-total" style="padding:15px 0; text-align:right;">Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($_SESSION['cart'])): ?>
                                            <tr>
                                                <td colspan="2" style="padding:20px 0;text-align:center;">Your cart is
                                                    empty.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                                <tr class="cart_item" style="border-bottom:1px dashed #ddd;">
                                                    <td class="product-name" style="padding:15px 0;">
                                                        <?php echo htmlspecialchars($item['name']); ?>&nbsp; <strong
                                                            class="product-quantity">&times;
                                                            <?php echo $item['quantity']; ?></strong>
                                                    </td>
                                                    <td class="product-total" style="text-align:right; padding:15px 0;">
                                                        <span class="woocommerce-Price-amount amount"><span
                                                                class="woocommerce-Price-currencySymbol">₹</span><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="cart-subtotal" style="border-bottom:1px dashed #ddd;">
                                            <th style="padding:15px 0;">Subtotal</th>
                                            <td style="text-align:right; padding:15px 0;"><span
                                                    class="woocommerce-Price-amount amount"><span
                                                        class="woocommerce-Price-currencySymbol">₹</span><?php echo number_format($total, 2); ?></span>
                                            </td>
                                        </tr>
                                        <tr class="shipping" style="border-bottom:1px dashed #ddd;">
                                            <th style="padding:15px 0;">Shipping</th>
                                            <td data-title="Shipping" style="text-align:right; padding:15px 0;">Free
                                                Shipping</td>
                                        </tr>
                                        <tr class="order-total">
                                            <th style="padding:20px 0; font-size:18px;">Total</th>
                                            <td style="text-align:right; padding:20px 0;"><strong><span
                                                        class="woocommerce-Price-amount amount"
                                                        style="color:#f7941d; font-size:22px;"><span
                                                            class="woocommerce-Price-currencySymbol">₹</span><?php echo number_format($total, 2); ?></span></strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <div id="payment" class="woocommerce-checkout-payment" style="margin-top:20px;">
                                    <ul class="wc_payment_methods payment_methods methods"
                                        style="list-style:none; padding:0; margin:0 0 20px 0;">
                                        <li class="wc_payment_method payment_method_cod"
                                            style="padding:15px; border:1px solid #ddd; background:#fff; border-radius:4px; margin-bottom:10px;">
                                            <input id="payment_method_cod" type="radio" class="input-radio"
                                                name="payment_method" value="cod" checked="checked" />
                                            <label for="payment_method_cod"
                                                style="font-weight:bold; margin-left:10px;">Cash on Delivery</label>
                                            <div class="payment_box payment_method_cod"
                                                style="margin-top:10px; color:#666;">
                                                <p>Pay with cash upon delivery.</p>
                                            </div>
                                        </li>
                                        <li class="wc_payment_method payment_method_qr"
                                            style="padding:15px; border:1px solid #ddd; background:#fff; border-radius:4px;">
                                            <input id="payment_method_qr" type="radio" class="input-radio"
                                                name="payment_method" value="qr_payment" />
                                            <label for="payment_method_qr"
                                                style="font-weight:bold; margin-left:10px;">QR Payment (UPI)</label>
                                            <div class="payment_box payment_method_qr"
                                                style="margin-top:10px; color:#666;">
                                                <p>Pay instantly using your UPI app by scanning our QR code. You'll need
                                                    to enter the UTR number in the next step.</p>
                                            </div>
                                            <input type="hidden" name="utr_number" id="hidden_utr_number" value="">
                                        </li>
                                    </ul>
                                    <div class="form-row place-order text-center">
                                        <input type="submit" class="xb-btn" name="woocommerce_checkout_place_order"
                                            value="Place order"
                                            style="width: 100%; border-radius: 4px; padding: 15px; font-size: 18px; margin-top: 20px; text-transform:uppercase; font-weight:bold;" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end checkout-section -->
</main>
<!-- main area end  -->

<!-- QR Payment Modal -->
<div id="qrPaymentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999;">
    <div id="qrModalOverlay"
        style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);">
    </div>
    <div
        style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; border-radius:16px; padding:30px; max-width:400px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); z-index:100000;">
        <button id="qrModalClose"
            style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color:#999; line-height:1;">&times;</button>
        <h3 style="font-family:var(--font-heading); font-size:20px; color:#060606; margin-bottom:10px;">Pay via UPI / QR
        </h3>
        <p style="color:#666; font-size:14px; margin-bottom:20px;">Please scan the QR code to pay <strong
                style="color:#000;">₹<?php echo number_format($total, 2); ?></strong>. Once paid, enter your
            UTR/Reference number below.</p>

        <div style="background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:20px; border:1px solid #eaeaea;">
            <!-- Placeholder QR Image. The user can replace this with actual QR later -->
            <?php
            $upi_link = "upi://pay?pa=9699961011@ybl&pn=Protinut&am=" . $total . "&cu=INR";
            $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($upi_link);
            ?>
            <img src="<?php echo $qr_api_url; ?>" alt="QR Code Placeholder"
                style="width:200px; height:200px; border-radius:8px;">
        </div>

        <div style="text-align:left; margin-bottom:20px;">
            <label style="display:block; font-size:14px; font-weight:bold; margin-bottom:8px; color:#333;">UTR /
                Reference Number <span style="color:red">*</span></label>
            <input type="text" id="utrInput" placeholder="Enter 12-digit UTR No."
                inputmode="numeric"
                autocomplete="off"
                maxlength="12"
                pattern="\d{12}"
                style="width:100%; padding:12px; border:1px solid #ddd; border-radius:4px; font-size:15px;">
            <span id="utrError" style="color:red; font-size:12px; display:none; margin-top:5px;">Please enter a valid
                UTR number.</span>
        </div>

        <button id="confirmQrPaymentBtn"
            style="display:block; width:100%; padding:14px 20px; background:var(--color-primary); color:#fff; font-weight:700; font-size:16px; text-transform:uppercase; border:none; border-radius:4px; cursor:pointer; transition:0.3s;">
            Confirm & Place Order
        </button>
    </div>
</div>

<!-- QR Payment Modal End -->

<!-- Flipkart-style Order Success Overlay -->
<div id="orderSuccessOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:999999; background:#fff; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
    <div class="success-checkmark">
        <div class="check-icon">
            <span class="icon-line line-tip"></span>
            <span class="icon-line line-long"></span>
            <div class="icon-circle"></div>
            <div class="icon-fix"></div>
        </div>
    </div>
    <h2 style="font-family:var(--font-heading); color:#28a745; font-weight:700; font-size:32px; margin-top:35px; animation: fadeInUp 0.5s ease-out 0.8s forwards; opacity:0; transform:translateY(20px);">Order Placed Successfully!</h2>
    <p style="color:#666; font-size:18px; margin-top:10px; animation: fadeInUp 0.5s ease-out 1s forwards; opacity:0; transform:translateY(20px);">Thank you for shopping with Protinut.</p>
    <p style="color:#999; font-size:15px; margin-top:8px; animation: fadeInUp 0.5s ease-out 1.2s forwards; opacity:0; transform:translateY(20px);">Redirecting to your orders...</p>

    <style>
        .success-checkmark {
            width: 90px;
            height: 90px;
            margin: 0 auto;
        }
        .check-icon {
            width: 80px;
            height: 80px;
            position: relative;
            border-radius: 50%;
            box-sizing: content-box;
            border: 4px solid #4CAF50;
        }
        .check-icon::before {
            top: 3px;
            left: -2px;
            width: 30px;
            transform-origin: 100% 50%;
            border-radius: 100px 0 0 100px;
        }
        .check-icon::after {
            top: 0;
            left: 30px;
            width: 60px;
            transform-origin: 0 50%;
            border-radius: 0 100px 100px 0;
            animation: rotate-circle 4.25s ease-in;
        }
        .check-icon::before, .check-icon::after {
            content: '';
            height: 100px;
            position: absolute;
            background: #FFFFFF;
            transform: rotate(-45deg);
        }
        .icon-line {
            height: 5px;
            background-color: #4CAF50;
            display: block;
            border-radius: 2px;
            position: absolute;
            z-index: 10;
        }
        .icon-line.line-tip {
            top: 46px;
            left: 14px;
            width: 25px;
            transform: rotate(45deg);
            animation: icon-line-tip 0.75s;
        }
        .icon-line.line-long {
            top: 38px;
            right: 8px;
            width: 47px;
            transform: rotate(-45deg);
            animation: icon-line-long 0.75s;
        }
        .icon-circle {
            top: -4px;
            left: -4px;
            z-index: 10;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            position: absolute;
            box-sizing: content-box;
            border: 4px solid rgba(76, 175, 80, .5);
        }
        .icon-fix {
            top: 8px;
            width: 5px;
            left: 26px;
            z-index: 1;
            height: 85px;
            position: absolute;
            transform: rotate(-45deg);
            background-color: #FFFFFF;
        }
        @keyframes rotate-circle {
            0% { transform: rotate(-45deg); }
            5% { transform: rotate(-45deg); }
            12% { transform: rotate(-405deg); }
            100% { transform: rotate(-405deg); }
        }
        @keyframes icon-line-tip {
            0% { width: 0; left: 1px; top: 19px; }
            54% { width: 0; left: 1px; top: 19px; }
            70% { width: 50px; left: -8px; top: 37px; }
            84% { width: 17px; left: 21px; top: 48px; }
            100% { width: 25px; left: 14px; top: 46px; }
        }
        @keyframes icon-line-long {
            0% { width: 0; right: 46px; top: 54px; }
            65% { width: 0; right: 46px; top: 54px; }
            84% { width: 55px; right: 0px; top: 35px; }
            100% { width: 47px; right: 8px; top: 38px; }
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>

<?php
$savedDataJson = json_encode([
    'f_name' => $f_name,
    'l_name' => $l_name,
    'email' => $user['email'] ?? '',
    'phone' => $user['phone'] ?? '',
    'country' => $user['country'] ?? 'IN',
    'address' => $user['address'] ?? '',
    'city' => $user['city'] ?? '',
    'postcode' => $user['postcode'] ?? ''
], JSON_UNESCAPED_SLASHES);

$pageFooterScripts = <<<HTML
<script>
    $(document).ready(function () {
        const savedData = $savedDataJson;

        $('input[name="address_option"]').on('change', function () {
            if ($(this).val() === 'custom') {
                $('input[name="billing_first_name"]').val('');
                $('input[name="billing_last_name"]').val('');
                $('input[name="billing_email"]').val('');
                $('input[name="billing_phone"]').val('');
                $('#billing_country').val('');
                $('input[name="billing_address_1"]').val('');
                $('input[name="billing_city"]').val('');
                $('input[name="billing_postcode"]').val('');
            } else {
                $('input[name="billing_first_name"]').val(savedData.f_name);
                $('input[name="billing_last_name"]').val(savedData.l_name);
                $('input[name="billing_email"]').val(savedData.email);
                $('input[name="billing_phone"]').val(savedData.phone);
                $('#billing_country').val(savedData.country);
                $('input[name="billing_address_1"]').val(savedData.address);
                $('input[name="billing_city"]').val(savedData.city);
                $('input[name="billing_postcode"]').val(savedData.postcode);
            }
        });

        var checkoutForm = $('form.checkout');

        function processCheckout() {
            var formData = checkoutForm.serialize();
            
            var btn = $('input[name="woocommerce_checkout_place_order"]');
            var btnOrigText = btn.val();
            btn.val('Processing...').prop('disabled', true);
            
            var qrBtn = $('#confirmQrPaymentBtn');
            var qrBtnOrigText = qrBtn.text();
            qrBtn.text('Processing...').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: checkoutForm.attr('action'),
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#orderSuccessOverlay').css('display', 'flex').hide().fadeIn(300);
                        setTimeout(function(){
                            window.location.href = response.redirect;
                        }, 3500);
                    } else {
                        btn.val(btnOrigText).prop('disabled', false);
                        qrBtn.text(qrBtnOrigText).prop('disabled', false);
                        
                        if (response.redirect && response.redirect !== '') {
                             window.location.href = response.redirect;
                        } else {
                             alert(response.message);
                        }
                    }
                },
                error: function() {
                    btn.val(btnOrigText).prop('disabled', false);
                    qrBtn.text(qrBtnOrigText).prop('disabled', false);
                    alert("System error occurred while submitting. Please try again.");
                }
            });
        }

        checkoutForm.on('submit', function (e) {
            e.preventDefault();
            
            if (!checkoutForm[0].checkValidity()) {
                checkoutForm[0].reportValidity();
                return;
            }

            var selectedMethod = $('input[name="payment_method"]:checked').val();

            if (selectedMethod === 'qr_payment' && $('#hidden_utr_number').val() === '') {
                $('#qrPaymentModal').fadeIn();
            } else {
                processCheckout();
            }
        });

        $('#qrModalClose, #qrModalOverlay').on('click', function () {
            $('#qrPaymentModal').fadeOut();
        });

        $('#confirmQrPaymentBtn').on('click', function () {
            var utr = $('#utrInput').val().trim();
            var isValidUtr = /^\d{12}$/.test(utr);
            if (!isValidUtr) {
                $('#utrError').show();
            } else {
                $('#utrError').hide();
                $('#hidden_utr_number').val(utr);
                $('#qrPaymentModal').fadeOut();
                processCheckout();
            }
        });

        $('input[name="payment_method"]').on('change', function () {
            $('.payment_box').slideUp();
            $('.payment_box.payment_method_' + $(this).val()).slideDown();
            if ($(this).val() !== 'qr_payment') {
                $('#hidden_utr_number').val('');
                $('#utrInput').val('');
                $('#utrError').hide();
            }
        });

        $('.payment_box').hide();
        $('.payment_box.payment_method_' + $('input[name="payment_method"]:checked').val()).show();
    });
</script>
HTML;
include 'footer.php';
?>
