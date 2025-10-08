<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: p4.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign In - Dell Clone</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('https://t3.ftcdn.net/jpg/02/92/90/56/360_F_292905667_yFUJNJPngYeRNlrRL4hApHWxuYyRY4kN.jpg') no-repeat center center fixed;
      background-size: cover; min-height:100vh; display:flex; flex-direction:column;
    }
    header { width:100%; background-color:#0c2d48; padding:15px 30px; display:flex; justify-content:flex-end; align-items:center; }
    #btn { background:#fff; color:#0c2d48; padding:10px 20px; border-radius:5px; border:none; cursor:pointer; font-weight:bold; }
    .form-wrapper { flex:1; display:flex; justify-content:flex-end; align-items:center; padding:60px 60px 60px 0; }
    .form-container { background:rgba(255,255,255,0.98); border:1px solid #333; padding:35px 40px; border-radius:15px; box-shadow:0 12px 30px rgba(0,0,0,0.2); max-width:400px; width:100%; margin-right:60px; }
    h2{ text-align:center; color:#007db8; margin-bottom:25px; }
    label{ display:block; font-weight:bold; margin-bottom:6px; color:#333; }
    input[type="email"], input[type="password"] { width:100%; padding:12px; margin-bottom:12px; border:1px solid #ccc; border-radius:8px; font-size:15px; }
    .remember-row { display:flex; align-items:center; gap:8px; margin-bottom:12px; }
    button[type="submit"]{ width:100%; padding:12px; border:none; border-radius:25px; background:#007db8; color:#fff; font-weight:bold; cursor:pointer; }
    button[type="submit"]:hover{ background:#005f8a; }
    .error { color: #b00020; margin-bottom:10px; text-align:center; }
  </style>
</head>
<body>
  <header>
    <a href="p1.html"><button id="btn">HOME</button></a>
  </header>
  <div class="form-wrapper">
    <div class="form-container">
      <h2>Sign In</h2>
      <?php
      if (isset($_GET['err']) && $_GET['err'] == '1') {
          echo '<div class="error">Invalid email or password.</div>';
      } elseif (isset($_GET['err']) && $_GET['err'] == '2') {
          echo '<div class="error">Please login to continue.</div>';
      }
      ?>
      <form action="login1.php" method="post" onsubmit="return checkPassword()">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php if(isset($_COOKIE['user_email'])) echo htmlspecialchars($_COOKIE['user_email']); ?>" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <div class="remember-row">
          <input type="checkbox" id="remember" name="remember" value="1" <?php if(isset($_COOKIE['remember_me_token'])) echo 'checked'; ?>>
          <label for="remember" style="margin:0;">Remember me</label>
        </div>
        <button type="submit">Sign In</button>
      </form>
    </div>
  </div>
  <script>
    function checkPassword() {
      const password = document.getElementById("password").value;
      if (password.trim() === "") {
        alert("Please enter your password!");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
