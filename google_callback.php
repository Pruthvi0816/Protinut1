<?php
// ─── Google OAuth Callback Handler ───
// Receives auth code from Google, exchanges for token, gets user info, logs in or registers

require_once 'connection.php';
require_once 'google_config.php';

// --- Error handling ---
if (isset($_GET['error'])) {
    // User cancelled or an error occurred
    header('Location: login.php?error=' . urlencode('Google login was cancelled.'));
    exit();
}

// --- Validate state (CSRF protection) ---
if (!isset($_GET['state']) || !isset($_SESSION['google_oauth_state']) || $_GET['state'] !== $_SESSION['google_oauth_state']) {
    header('Location: login.php?error=' . urlencode('Invalid request. Please try again.'));
    exit();
}
unset($_SESSION['google_oauth_state']);

// --- Validate authorization code ---
if (!isset($_GET['code'])) {
    header('Location: login.php?error=' . urlencode('No authorization code received.'));
    exit();
}

$auth_code = $_GET['code'];

// --- Step 1: Exchange authorization code for access token ---
$token_url = 'https://oauth2.googleapis.com/token';
$token_data = [
    'code'          => $auth_code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
// SSL verification (set to true in production with proper CA bundle)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$token_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $curl_error = curl_error($ch);
    curl_close($ch);
    header('Location: login.php?error=' . urlencode('Connection error: ' . $curl_error));
    exit();
}
curl_close($ch);

$token_result = json_decode($token_response, true);

if ($http_code !== 200 || !isset($token_result['access_token'])) {
    $err_msg = $token_result['error_description'] ?? $token_result['error'] ?? 'Failed to get access token.';
    header('Location: login.php?error=' . urlencode($err_msg));
    exit();
}

$access_token = $token_result['access_token'];

// --- Step 2: Get user info from Google ---
$userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userinfo_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$userinfo_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    curl_close($ch);
    header('Location: login.php?error=' . urlencode('Failed to fetch user info.'));
    exit();
}
curl_close($ch);

$user_info = json_decode($userinfo_response, true);

if ($http_code !== 200 || !isset($user_info['email'])) {
    header('Location: login.php?error=' . urlencode('Could not retrieve your Google account information.'));
    exit();
}

// --- Extract user data ---
$google_id    = $user_info['id'] ?? '';
$google_email = $user_info['email'] ?? '';
$google_name  = $user_info['name'] ?? '';

if (empty($google_email)) {
    header('Location: login.php?error=' . urlencode('No email found in your Google account.'));
    exit();
}

// --- Step 3: Find or create user in database ---

// First, check if a user with this google_id exists
$stmt = mysqli_prepare($link, "SELECT id, name, email FROM users WHERE google_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $google_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing_user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($existing_user) {
    // User found by google_id — log them in
    $_SESSION['user_id']   = $existing_user['id'];
    $_SESSION['user_name'] = $existing_user['name'];
    $_SESSION['user_email'] = $existing_user['email'];
    header('Location: index.php');
    exit();
}

// Check if a user with this email already exists (registered with password)
$stmt = mysqli_prepare($link, "SELECT id, name, email FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $google_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing_user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($existing_user) {
    // Link Google ID to existing account
    $update_stmt = mysqli_prepare($link, "UPDATE users SET google_id = ? WHERE id = ?");
    mysqli_stmt_bind_param($update_stmt, "si", $google_id, $existing_user['id']);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);

    // Log them in
    $_SESSION['user_id']   = $existing_user['id'];
    $_SESSION['user_name'] = $existing_user['name'];
    $_SESSION['user_email'] = $existing_user['email'];
    header('Location: index.php');
    exit();
}

// New user — create account (no password needed)
$empty_password = ''; // Google users don't need a password
$stmt = mysqli_prepare($link, "INSERT INTO users (name, email, password, google_id) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $google_name, $google_email, $empty_password, $google_id);

if (mysqli_stmt_execute($stmt)) {
    $new_user_id = mysqli_insert_id($link);
    mysqli_stmt_close($stmt);

    $_SESSION['user_id']   = $new_user_id;
    $_SESSION['user_name'] = $google_name;
    $_SESSION['user_email'] = $google_email;
    header('Location: index.php');
    exit();
} else {
    mysqli_stmt_close($stmt);
    header('Location: login.php?error=' . urlencode('Failed to create account. Please try again.'));
    exit();
}
?>
