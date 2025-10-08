<?php
require 'init.php';

$a = random_int(1, 9);
$b = random_int(1, 9);
$_SESSION['sum_captcha'] = $a + $b;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h2>Login</h2>
<form method="post" action="login.php">
  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
  <label>Email
    <input name="email" type="email" required>
  </label><br>
  <label>Password
    <input name="password" type="password" required>
  </label><br>
  <label>What is <?= $a ?> + <?= $b ?> ?
    <input name="sum_answer" required pattern="^\d+$">
  </label><br>
  <button type="submit">Login</button>
</form>
<p>No account? <a href="register_form.php">Register here</a></p>
</div>
</body>
</html>
