<?php
// ─── Google OAuth Login Initiator ───
// Redirects user to Google's OAuth consent screen

require_once 'connection.php';
require_once 'google_config.php';

// Generate a CSRF state token
$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;

// Build the Google OAuth URL
$params = [
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $state,
    'access_type'   => 'online',
    'prompt'        => 'select_account', // Always show account picker
];

$google_auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

header('Location: ' . $google_auth_url);
exit();
?>
