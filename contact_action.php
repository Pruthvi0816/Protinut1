<?php
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($link, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($link, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($link, trim($_POST['phone'] ?? ''));
    $message = mysqli_real_escape_string($link, trim($_POST['message'] ?? ''));

    if ($name && $email && $message) {
        $sql = "INSERT INTO contacts (name, email, phone, message) VALUES ('$name', '$email', '$phone', '$message')";
        if (mysqli_query($link, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Thank you! We will get back to you soon.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
