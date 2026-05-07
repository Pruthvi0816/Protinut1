<?php include("header.php"); ?>

<!-- Logic to handle updates -->
<?php
// Handle Status Update via POST
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Determine status logic if needed, or just trust the dropdown
    // For "Expired", we might want to set end_date to yesterday? Or just label it.
    // The previous logic for 'update_device' (full edit) is not used by the simple dropdown.

    $stmt = $link->prepare("UPDATE devices SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='device_control.php?success=Status Updated'</script>";
    } else {
        echo "<script>window.location.href='device_control.php?error=Failed to Update Status'</script>";
    }
}

// Handle Delete via POST
if (isset($_POST['delete_device'])) {
    $id = $_POST['delete_id'];
    $stmt = $link->prepare("DELETE FROM devices WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>window.location.href='device_control.php?success=Device Deleted Successfully'</script>";
    } else {
        echo "<script>window.location.href='device_control.php?error=Failed to Delete Device'</script>";
    }
}
?>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Device Control</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Device Control</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="card">
            <div class="card-body">
                <div class="d-lg-flex align-items-center mb-4 gap-3">
                    <div class="position-relative">
                        <input type="text" class="form-control ps-5 radius-30" placeholder="Search Device"> <span
                            class="position-absolute top-50 product-show translate-middle-y"><i
                                class="bx bx-search"></i></span>
                    </div>
                    <!-- <div class="ms-auto"><a href="javascript:;" class="btn btn-primary radius-30 mt-2 mt-lg-0"><i class="bx bxs-plus-square"></i>Add New Device</a></div> -->
                </div>
                <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID #</th>
                                <th>Device Name</th> <!-- Added Name Column -->
                                <th>Email</th>
                                <th>MAC ID</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Support Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch all devices ASC limit 2000
                            $sql = "SELECT * FROM devices ORDER BY id ASC LIMIT 2000";
                            $result = mysqli_query($link, $sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $status_badge = '';
                                    if ($row['status'] == 'Pending') {
                                        $status_badge = '<span class="badge bg-warning text-dark">Pending</span>';
                                    } elseif ($row['status'] == 'Approved') {
                                        $status_badge = '<span class="badge bg-success">Approved</span>';
                                    } elseif ($row['status'] == 'Rejected') {
                                        $status_badge = '<span class="badge bg-danger">Rejected</span>';
                                    } elseif ($row['status'] == 'Expired') {
                                        $status_badge = '<span class="badge bg-secondary">Expired</span>';
                                    } else {
                                        $status_badge = '<span class="badge bg-info">' . $row['status'] . '</span>';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h6 class="mb-0 font-14">#<?php echo $row['id']; ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['device_name'] ?? ''); ?></td>
                                        <!-- Display Name -->
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['mac_id']; ?></td>
                                        <td><?php echo $row['start_date']; ?></td>
                                        <td><?php echo $row['end_date']; ?></td>
                                        <td><?php echo $row['support_phone']; ?></td>
                                        <td><?php echo $status_badge; ?></td>
                                        <td>
                                            <div class="d-flex order-actions">
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm"
                                                        onchange="this.form.submit()">
                                                        <option value="Approved" <?php if ($row['status'] == 'Approved')
                                                            echo 'selected'; ?>>Approve</option>
                                                        <option value="Rejected" <?php if ($row['status'] == 'Rejected')
                                                            echo 'selected'; ?>>Reject</option>
                                                        <option value="Pending" <?php if ($row['status'] == 'Pending')
                                                            echo 'selected'; ?>>Pending</option>
                                                        <option value="Expired" <?php if ($row['status'] == 'Expired')
                                                            echo 'selected'; ?>>Expire</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>

                                                <!-- Delete Button -->
                                                <form method="POST" action="" class="d-inline ms-2"
                                                    onsubmit="return confirm('Are you sure you want to delete this device?');">
                                                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" name="delete_device" class="btn btn-sm btn-danger"><i
                                                            class='bx bxs-trash'></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='9'>No devices found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end page wrapper -->
        <?php include("footer.php"); ?>
