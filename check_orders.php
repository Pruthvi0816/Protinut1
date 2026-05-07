<?php
include('connection.php');
echo "USERS TABLE:\n";
$res = mysqli_query($link, "SELECT id, email FROM users");
while ($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}
echo "\nORDERS TABLE:\n";
$res = mysqli_query($link, "SELECT id, user_id, email FROM orders");
while ($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}
