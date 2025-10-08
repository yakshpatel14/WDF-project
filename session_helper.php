<?php
// session_helper.php
// Secure session helpers: start, create session on login, enforce timeout, destroy, role checks

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

function secure_session_start() {
    // If session already started, do nothing
    if (session_status() === PHP_SESSION_ACTIVE) return;

    $cookieParams = session_get_cookie_params();
    $secure = false; // set true if using HTTPS
    $httponly = true;
    session_set_cookie_params([
        'lifetime' => 0, // session cookie (or change if you need persistent)
        'path' => $cookieParams['path'] ?? '/',
        'domain' => $cookieParams['domain'] ?? '',
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax'
    ]);

    session_start();

    // Mitigate session fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

/**
 * Initialize session values after a successful login.
 * $user = ['id'=>..., 'name'=>..., 'email'=>..., 'role'=>...]
 */
function session_set_user($user) {
    secure_session_start();
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'] ?? 'user';
    $_SESSION['created_at'] = time();
    $_SESSION['last_activity'] = time();

    // Update last_login in DB
    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);
    } catch (Exception $e) {
        error_log("Failed to update last_login: " . $e->getMessage());
    }
}

/**
 * Ensure user is logged in and session hasn't timed out.
 * If not authenticated, redirects to p3.php. If timed out, destroys session and redirects with ?timeout=1
 * $timeoutSeconds - how many seconds of inactivity allowed (default 1800 = 30 minutes)
 */
function ensure_logged_in($timeoutSeconds = 1800) {
    secure_session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: p3.php?err=2");
        exit;
    }

    $now = time();
    $last = $_SESSION['last_activity'] ?? $_SESSION['created_at'] ?? $now;

    if (($now - $last) > $timeoutSeconds) {
        // session expired
        secure_session_destroy();
        header("Location: p3.php?timeout=1");
        exit;
    }

    // update last activity timestamp
    $_SESSION['last_activity'] = $now;
}

/**
 * Destroy session and optionally remove remember tokens for this user.
 */
function secure_session_destroy() {
    secure_session_start();

    // If user has remember cookie, remove token from DB
    if (isset($_COOKIE['remember_me_token'])) {
        $token = $_COOKIE['remember_me_token'];
        auth_remove_remember_token($token);
        setcookie('remember_me_token', '', time() - 3600, '/', '', false, true);
    }

    // Optionally remove all tokens for the user
    if (isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        // uncomment to clear all tokens for user on logout
        // auth_remove_user_tokens($uid);
    }

    // clear session data
    $_SESSION = [];

    // destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Require a minimum role. If insufficient, either redirect or show 403.
 * Usage: require_role('admin');
 */
function require_role($role) {
    secure_session_start();
    if (!isset($_SESSION['role'])) {
        header("Location: p3.php?err=2");
        exit;
    }
    // Simple check: equality or if user has admin it can pass for lower roles if desired.
    $userRole = $_SESSION['role'];
    if ($userRole !== $role) {
        // Redirect to dashboard with message or show 403
        header("HTTP/1.1 403 Forbidden");
        echo "<h2>403 Forbidden</h2><p>You do not have permission to access this page.</p><p><a href='p4.php'>Back to dashboard</a></p>";
        exit;
    }
}
