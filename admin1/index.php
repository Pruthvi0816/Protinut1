<?php include("header.php"); ?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Orders</p>
                                <h4 class="my-1 text-info">
                                    <?php
                                    $r = mysqli_query($link, "SELECT COUNT(*) as c FROM orders");
                                    $row = mysqli_fetch_assoc($r);
                                    echo $row['c'];
                                    ?>
                                </h4>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto"><i
                                    class='bx bx-cart'></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Revenue</p>
                                <h4 class="my-1 text-success">
                                    ₹<?php
                                    $r = mysqli_query($link, "SELECT COALESCE(SUM(total_amount),0) as s FROM orders");
                                    $row = mysqli_fetch_assoc($r);
                                    echo number_format($row['s'], 2);
                                    ?>
                                </h4>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i
                                    class='bx bx-rupee'></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Products</p>
                                <h4 class="my-1 text-warning">
                                    <?php
                                    $r = mysqli_query($link, "SELECT COUNT(*) as c FROM products");
                                    $row = mysqli_fetch_assoc($r);
                                    echo $row['c'];
                                    ?>
                                </h4>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                    class='bx bx-package'></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Customers</p>
                                <h4 class="my-1 text-danger">
                                    <?php
                                    $r = mysqli_query($link, "SELECT COUNT(*) as c FROM users");
                                    $row = mysqli_fetch_assoc($r);
                                    echo $row['c'];
                                    ?>
                                </h4>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i
                                    class='bx bx-group'></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Recent Orders</h6>
                    </div>
                    <div class="ms-auto"><a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a></div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM orders ORDER BY id DESC LIMIT 10";
                            $result = mysqli_query($link, $sql);
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $badge = 'bg-secondary';
                                    $status = $row['status'] ?? 'Pending';
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
                                        <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td><span class="badge <?php echo $badge; ?>"><?php echo $status; ?></span></td>
                                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='5'>No orders yet.</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Unread Messages -->
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Unread Messages</h6>
                    </div>
                    <div class="ms-auto"><a href="contacts.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM contacts WHERE is_read=0 ORDER BY id DESC LIMIT 5";
                            $result = mysqli_query($link, $sql);
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['message'], 0, 60)) . '...'; ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='4'>No unread messages.</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include("footer.php"); ?>