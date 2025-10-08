<?php
// register.php - both the form and handler (simple)
require_once 'auth.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $password !== $confirm) {
        $err = 'Please fill all fields correctly and ensure passwords match.';
    } else {
        try {
            $user_id = auth_register_user($name, $email, $password);
            // auto-login after register
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            header("Location: p4.php");
            exit;
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
  <h2>Register</h2>
  <?php if (!empty($err)) echo "<div style='color:red;'>".htmlspecialchars($err)."</div>"; ?>
  <form method="post" action="register.php">
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label>Confirm: <input type="password" name="confirm" required></label><br>
    <button type="submit">Register</button>
  </form>
</body>
</html>
