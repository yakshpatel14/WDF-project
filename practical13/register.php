<?php
require 'init.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die('Invalid CSRF token.');
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['password_confirm'] ?? '';
$answer = trim($_POST['sum_answer'] ?? '');

$errors = [];

// Validation
if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) $errors[] = 'Invalid username.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
if (strlen($password) < 8) $errors[] = 'Password too short.';
if ($password !== $confirm) $errors[] = 'Passwords do not match.';
if (!preg_match('/^\d+$/', $answer) || (int)$answer !== ($_SESSION['sum_captcha'] ?? -1)) $errors[] = 'Captcha incorrect.';

if ($errors) {
    foreach ($errors as $e) echo "<p>$e</p>";
    echo '<p><a href="register_form.php">Go back</a></p>';
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:u, :e, :p)");
    $stmt->execute([':u' => $username, ':e' => $email, ':p' => $hash]);
    echo '<p>Registration successful! <a href="login_form.php">Login now</a>.</p>';
} catch (PDOException $ex) {
    if ($ex->errorInfo[1] == 1062) {
        echo '<p>Email or username already exists.</p>';
    } else {
        echo '<p>Error: registration failed.</p>';
    }
}
