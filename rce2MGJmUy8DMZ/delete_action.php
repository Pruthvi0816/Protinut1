<?php
/**
 * Standalone Delete Action Handler
 * Deletes immediately and redirects back — no confirmation page needed.
 */
session_start();
include("../connection.php");

// Auth check
$uuid = isset($_COOKIE['uuid']) ? (string) $_COOKIE['uuid'] : '';
if ($uuid === '') {
    header("Location: login.php");
    exit;
}

$ok = false;
$stmt = $link->prepare("SELECT id FROM ods WHERE uuid=? AND status='verified' ORDER BY id DESC LIMIT 1");
if ($stmt) {
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $stmt->bind_result($id_check);
    $ok = (bool) $stmt->fetch();
    $stmt->close();
}
if (!$ok) {
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
