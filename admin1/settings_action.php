<?php
session_start();
include("../connection.php");

// Verify admin login
if (isset($_COOKIE['uuid'])) {
    $u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);
} else {
    echo "<script>window.location.href='login.php'</script>";
    exit;
}

$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = mysqli_query($link, $sql4);

if ($result4->num_rows == 0) {
    echo "<script>window.location.href='login.php'</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($link, $_POST['title'] ?? '');
    $subtitle = mysqli_real_escape_string($link, $_POST['subtitle'] ?? '');
    $button_text = mysqli_real_escape_string($link, $_POST['button_text'] ?? '');
    $button_link = mysqli_real_escape_string($link, $_POST['button_link'] ?? '');
    $contact_text = mysqli_real_escape_string($link, $_POST['contact_text'] ?? '');
    $contact_number = mysqli_real_escape_string($link, $_POST['contact_number'] ?? '');

    // Fetch current images to retain if not updated
    $current_bg = '';
    $current_product = '';
    $res = mysqli_query($link, "SELECT bg_image, product_image FROM hero_settings LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) {
        $current_bg = $row['bg_image'];
        $current_product = $row['product_image'];
    }

    $bg_image_path = $current_bg;
    $product_image_path = $current_product;

    // Handle BG Image Upload
    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] == 0) {
        $bg_ext = pathinfo($_FILES['bg_image']['name'], PATHINFO_EXTENSION);
        $bg_filename = 'hero_bg_' . time() . '.' . $bg_ext;
        $target_bg = '../assets/img/bg/' . $bg_filename;
        if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $target_bg)) {
            $bg_image_path = 'assets/img/bg/' . $bg_filename;
        }
    }

    // Handle Product Image Upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $prod_ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $prod_filename = 'hero_product_' . time() . '.' . $prod_ext;
        $target_prod = '../assets/img/shop/' . $prod_filename;
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_prod)) {
            $product_image_path = 'assets/img/shop/' . $prod_filename;
        }
    }

    // Update Query
    $update_sql = "UPDATE hero_settings SET 
                    title = '$title',
                    subtitle = '$subtitle',
                    button_text = '$button_text',
                    button_link = '$button_link',
                    contact_text = '$contact_text',
                    contact_number = '$contact_number',
                    bg_image = '$bg_image_path',
                    product_image = '$product_image_path'
                   WHERE id = 1"; // Assuming single record

    // Ensure at least one record exists
    if (mysqli_num_rows($res) == 0) {
        $update_sql = "INSERT INTO hero_settings (title, subtitle, button_text, button_link, contact_text, contact_number, bg_image, product_image) 
                       VALUES ('$title', '$subtitle', '$button_text', '$button_link', '$contact_text', '$contact_number', '$bg_image_path', '$product_image_path')";
    }

    if (mysqli_query($link, $update_sql)) {
        echo "<script>alert('Hero configuration updated successfully.'); window.location.href='settings.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating configuration: " . mysqli_error($link) . "'); window.location.href='settings.php';</script>";
    }
} else {
    header("Location: settings.php");
    exit;
}
?>