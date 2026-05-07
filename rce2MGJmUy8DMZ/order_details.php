<?php include("header.php"); ?>

<?php
$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order = null;
$items = [];

if ($order_id > 0) {
    $result = mysqli_query($link, "SELECT * FROM orders WHERE id=$order_id LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
    }
    $items_result = mysqli_query($link, "SELECT * FROM order_items WHERE order_id=$order_id");
    if ($items_result) {
        while ($item = mysqli_fetch_assoc($items_result)) {
            $items[] = $item;
        }
    }
}
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Order Details</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item"><a href="orders.php">Orders</a></li>
                        <li class="breadcrumb-item active">#<?php echo $order_id; ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if ($order): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-3">Order Items</h6>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-3">Customer Info</h6>
                            <p><strong>Name:</strong>
                                <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <hr>
                            <h6 class="mb-3">Shipping Address</h6>
                            <p><?php echo htmlspecialchars($order['address']); ?></p>
                            <p><?php echo htmlspecialchars($order['city']) . ', ' . htmlspecialchars($order['postcode']); ?>
                            </p>
                            <p><?php echo htmlspecialchars($order['country']); ?></p>
                            <h6 class="mb-3">Payment Info</h6>
                            <p><strong>Method:</strong>
                                <?php echo ($order['payment_method'] == 'qr_payment') ? 'UPI / QR' : 'Cash on Delivery'; ?>
                            </p>
                            <?php if ($order['payment_method'] == 'qr_payment' && !empty($order['utr_number'])): ?>
                                <p><strong>UTR:</strong> <?php echo htmlspecialchars($order['utr_number']); ?></p>
                            <?php endif; ?>
                            <p><strong>Payment Status:</strong>
                                <?php
                                $ps = ucfirst($order['payment_status'] ?? 'pending');
                                $ps_badge = ($ps == 'Paid') ? 'bg-success' : (($ps == 'Unpaid') ? 'bg-danger' : 'bg-warning text-dark');
                                ?>
                                <span class="badge <?php echo $ps_badge; ?>"><?php echo $ps; ?></span>
                            </p>
                            <hr>
                            <p><strong>Delivery Status:</strong>
                                <span class="badge <?php
                                $s = $order['status'] ?? 'Pending';
                                echo $s == 'Delivered' ? 'bg-success' : ($s == 'Cancelled' ? 'bg-danger' : 'bg-warning text-dark');
                                ?>"><?php echo $s; ?></span>
                            </p>
                            <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?>
                            </p>
                            <hr>
                            <?php
                            $qr_data = "--- USER INFO ---\nName: {$order['first_name']} {$order['last_name']}\nPhone: {$order['phone']}\nEmail: {$order['email']}\n\n--- SHIPPING INFO ---\nAddress: {$order['address']}\nCity: {$order['city']}\nPostcode: {$order['postcode']}\nCountry: {$order['country']}\n\nOrder ID: {$order['id']}";
                            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);
                            ?>
                            <div class="text-center mt-3">
                                <img src="<?php echo $qr_url; ?>" alt="QR Code" style="width: 140px; height: 140px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                <p class="text-muted small mt-1 mb-3">Scan for Shipping Info</p>
                                <a href="receipt.php?id=<?php echo $order_id; ?>" target="_blank" class="btn btn-dark w-100"><i class="bx bx-receipt"></i> View Invoice</a>
                            </div>
                        </div>
                    </div>
                    <a href="orders.php" class="btn btn-secondary w-100 mt-3"><i class="bx bx-arrow-back"></i> Back to Orders</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center">Order not found. <a href="orders.php">Go back</a></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("footer.php"); ?>