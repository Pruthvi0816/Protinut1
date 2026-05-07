<?php
require_once 'connection.php';

$error = '';

// Check for Google OAuth error passed via URL
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($link, trim($_POST['email']));
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $sql = "SELECT id, name, email, password, google_id FROM users WHERE email='$email' LIMIT 1";
        $result = mysqli_query($link, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Check if this is a Google-only account (no password set)
            if (!empty($row['google_id']) && empty($row['password'])) {
                $error = "This account uses Google Sign-In. Please click 'Sign in with Google' below.";
            } elseif (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];

                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
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
                <div class="col-lg-6">
                    <div class="checkout__wrap">
                        <div class="woocommerce-billing-fields">
                            <h3 class="mb-30 text-center">Login to Your Account</h3>

                            <?php if ($error): ?>
                                <div class="alert alert-danger" role="alert"
                                    style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; margin-bottom: 20px; padding: 15px; border-radius: 8px; font-size: 14px;">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Google Sign-In Button -->
                            <a href="google_login.php" class="google-btn" id="google-signin-btn">
                                <svg width="20" height="20" viewBox="0 0 48 48">
                                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                </svg>
                                Sign in with Google
                            </a>

                            <!-- OR Divider -->
                            <div class="login-divider">
                                <span>or</span>
                            </div>

                            <form action="login.php" method="POST">
                                <div class="woocommerce-billing-fields__field-wrapper">
                                    <p class="form-row form-row-wide validate-required validate-email">
                                        <label>Email address&nbsp;<abbr class="required"
                                                title="required">*</abbr></label>
                                        <span class="woocommerce-input-wrapper">
                                            <input type="email" class="input-text" name="email" required>
                                        </span>
                                    </p>
                                    <p class="form-row form-row-wide validate-required">
                                        <label>Password&nbsp;<abbr class="required" title="required">*</abbr></label>
                                        <span class="woocommerce-input-wrapper">
                                            <input type="password" class="input-text" name="password" required>
                                        </span>
                                    </p>
                                    <p class="form-row">
                                        <button type="submit" class="thm-btn btn-filled"
                                            style="width: 100%; border: none; cursor: pointer;">Login</button>
                                    </p>
                                    <p class="form-row mt-3 text-center">
                                        Don't have an account? <a href="register.php"
                                            style="color: var(--color-primary);">Register here</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- main area end  -->

<?php include 'footer.php'; ?>