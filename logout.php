<?php
// logout.php
session_start();
require_once 'auth.php';

// If cookie exists, remove token from DB
if (isset($_COOKIE['remember_me_token'])) {
    $token = $_COOKIE['remember_me_token'];
    auth_remove_remember_token($token);
    setcookie('remember_me_token', '', time() - 3600, '/', '', false, true);
}

// destroy session cookies & session
$user_id = $_SESSION['user_id'] ?? null;
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: p3.php");
exit;
