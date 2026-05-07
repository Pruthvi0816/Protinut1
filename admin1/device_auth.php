<?php include("header.php"); ?>

<!-- Handle Form Submission -->
<?php
if (isset($_POST['activate_device'])) {
    $device_id = $_POST['device_id'];
    $device_name = $_POST['device_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $support_phone = $_POST['support_phone'];

    // Update Device Directly
    $stmt = $link->prepare("UPDATE devices SET device_name=?, start_date=?, end_date=?, support_phone=?, status='Approved' WHERE id=?");
    $stmt->bind_param("ssssi", $device_name, $start_date, $end_date, $support_phone, $device_id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='device_auth.php?success=Device Activated Successfully'</script>";
    } else {
        echo "<script>window.location.href='device_auth.php?error=Failed to Activate Device'</script>";
    }
}
?>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Device Authentication</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Device Auth</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="row">
            <div class="col-xl-9 mx-auto">
                <h6 class="mb-0 text-uppercase">Device Activation</h6>
                <hr />
                <div class="card border-top border-0 border-4 border-info">
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Select Device to Activate</label>
                                <select class="form-select mb-3" name="device_id" required>
                                    <option selected value="">Select Device</option>
                                    <?php
                                    // Show all devices to allow editing existing ones too, or just pending?
                                    $sql = "SELECT * FROM devices ORDER BY id DESC";
                                    $result = mysqli_query($link, $sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $label = ($row['device_name'] ? $row['device_name'] . " - " : "") . $row['mac_id'] . " (" . $row['status'] . ")";
                                        echo "<option value='" . $row['id'] . "'>" . $label . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Device Name</label>
                                <input type="text" class="form-control" name="device_name"
                                    placeholder="Enter Device Name (e.g. Sales Officer 1)" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Support Phone (for Banner)</label>
                                <input type="text" class="form-control" name="support_phone" placeholder="9876543210"
                                    required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subscription Start Date</label>
                                    <input type="date" class="form-control" name="start_date" required
                                        value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subscription End Date</label>
                                    <input type="date" class="form-control" name="end_date" required
                                        value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="activate_device" class="btn btn-primary">Activate / Update
                                    Device</button>
                            </div>
                        </form>
                    </div>
                </div>

                <h6 class="mb-0 text-uppercase mt-4">Active Subscriptions</h6>
                <hr />
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Device Name</th>
                                        <th>Mac ID</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                    </tr>
                                    <?php
                                    $sql_users = "SELECT * FROM devices WHERE status='Approved' OR status='Expired' ORDER BY id DESC";
                                    $res_users = mysqli_query($link, $sql_users);
                                    if ($res_users->num_rows > 0) {
                                        while ($row = $res_users->fetch_assoc()) {
                                            $today = date('Y-m-d');
                                            $badge_class = 'bg-success';
                                            if ($row['end_date'] < $today) {
                                                $badge_class = 'bg-danger';
                                            } elseif ($row['status'] == 'Expired') {
                                                $badge_class = 'bg-secondary';
                                            }
                                            ?>
                                            <tr>
                                                <td>#<?php echo $row['id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['device_name'] ?? ''); ?></td>
                                                <td><?php echo $row['mac_id']; ?></td>
                                                <td><?php echo $row['start_date']; ?></td>
                                                <td><?php echo $row['end_date']; ?></td>
                                                <td><span
                                                        class="badge <?php echo $badge_class; ?>"><?php echo $row['status']; ?></span>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No active subscriptions found.</td></tr>";
                                    }
                                    ?>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end page wrapper -->

        <?php include("footer.php"); ?>