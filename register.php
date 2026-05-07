<?php
require_once 'connection.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    // Additional Address fields
    $country = trim($_POST['country'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Name, Email, and Password are required fields.";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            $emailDomain = strtolower(ltrim(strrchr($email, '@'), '@'));
            if ($emailDomain !== 'gmail.com' && $emailDomain !== 'googlemail.com') {
                $error = "Please use a valid Gmail address (example@gmail.com).";
            }
        }

        if (!$error) {
            $passwordOk =
                strlen($password) >= 8 &&
                preg_match('/[a-z]/', $password) &&
                preg_match('/[A-Z]/', $password) &&
                preg_match('/\d/', $password) &&
                preg_match('/[^A-Za-z0-9]/', $password);

            if (!$passwordOk) {
                $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
            }
        }

        if (!$error) {
            $checkStmt = mysqli_prepare($link, "SELECT id FROM users WHERE email = ? LIMIT 1");
            if (!$checkStmt) {
                $error = "Something went wrong. Please try again.";
            } else {
                mysqli_stmt_bind_param($checkStmt, "s", $email);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);

                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    $error = "Email is already registered. Please login instead.";
                }
                mysqli_stmt_close($checkStmt);
            }
        }

        if (!$error) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = mysqli_prepare(
                $link,
                "INSERT INTO users (name, email, password, phone, country, address, city, postcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$insertStmt) {
                $error = "Something went wrong. Please try again.";
            } else {
                mysqli_stmt_bind_param($insertStmt, "ssssssss", $name, $email, $hashed_password, $phone, $country, $address, $city, $postcode);
                if (mysqli_stmt_execute($insertStmt)) {
                    $success = "Registration successful! You can now <a href='login.php'>login here</a>.";
                } else {
                    $error = "Something went wrong creating your account. Please try again.";
                }
                mysqli_stmt_close($insertStmt);
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<style>
/* ─── Google Sign-In Button Styles ─── */
.google-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 14px 20px;
    background: #ffffff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    color: #3c4043;
    font-family: 'Segoe UI', Roboto, Arial, sans-serif;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.google-btn:hover {
    background: #f7f8f8;
    border-color: #d0d0d0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    transform: translateY(-1px);
    text-decoration: none;
    color: #3c4043;
}
.google-btn:active {
    transform: translateY(0);
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.google-btn svg {
    flex-shrink: 0;
}

/* ─── OR Divider ─── */
.login-divider {
    display: flex;
    align-items: center;
    margin: 28px 0;
    gap: 16px;
}
.login-divider::before,
.login-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, transparent, #d0d0d0, transparent);
}
.login-divider span {
    font-size: 13px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}
</style>

<!-- main area start  -->
<main>
    <section class="checkout pt-120 pb-385">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="checkout__wrap">
                        <div class="woocommerce-billing-fields">
                            <h3 class="mb-30 text-center" style="font-size:32px; font-weight:bold; color:#060606;">
                                Create an Account</h3>
                            <p class="text-center" style="color:#666; margin-bottom:40px;">Fill out your details to join
                                Protinut and enhance your shopping experience.</p>

                            <?php if ($error): ?>
                                <div class="alert alert-danger" role="alert"
                                    style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; margin-bottom: 20px; padding: 15px; border-radius: 4px;">
                                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success" role="alert"
                                    style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; margin-bottom: 20px; padding: 15px; border-radius: 4px;">
                                    <?php echo $success; ?>
                                </div>
                            <?php else: ?>

                                <!-- Google Sign-Up Button -->
                                <a href="google_login.php" class="google-btn" id="google-signup-btn">
                                    <svg width="20" height="20" viewBox="0 0 48 48">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                    </svg>
                                    Sign up with Google
                                </a>

                                <!-- OR Divider -->
                                <div class="login-divider">
                                    <span>or register with email</span>
                                </div>

                                <form action="register.php" method="POST">
                                    <div class="row">
                                        <!-- Basic Info -->
                                        <div class="col-md-6 mb-3">
                                            <label style="font-weight:600; color:#333;">Full Name <abbr class="required"
                                                    title="required" style="color:red;">*</abbr></label>
                                            <input type="text" class="form-control" name="name" required
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label style="font-weight:600; color:#333;">Email Address <abbr class="required"
                                                    title="required" style="color:red;">*</abbr></label>
                                            <input type="email" class="form-control" name="email" required
                                                pattern="^[A-Za-z0-9._%+-]+@(?:gmail\.com|googlemail\.com)$"
                                                title="Use a Gmail address (example@gmail.com)"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label style="font-weight:600; color:#333;">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label style="font-weight:600; color:#333;">Password <abbr class="required"
                                                    title="required" style="color:red;">*</abbr></label>
                                            <input type="password" class="form-control" name="password" required
                                                minlength="8"
                                                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
                                                title="At least 8 chars with uppercase, lowercase, number, and special character"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                        </div>

                                        <!-- Address Info -->
                                        <div class="col-12 mt-4 mb-3">
                                            <h4
                                                style="font-size:20px; border-bottom:2px solid #f7941d; padding-bottom:10px;">
                                                Shipping Address</h4>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label style="font-weight:600; color:#333;">Street Address</label>
                                            <input type="text" class="form-control" name="address"
                                                placeholder="House number and street name"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label style="font-weight:600; color:#333;">Country</label>
                                            <select name="country" class="form-control"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                                <option value="">Select a country...</option>
                                                <option value="US">United States</option>
                                                <option value="UK">United Kingdom</option>
                                                <option value="CA">Canada</option>
                                                <option value="IN">India</option>
                                                <option value="AU">Australia</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label style="font-weight:600; color:#333;">Town / City</label>
                                            <input type="text" class="form-control" name="city"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label style="font-weight:600; color:#333;">Postcode / ZIP</label>
                                            <input type="text" class="form-control" name="postcode"
                                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                                        </div>

                                        <div class="col-12 mt-4">
                                            <button type="submit" class="xb-btn thm-btn btn-filled"
                                            style="width: 100%; height: 60px; border: none; cursor: pointer; text-transform: uppercase; font-family: var(--font-heading); letter-spacing: 1px;">Register
                                            Account</button>
                                        </div>

                                        <div class="col-12 mt-4 text-center">
                                            <p style="font-size: 15px; color:#666;">Already have an account? <a
                                                    href="login.php"
                                                    style="color: var(--color-primary); font-weight:600;">Login here</a></p>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- main area end  -->

<?php include 'footer.php'; ?>
