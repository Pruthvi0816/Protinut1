<?php include("header.php"); ?>

<?php
// Handle status update
if (isset($_POST['update_status'])) {
    $id = (int) $_POST['order_id'];
    $status = mysqli_real_escape_string($link, $_POST['status']);

    // Also handle payment_status if it is being posted
    if (isset($_POST['payment_status'])) {
        $payment_status = mysqli_real_escape_string($link, $_POST['payment_status']);
        mysqli_query($link, "UPDATE orders SET status='$status', payment_status='$payment_status' WHERE id=$id");
    } else {
        mysqli_query($link, "UPDATE orders SET status='$status' WHERE id=$id");
    }
    echo "<script>window.location.href='orders.php?success=Order Status Updated'</script>";
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($link, "DELETE FROM order_items WHERE order_id=$id");
    mysqli_query($link, "DELETE FROM orders WHERE id=$id");
    echo "<script>window.location.href='orders.php?success=Order Deleted'</script>";
    exit;
}
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Orders</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Amount</th>
                                <th>Payment Info</th>
                                <th>Delivery Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($link, "SELECT * FROM orders ORDER BY id DESC");
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status = $row['status'] ?? 'Pending';
                                    $badge = 'bg-secondary';
                                    if ($status == 'Pending')
                                        $badge = 'bg-warning text-dark';
                                    elseif ($status == 'Processing')
                                        $badge = 'bg-info';
                                    elseif ($status == 'Shipped')
                                        $badge = 'bg-primary';
                                    elseif ($status == 'Delivered')
                                        $badge = 'bg-success';
                                    elseif ($status == 'Cancelled')
                                        $badge = 'bg-danger';
                                    ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <?php
                                            $method = ($row['payment_method'] == 'qr_payment') ? 'UPI' : 'COD';
                                            echo '<strong>' . $method . '</strong>';
                                            if ($method == 'UPI' && !empty($row['utr_number'])) {
                                                echo '<br><small class="text-muted">UTR: ' . htmlspecialchars($row['utr_number']) . '</small>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <select name="status" class="form-select form-select-sm mb-1"
                                                    style="width:130px;" onchange="this.form.submit()">
                                                    <?php foreach (['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'] as $s) { ?>
                                                        <option value="<?php echo $s; ?>" <?php echo ($status == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php $ps = $row['payment_status'] ?? 'pending'; ?>
                                                <select name="payment_status" class="form-select form-select-sm"
                                                    style="width:130px;" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo ($ps == 'pending') ? 'selected' : ''; ?>>
                                                        Pay: Pending</option>
                                                    <option value="paid" <?php echo ($ps == 'paid') ? 'selected' : ''; ?>>Pay:
                                                        Paid</option>
                                                    <option value="unpaid" <?php echo ($ps == 'unpaid') ? 'selected' : ''; ?>>Pay:
                                                        Unpaid</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="order_details.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-info"><i class="bx bx-show"></i></a>
                                            <a href="delete_action.php?type=order&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No orders found.</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
