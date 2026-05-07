<?php
require_once 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_id = (int) $_POST['cancel_order_id'];
    $update = mysqli_query($link, "UPDATE orders SET status='Cancelled' WHERE id=$cancel_id AND user_id=$user_id AND status='Pending'");
    if ($update) {
        $_SESSION['order_msg'] = "Notice: Order log updated. Order was successfully cancelled.";
    }
    header('Location: my_orders.php');
    exit();
}

include 'header.php';

$msg = $_SESSION['order_msg'] ?? '';
unset($_SESSION['order_msg']);
?>

<main>
    <!-- breadcrumb start -->
    <section class="breadcrumb position-bottom bg_img" data-background="assets/img/bg/page_title.png">
        <div class="container">
            <div class="breadcrumb__content text-center">
                <h2 class="breadcrumb__title">My Orders</h2>
                <ul class="breadcrumb__list clearfix">
                    <li class="breadcrumb-item"><a href="index.php">Protinut</a></li>
                    <li class="breadcrumb-item">My Orders</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumb end -->

    <!-- start orders-section -->
    <section class="cart-section woocommerce-cart pt-115 pb-385">
        <div class="container">
            <div class="row">
                <div class="col col-xs-12">
                    <div class="woocommerce">
                        <?php if ($msg): ?>
                            <div class="alert alert-info" style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px; border:1px solid #f5c6cb;">
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php endif; ?>

                        <table class="shop_table shop_table_responsive cart"
                            style="border-collapse: collapse; width: 100%;">
                            <thead>
                                <tr style="background:#f9f9f9; border-bottom:2px solid #ddd;">
                                    <th style="padding:15px; font-weight:600; text-align: left;">Order #</th>
                                    <th style="padding:15px; font-weight:600; text-align: left;">Date</th>
                                    <th style="padding:15px; font-weight:600; text-align: left;">Status</th>
                                    <th style="padding:15px; font-weight:600; text-align: left;">Total</th>
                                    <th style="padding:15px; font-weight:600; text-align: left;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id ASC";
                                $res = mysqli_query($link, $sql);
                                $total_orders = mysqli_num_rows($res);
                                if ($total_orders == 0):
                                    ?>
                                    <tr>
                                        <td colspan="5" class="text-center" style="padding:50px 0;">
                                            <h4 style="margin-bottom: 20px;">You haven't placed any orders yet.</h4>
                                            <a href="shop.php" class="xb-btn">Go Shopping</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $seq = 1;
                                    while ($row = mysqli_fetch_assoc($res)):
                                        $status = $row['status'] ?? 'Pending';
                                        $badge_color = '#6c757d'; // Default
                                        if ($status == 'Pending')
                                            $badge_color = '#ffc107'; // Warning
                                        else if ($status == 'Processing')
                                            $badge_color = '#17a2b8'; // Info
                                        else if ($status == 'Shipped')
                                            $badge_color = '#0d6efd'; // Primary
                                        else if ($status == 'Delivered')
                                            $badge_color = '#28a745'; // Success
                                        else if ($status == 'Cancelled')
                                            $badge_color = '#dc3545'; // Danger
                                        ?>
                                        <tr style="border-bottom:1px solid #ddd;">
                                            <td style="padding: 15px;"><strong>#<?php echo $seq; ?></strong></td>
                                            <td style="padding: 15px;">
                                                <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
                                            </td>
                                            <td style="padding: 15px;">
                                                <span
                                                    style="padding: 5px 10px; border-radius: 4px; color: #fff; font-size: 13px; font-weight: bold; background-color: <?php echo $badge_color; ?>;">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>
                                            <td style="padding: 15px;">₹<?php echo number_format($row['total_amount'], 2); ?>
                                            </td>
                                            <td style="padding: 15px;">
                                                <a href="my_order_details.php?id=<?php echo $row['id']; ?>"
                                                    style="color:#000; font-weight:bold; border-bottom:1px solid #000; padding-bottom:2px; display:inline-block; margin-bottom:5px;">View
                                                    Details</a>
                                                
                                                <?php if ($status == 'Pending'): ?>
                                                    <br>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                        <input type="hidden" name="cancel_order_id" value="<?php echo $row['id']; ?>">
                                                        <button type="submit" style="background:none; border:none; color:#dc3545; font-weight:bold; border-bottom:1px solid #dc3545; padding:0; padding-bottom:2px; margin-top:5px; cursor:pointer; font-size:13px;">
                                                            Cancel Order
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($status == 'Cancelled'): ?>
                                                    <br>
                                                    <span style="color:#dc3545; font-size:12px; font-weight:bold; display:inline-block; margin-top:5px;">
                                                        Order cancelled
                                                    </span>
                                                <?php endif; ?>

                                                <?php if ($status !== 'Cancelled' && isset($row['payment_status']) && strtolower($row['payment_status']) === 'paid'): ?>
                                                    <br>
                                                    <a href="receipt.php?id=<?php echo $row['id']; ?>" target="_blank"
                                                        style="color:#28a745; font-weight:bold; border-bottom:1px solid #28a745; padding-bottom:2px; display:inline-block; margin-top:5px; font-size:13px;">
                                                        Download Receipt
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php 
                                    $seq++;
                                    endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>