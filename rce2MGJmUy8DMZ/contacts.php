<?php include("header.php"); ?>

<?php
// Mark as read
if (isset($_GET['mark_read'])) {
    $id = (int) $_GET['mark_read'];
    mysqli_query($link, "UPDATE contacts SET is_read=1 WHERE id=$id");
    echo "<script>window.location.href='contacts.php?success=Marked as Read'</script>";
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($link, "DELETE FROM contacts WHERE id=$id");
    echo "<script>window.location.href='contacts.php?success=Message Deleted'</script>";
    exit;
}

// Mark all as read
if (isset($_GET['mark_all'])) {
    mysqli_query($link, "UPDATE contacts SET is_read=1");
    echo "<script>window.location.href='contacts.php?success=All Messages Marked as Read'</script>";
}
?>

<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-flex flex-sm-nowrap align-items-center justify-content-between mb-3 gap-2">
            <div class="breadcrumb-title pe-3">Contact Messages</div>
            <div class="ps-3 d-none d-sm-flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active">Messages</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a href="contacts.php?mark_all=1" class="btn btn-outline-success btn-sm"><i
                        class="bx bx-check-double"></i> Mark All Read</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($link, "SELECT * FROM contacts ORDER BY is_read ASC, id DESC");
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr class="<?php echo $row['is_read'] ? '' : 'table-warning'; ?>">
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><a
                                                href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>
                                        <td>
                                            <span title="<?php echo htmlspecialchars($row['message']); ?>">
                                                <?php echo htmlspecialchars(substr($row['message'], 0, 80)); ?>
                                                <?php echo strlen($row['message']) > 80 ? '...' : ''; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <?php if ($row['is_read']): ?>
                                                <span class="badge bg-success">Read</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Unread</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$row['is_read']): ?>
                                                <a href="contacts.php?mark_read=<?php echo $row['id']; ?>"
                                                    class="btn btn-sm btn-outline-success" title="Mark Read"><i
                                                        class="bx bx-check"></i></a>
                                            <?php endif; ?>
                                            <a href="delete_action.php?type=contact&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No messages received yet.</td></tr>";
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
