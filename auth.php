<?php
// auth.php - DB-backed authentication helpers
require_once __DIR__ . '/db.php';

/**
 * Find user by email. Returns user row or false.
 */
function auth_get_user_by_email($email) {
    $pdo = getPDO();
    $sql = "SELECT id, name, email, password_hash, created_at FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    return $user ?: false;
}

/**
 * Register a new user. Returns user id on success, or throws exception on error.
 */
function auth_register_user($name, $email, $password) {
    $pdo = getPDO();

    // validate uniqueness
    if (auth_get_user_by_email($email)) {
        throw new Exception("Email already registered.");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => $password_hash
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Validate credentials; return user (id,name,email) or false.
 */
function auth_validate_credentials($email, $password) {
    $user = auth_get_user_by_email($email);
    if (!$user) return false;
    if (password_verify($password, $user['password_hash'])) {
        return ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']];
    }
    return false;
}

/**
 * Create and store remember token in DB, returns token.
 */
function auth_store_remember_token($user_id) {
    $pdo = getPDO();
    // token length 40 hex chars
    $token = bin2hex(random_bytes(20));
    $sql = "INSERT INTO remember_tokens (token, user_id) VALUES (:token, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token, 'user_id' => $user_id]);
    return $token;
}

/**
 * Check remember token and return user row or false.
 */
function auth_check_remember_token($token) {
    $pdo = getPDO();
    $sql = "SELECT rt.token, rt.user_id, u.name, u.email
            FROM remember_tokens rt
            JOIN users u ON u.id = rt.user_id
            WHERE rt.token = :token LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch();
    if (!$row) return false;
    return ['id' => $row['user_id'], 'name' => $row['name'], 'email' => $row['email']];
}

/**
 * Remove remember token.
 */
function auth_remove_remember_token($token) {
    $pdo = getPDO();
    $sql = "DELETE FROM remember_tokens WHERE token = :token";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
}

/**
 * Remove all remember tokens for a user (optional logout from all devices)
 */
function auth_remove_user_tokens($user_id) {
    $pdo = getPDO();
    $sql = "DELETE FROM remember_tokens WHERE user_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['uid' => $user_id]);
}
