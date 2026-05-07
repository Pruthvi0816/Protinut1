<?php
/**
 * Standalone Delete Action Handler
 * Deletes immediately and redirects back — no confirmation page needed.
 */
session_start();
include("../connection.php");

// Auth check
if (isset($_COOKIE['uuid'])) {
    $u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);
} else {
    $u_uname = '';
}
$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = mysqli_query($link, $sql4);
if (!$result4 || $result4->num_rows == 0) {
    header("Location: login.php");
    exit;
}

// Get parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0 || empty($type)) {
    header("Location: index.php?error=Invalid request");
    exit;
}

// Define valid types
$types = [
    'product' => ['table' => 'products', 'redirect' => 'products.php', 'label' => 'Product'],
    'blog' => ['table' => 'blogs', 'redirect' => 'blogs.php', 'label' => 'Blog Post'],
    'user' => ['table' => 'users', 'redirect' => 'users.php', 'label' => 'User'],
    'order' => ['table' => 'orders', 'redirect' => 'orders.php', 'label' => 'Order'],
    'contact' => ['table' => 'contacts', 'redirect' => 'contacts.php', 'label' => 'Message'],
];

if (!isset($types[$type])) {
    header("Location: index.php?error=Invalid type");
    exit;
}

$info = $types[$type];

// Special handling for orders (also delete order_items)
if ($type === 'order') {
    mysqli_query($link, "DELETE FROM order_items WHERE order_id=$id");
}

$del = mysqli_query($link, "DELETE FROM {$info['table']} WHERE id=$id");

if ($del) {
    header("Location: {$info['redirect']}?success={$info['label']} Deleted");
} else {
    $err = urlencode(mysqli_error($link));
    header("Location: {$info['redirect']}?error=Delete failed: $err");
}
exit;