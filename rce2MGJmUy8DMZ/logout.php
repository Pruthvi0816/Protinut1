<?php
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

$setCookie = function (string $name, string $value, int $expires) use ($secure): void {
    if (PHP_VERSION_ID >= 70300) {
        setcookie($name, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        return;
    }
    setcookie($name, $value, $expires, '/', '', $secure, true);
};

$uuid = isset($_COOKIE['uuid']) ? (string) $_COOKIE['uuid'] : '';
if ($uuid !== '') {
    include("../connection.php");
    $stmt = $link->prepare("UPDATE ods SET status='revoked' WHERE uuid=? AND status='verified'");
    if ($stmt) {
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $stmt->close();
    }
}

$setCookie("uuid", "", time() - 3600);
$setCookie("uname", "", time() - 3600);
if (isset($_COOKIE["password"])) {
    $setCookie("password", "", time() - 3600);
}

header("Location:login.php");
exit;
?>
