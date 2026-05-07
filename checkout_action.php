<?php
require_once 'connection.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

function sendResponse($success, $message, $redirect = '') {
    global $isAjax;
    if ($isAjax) {
        echo json_encode(['success' => $success, 'message' => $message, 'redirect' => $redirect]);
        exit();
    } else {
        if (!$success) {
            $_SESSION['checkout_error'] = $message;
            header('Location: checkout.php');
        } else {
            echo "<script>alert('{$message}'); window.location.href='{$redirect}';</script>";
        }
        exit();
    }
}

if (!isset($_SESSION['user_id'])) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Please login to continue.', 'redirect' => 'login.php']);
        exit();
    }
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int) $_SESSION['user_id'];

    if (empty($_SESSION['cart'])) {
        sendResponse(false, 'Your cart is empty.', 'cart.php');
    }

    $first_name = mysqli_real_escape_string($link, trim($_POST['billing_first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($link, trim($_POST['billing_last_name'] ?? ''));
    $email = mysqli_real_escape_string($link, trim($_POST['billing_email'] ?? ''));
    $phone = mysqli_real_escape_string($link, trim($_POST['billing_phone'] ?? ''));
    $country = mysqli_real_escape_string($link, trim($_POST['billing_country'] ?? ''));
    $address = mysqli_real_escape_string($link, trim($_POST['billing_address_1'] ?? ''));
    $city = mysqli_real_escape_string($link, trim($_POST['billing_city'] ?? ''));
    $postcode = mysqli_real_escape_string($link, trim($_POST['billing_postcode'] ?? ''));

    $raw_payment_method = trim($_POST['payment_method'] ?? 'cod');
    $payment_method = ($raw_payment_method === 'qr_payment') ? 'qr_payment' : 'cod';
    $utr_number = trim($_POST['utr_number'] ?? '');
    $payment_status = ($payment_method === 'qr_payment') ? 'unpaid' : 'pending';

    if ($payment_method === 'qr_payment') {
        if ($utr_number === '' || !preg_match('/^\d{12}$/', $utr_number)) {
            sendResponse(false, 'Please enter a valid 12-digit UTR number.');
        }

        $utrCheckStmt = mysqli_prepare($link, "SELECT id FROM orders WHERE utr_number = ? LIMIT 1");
        if ($utrCheckStmt) {
            mysqli_stmt_bind_param($utrCheckStmt, "s", $utr_number);
            mysqli_stmt_execute($utrCheckStmt);
            mysqli_stmt_store_result($utrCheckStmt);
            if (mysqli_stmt_num_rows($utrCheckStmt) > 0) {
                mysqli_stmt_close($utrCheckStmt);
                sendResponse(false, 'This UTR number is already used. Please check and enter the correct UTR.');
            }
            mysqli_stmt_close($utrCheckStmt);
        }
    } else {
        $utr_number = '';
    }

    $utr_number = mysqli_real_escape_string($link, $utr_number);

    $total_price = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    $sql = "INSERT INTO orders (user_id, total_amount, first_name, last_name, email, phone, country, address, city, postcode, status, payment_method, utr_number, payment_status, created_at) 
            VALUES ($user_id, $total_price, '$first_name', '$last_name', '$email', '$phone', '$country', '$address', '$city', '$postcode', 'Pending', '$payment_method', '$utr_number', '$payment_status', NOW())";

    if (!mysqli_query($link, $sql)) {
        sendResponse(false, 'Something went wrong placing your order. Please try again.');
    }

    $order_id = mysqli_insert_id($link);

    foreach ($_SESSION['cart'] as $item) {
        $pid = (int) $item['id'];
        $pname = mysqli_real_escape_string($link, $item['name']);
        $pprice = (float) $item['price'];
        $pqty = (int) $item['quantity'];
        mysqli_query($link, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES ($order_id, $pid, '$pname', $pprice, $pqty)");
    }

    $_SESSION['cart'] = [];
    sendResponse(true, 'Order placed successfully!', 'my_orders.php');

} else {
    header('Location: checkout.php');
    exit();
}
?>
