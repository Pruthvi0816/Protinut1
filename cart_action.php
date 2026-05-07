<?php
require_once 'connection.php';

// If user is not logged in, redirect to login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Initialize cart in session if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($action === 'add') {
        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
        $product_price = isset($_POST['product_price']) ? (float) $_POST['product_price'] : 0.0;
        $product_image = isset($_POST['product_image']) ? $_POST['product_image'] : '';
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        if ($product_id > 0) {
            // Check if product exists in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }

            // If not found, add new item
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $product_id,
                    'name' => $product_name,
                    'price' => $product_price,
                    'image' => $product_image,
                    'quantity' => $quantity
                ];
            }
        }

        // Return back to referring page or shop.php
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'shop.php';
        header("Location: " . $referer);
        exit();

    } elseif ($action === 'remove') {
        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        if ($product_id > 0) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id'] == $product_id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            // Re-index array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }

        header("Location: cart.php");
        exit();

    } elseif ($action === 'update') {
        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        if ($product_id > 0 && $quantity > 0) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product_id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
        } elseif ($quantity <= 0) {
            // Removing if quantity is less than or equals 0
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id'] == $product_id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }

        header("Location: cart.php");
        exit();
    }
}

// Redirect fallback
header("Location: shop.php");
exit();
?>