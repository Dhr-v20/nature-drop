<?php
// logout.php — Session destroy and redirect
require_once 'includes/db.php';

// Destroy session completely
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// Redirect to login with a small message
header('Location: login.php?logged_out=1');
exit;