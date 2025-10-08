<?php 
include 'db2.php';
session_start();

if($_POST){
    $u = $_POST['username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE name=?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();

    if($r && $r['role'] == 'admin'){
        $_SESSION['role'] = 'admin';
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Access Denied. Invalid admin username.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #0984e3, #6c5ce7);
        height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        background: white;
        padding: 40px;
        width: 350px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        text-align: center;
        animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        margin-bottom: 20px;
        color: #2d3436;
    }

    input[type="text"], input[name="username"] {
        width: 90%;
        padding: 10px;
        margin: 10px 0 20px;
        border: 1px solid #b2bec3;
        border-radius: 8px;
        font-size: 16px;
        outline: none;
        transition: border 0.3s;
    }

    input[type="text"]:focus, input[name="username"]:focus {
        border-color: #0984e3;
        box-shadow: 0 0 5px rgba(9,132,227,0.5);
    }

    input[type="submit"] {
        width: 100%;
        background: #0984e3;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }

    input[type="submit"]:hover {
        background: #74b9ff;
    }

    .error {
        background: #ffe6e6;
        color: #d63031;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    footer {
        text-align: center;
        margin-top: 15px;
        font-size: 12px;
        color: #636e72;
    }

</style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Enter your admin username" required>
        <input type="submit" value="Login">
    </form>

    <footer>&copy; <?= date('Y') ?> Admin Panel</footer>
</div>

</body>
</html>
