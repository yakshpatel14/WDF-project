<?php
require 'init.php';

// Generate sum captcha
$a = random_int(1, 9);
$b = random_int(1, 9);
$_SESSION['sum_captcha'] = $a + $b;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Register</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h2>Register</h2>
<form method="post" action="register.php" novalidate>
  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
  <label>Username
    <input name="username" required minlength="3" maxlength="50" pattern="^[A-Za-z0-9_.-]{3,50}$">
  </label><br>
  <label>Email
    <input name="email" type="email" required>
  </label><br>
  <label>Password
    <input name="password" type="password" required minlength="8">
  </label><br>
  <label>Confirm Password
    <input name="password_confirm" type="password" required minlength="8">
  </label><br>
  <label>What is <?= $a ?> + <?= $b ?> ?
    <input name="sum_answer" required pattern="^\d+$">
  </label><br>
  <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login_form.php">Login here</a></p>
</div>
</body>
</html>
