<?php
session_start();
require_once '../connection.php';

$uuid = isset($_COOKIE['uuid']) ? (string) $_COOKIE['uuid'] : '';
if ($uuid === '') {
    header('Location: login.php');
    exit();
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
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($order_id <= 0) {
    die("Invalid order ID.");
}

// Fetch the order. Admin can access any receipt.
$sql = "SELECT * FROM orders WHERE id=$order_id";
$res = mysqli_query($link, $sql);

if (mysqli_num_rows($res) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($res);

// Ensure payment status is paid (Optional strict check: uncomment if receipt should STRICTLY ONLY work when paid, even via direct URL)
/*
if (strtolower($order['payment_status'] ?? '') !== 'paid') {
    die("<h2>Receipt is not available yet.</h2><p>Your payment status is currently marked as: <strong>" . ucfirst(htmlspecialchars($order['payment_status'] ?? 'pending')) . "</strong></p><p>Receipts are only generated for fully paid orders.</p>");
}
*/

$qr_data = "--- USER INFO ---\nName: {$order['first_name']} {$order['last_name']}\nPhone: {$order['phone']}\nEmail: {$order['email']}\n\n--- SHIPPING INFO ---\nAddress: {$order['address']}\nCity: {$order['city']}\nPostcode: {$order['postcode']}\nCountry: {$order['country']}\n\nOrder ID: {$order['id']}";
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);

// Fetch items
$items_sql = "SELECT * FROM order_items WHERE order_id=$order_id";
$items_res = mysqli_query($link, $items_sql);
$items = [];
while ($item = mysqli_fetch_assoc($items_res)) {
    $items[] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - Order #<?php echo $order['id']; ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header .logo {
            font-size: 32px;
            font-weight: 800;
            color: #111;
            letter-spacing: -1px;
            margin: 0;
        }

        .header .company-info {
            text-align: right;
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .receipt-title {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #555;
            margin-bottom: 40px;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .details-col {
            flex: 1;
        }

        .details-col h4 {
            margin-top: 0;
            color: #111;
            font-size: 16px;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .details-col p {
            margin: 5px 0;
            font-size: 14px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f9f9f9;
            color: #333;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
        }

        td {
            font-size: 14px;
            color: #555;
        }

        .totals {
            width: 50%;
            margin-left: auto;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .totals-row.grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #111;
            border-bottom: none;
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 15px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 13px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .badge-paid {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Print styles */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }

            /* Hide print button if we ever add one inside the body */
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="receipt-container">
        <div class="header">
            <div>
                <h1 class="logo">PROTINUT</h1>
            </div>
            <div class="company-info">
                <strong>Protinut Health Supplements</strong><br>
                <br>
                Phone: +91 081 256 023<br>
                Email: Protinut.in@gmail.com
            </div>
        </div>

        <h2 class="receipt-title">Transaction Receipt</h2>

        <div class="details-row">
            <div class="details-col" style="padding-right: 20px;">
                <h4>Billed To:</h4>
                <p><strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong>
                </p>
                <p><?php echo htmlspecialchars($order['address']); ?></p>
                <p><?php echo htmlspecialchars($order['city']) . ', ' . htmlspecialchars($order['postcode']); ?></p>
                <p><?php echo htmlspecialchars($order['country']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
            </div>
            <div class="details-col">
                <h4>Order Details:</h4>
                <p><strong>Order No:</strong> #<?php echo $order['id']; ?></p>
                <p><strong>Date:</strong> <?php echo date('F d, Y, h:i A', strtotime($order['created_at'])); ?></p>
                <p><strong>Payment Method:</strong>
                    <?php echo ($order['payment_method'] === 'qr_payment') ? 'UPI / QR Payment' : 'Cash on Delivery'; ?>
                </p>
                <?php if ($order['payment_method'] === 'qr_payment' && !empty($order['utr_number'])): ?>
                    <p><strong>UTR / Ref No:</strong> <?php echo htmlspecialchars($order['utr_number']); ?></p>
                <?php endif; ?>
                <p style="margin-top: 15px;">
                    <?php if (strtolower($order['payment_status'] ?? '') === 'paid'): ?>
                        <span class="badge-paid">PAID</span>
                    <?php else: ?>
                        <span class="badge-paid" style="background: #ffc107; color: #000;">PENDING PAYMENT</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="details-col" style="text-align: right; max-width: 130px;">
                <img src="<?php echo $qr_url; ?>" alt="QR Code" style="width: 120px; height: 120px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                <p style="font-size: 11px; color: #777; margin-top: 5px; text-align: center;">Scan for shipping info</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals-row">
            <span>Subtotal</span>
            <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
        </div>
        <div class="totals-row">
            <span>Shipping</span>
            <span>Free</span>
        </div>
        <div class="totals-row grand-total">
            <span>Total Paid</span>
            <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
        </div>

        <div class="footer">
            <p>Thank you for shopping with Protinut! For any questions regarding your order, please contact our support
                team.</p>
            <p>This is a computer-generated document. No signature is required.</p>
        </div>
    </div>

    <script>
        // Automatically trigger the print dialog when the page fully loads
        window.onload = function () {
            window.print();
        };
    </script>

</body>

</html>
