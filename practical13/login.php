<?php
require 'init.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) die('Invalid CSRF token.');

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$answer = trim($_POST['sum_answer'] ?? '');
$errors = [];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
if (!preg_match('/^\d+$/', $answer) || (int)$answer !== ($_SESSION['sum_captcha'] ?? -1)) $errors[] = 'Captcha incorrect.';

if ($errors) {
    foreach ($errors as $e) echo "<p>$e</p>";
    echo '<p><a href="login_form.php">Back to login</a></p>';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :e");
$stmt->execute([':e' => $email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: home.php');
    exit;
} else {
    echo '<p>Login failed. <a href="login_form.php">Try again</a></p>';
}
