<?php
session_start();
include("../connection.php");

if (isset($_POST["login"])) {
    $username = trim((string) ($_POST['uname'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

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

    if ($username === '' || $password === '') {
        header("Location:login.php?error=Invalid Credentials");
        exit;
    }

    $stmt = $link->prepare("SELECT id, username, password FROM admin WHERE username=? LIMIT 1");
    if (!$stmt) {
        header("Location:login.php?error=Login failed");
        exit;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($adminId, $adminUsername, $adminPassword);
    $found = $stmt->fetch();
    $stmt->close();

    $valid = false;
    $isHashed = false;
    if ($found) {
        $info = password_get_info((string) $adminPassword);
        $isHashed = ($info['algo'] ?? 0) !== 0;
        if ($isHashed) {
            $valid = password_verify($password, (string) $adminPassword);
        } else {
            $valid = hash_equals((string) $adminPassword, $password);
        }
    }

    if (!$valid) {
        usleep(400000);
        header("Location:login.php?error=Invalid Credentials");
        exit;
    }

    if (!$isHashed) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        if ($newHash) {
            $up = $link->prepare("UPDATE admin SET password=? WHERE id=?");
            if ($up) {
                $up->bind_param("si", $newHash, $adminId);
                $up->execute();
                $up->close();
            }
        }
    }

    session_regenerate_id(true);

    try {
        $uuid = bin2hex(random_bytes(32));
    } catch (Throwable $e) {
        $bytes = false;
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(32);
        }
        $uuid = bin2hex($bytes !== false ? $bytes : uniqid('', true));
    }

    $my_otp = (string) random_int(111111, 999999);
    $status = 'verified';

    $revoke = $link->prepare("UPDATE ods SET status='revoked' WHERE user_id=? AND status='verified'");
    if ($revoke) {
        $revoke->bind_param("s", $adminUsername);
        $revoke->execute();
        $revoke->close();
    }

    $ins = $link->prepare("INSERT INTO ods (ots, user_id, uuid, status) VALUES (?, ?, ?, ?)");
    if ($ins) {
        $ins->bind_param("ssss", $my_otp, $adminUsername, $uuid, $status);
        $ins->execute();
        $ins->close();
    }

    $uuidExpiry = $remember ? (time() + (30 * 24 * 60 * 60)) : 0;
    $setCookie("uuid", $uuid, $uuidExpiry);

    if ($remember) {
        $setCookie("uname", $adminUsername, time() + (30 * 24 * 60 * 60));
    } else {
        $setCookie("uname", "", time() - 3600);
    }

    if (isset($_COOKIE["password"])) {
        $setCookie("password", "", time() - 3600);
    }

    header("Location:index.php?success=" . urlencode("User Login Successfully"));
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png" />
    <!--plugins-->
    <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/plugins/notifications/css/lobibox.min.css" />
    <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="assets/css/pace.min.css" rel="stylesheet" />
    <script src="assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
    <link href="assets/css/icons.css" rel="stylesheet">
    <title>Protinut Admin Login</title>
</head>

<body style="background: linear-gradient(to right, #00b09b, #96c93d);">
    <!--wrapper-->
    <div class="wrapper valign-wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                    <div class="col mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4 text-center">
                                    <h4>Protinut Admin</h4>
                                </div>

                                <div class="border p-4 rounded">
                                    <div class="text-center">
                                        <h3 class="">Login</h3>
                                    </div>

                                    <div class="form-body">
                                        <form class="row g-3" method="post" action="">
                                            <div class="col-12">
                                                <label for="inputEmailAddress" class="form-label">Username</label>
                                                <input type="text" name="uname" class="form-control"
                                                    id="inputEmailAddress" value="<?php if (isset($_COOKIE['uname'])) {
                                                        echo htmlspecialchars((string) $_COOKIE['uname'], ENT_QUOTES, 'UTF-8');
                                                    } ?>" placeholder="Username" required>
                                            </div>
                                            <div class="col-12">
                                                <label for="inputChoosePassword" class="form-label">Password</label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" class="form-control border-end-0"
                                                        id="inputChoosePassword" name="password"
                                                        placeholder="Enter Password" required>
                                                    <a href="javascript:;" class="input-group-text bg-transparent"><i
                                                            class='bx bx-hide'></i></a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="flexSwitchCheckChecked" name="remember" <?php if (isset($_COOKIE['uname'])) { ?> checked <?php } ?>>
                                                    <label class="form-check-label"
                                                        for="flexSwitchCheckChecked">Remember Me</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <input type="submit" class="btn btn-primary" name="login"
                                                        Value="Login">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end wrapper-->

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        $(document).ready(function () {
            $("#show_hide_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
        });
    </script>
</body>

</html>
