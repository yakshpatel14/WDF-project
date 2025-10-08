<?php
require 'init.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Home</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h2>Welcome, <?= e($_SESSION['username']) ?>!</h2>
<p>You are logged in securely.</p>
<a href="logout.php">Logout</a>
</div>
</body>
</html>
