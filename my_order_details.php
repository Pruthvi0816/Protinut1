<?php
require_once 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'header.php';

$user_id = (int) $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$order_res = mysqli_query($link, "SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id LIMIT 1");
if (mysqli_num_rows($order_res) == 0) {
    echo "<script>alert('Order not found!'); window.location.href='my_orders.php';</script>";
    exit();
}
$order = mysqli_fetch_assoc($order_res);
$status = $order['status'] ?? 'Pending';
$badge_color = '#6c757d';
if ($status == 'Pending')
    $badge_color = '#ffc107';
else if ($status == 'Processing')
    $badge_color = '#17a2b8';
else if ($status == 'Shipped')
    $badge_color = '#0d6efd';
else if ($status == 'Delivered')
    $badge_color = '#28a745';
else if ($status == 'Cancelled')
    $badge_color = '#dc3545';

// Handle Cancellation
if (isset($_POST['cancel_order']) && $status == 'Pending') {
    mysqli_query($link, "UPDATE orders SET status='Cancelled' WHERE id=$order_id");
    echo "<script>alert('Your order has been cancelled.'); window.location.href='my_order_details.php?id=$order_id';</script>";
    exit;
}
?>

<main>
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title">Order Details</h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item"><a href="my_orders.php">My Orders</a></li>
                    <li class="breadcrumb-item">Order #<?php echo $order_id; ?></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="cart-section woocommerce-cart pt-115 pb-385">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="woocommerce" style="background:#f9f9f9; padding: 30px; border-radius: 8px;">
                        <h3 style="border-bottom: 2px solid #ddd; padding-bottom:15px; margin-bottom: 25px;">Items in
                            Order #<?php echo $order_id; ?></h3>
                        <table class="shop_table shop_table_responsive cart"
                            style="border-collapse: collapse; width: 100%;">
                            <thead>
                                <tr style="border-bottom:2px solid #ddd;">
                                    <th style="padding:15px; font-weight:600; text-align: left;">Product</th>
                                    <th style="padding:15px; font-weight:600; text-align: left;">Price</th>
                                    <th style="padding:15px; font-weight:600; text-align: left;">Qty</th>
                                    <th style="padding:15px; font-weight:600; text-align: left;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $items = mysqli_query($link, "SELECT * FROM order_items WHERE order_id=$order_id");
                                while ($item = mysqli_fetch_assoc($items)):
                                    ?>
                                    <tr style="border-bottom:1px solid #ddd;">
                                        <td style="padding: 15px;">
                                            <a href="shop-single.php?id=<?php echo $item['product_id']; ?>"
                                                style="color:#000; font-weight:500;">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </a>
                                        </td>
                                        <td style="padding: 15px;">₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td style="padding: 15px;"><?php echo $item['quantity']; ?></td>
                                        <td style="padding: 15px;">
                                            ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align: right; padding: 15px; font-size:18px;">Total
                                        Paid:</th>
                                    <th style="padding: 15px; font-size:18px; color: #f7941d;">
                                        ₹<?php echo number_format($order['total_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div style="background:#f9f9f9; padding: 30px; border-radius: 8px;">
                        <h4 style="border-bottom: 2px solid #ddd; padding-bottom:15px; margin-bottom: 20px;">Order
                            Summary</h4>
                        <ul style="list-style:none; padding:0; margin:0 0 20px 0;">
                            <li style="margin-bottom: 10px;"><strong>Date:</strong>
                                <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></li>
                            <li style="margin-bottom: 10px;">
                                <strong>Status:</strong>
                                <span
                                    style="padding: 3px 8px; border-radius: 4px; color: #fff; font-size: 12px; font-weight: bold; background-color: <?php echo $badge_color; ?>;">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </li>
                        </ul>

                        <h4
                            style="border-bottom: 2px solid #ddd; padding-bottom:15px; margin-bottom: 20px; margin-top:30px;">
                            Shipping Details</h4>
                        <p style="margin: 0; line-height: 1.6;">
                            <strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong><br>
                            <?php echo htmlspecialchars($order['address']); ?><br>
                            <?php echo htmlspecialchars($order['city']); ?>,
                            <?php echo htmlspecialchars($order['postcode']); ?><br>
                            <?php echo htmlspecialchars($order['country']); ?><br><br>
                            <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?>
                        </p>

                        <div style="margin-top: 25px; border-top: 2px solid #ddd; padding-top: 20px;">
                            <?php if (strtolower($order['payment_status'] ?? '') === 'paid'): ?>
                                <a href="receipt.php?id=<?php echo $order_id; ?>" target="_blank"
                                   style="display: block; text-align: center; width: 100%; background: #000; color: #fff; padding: 10px; font-weight: bold; border-radius: 4px; transition: 0.3s; text-transform: uppercase; text-decoration: none;">View Invoice</a>
                            <?php else: ?>
                                <a href="javascript:void(0);" onclick="alert('Receipt is not available yet.\nYour payment status is currently marked as: Unpaid\n\nReceipts are only generated for fully paid orders.');"
                                   style="display: block; text-align: center; width: 100%; background: #999; color: #fff; padding: 10px; font-weight: bold; border-radius: 4px; transition: 0.3s; text-transform: uppercase; text-decoration: none; cursor: not-allowed;">View Invoice</a>
                            <?php endif; ?>
                        </div>

                        <?php if ($status == 'Pending'): ?>
                            <div style="margin-top: 30px; border-top: 2px solid #ddd; padding-top: 20px;">
                                <form method="POST"
                                    onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                                    <input type="hidden" name="cancel_order" value="1">
                                    <button type="submit"
                                        style="width: 100%; background: transparent; border: 2px solid #dc3545; color: #dc3545; padding: 10px; font-weight: bold; border-radius: 4px; cursor: pointer; transition: 0.3s; text-transform: uppercase;">Cancel
                                        Order</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>