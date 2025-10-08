<?php
session_start();
require_once 'db2.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $pass = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && password_verify($pass, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];

        if (isset($_POST['remember']) && $_POST['remember'] == '1') {
            $token = bin2hex(random_bytes(16));
            $stmt2 = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt2->bind_param("si", $token, $user['id']);
            $stmt2->execute();
            setcookie("remember_me_token", $token, time() + (86400 * 30), "/", "", true, true);
            setcookie("user_email", $user['email'], time() + (86400 * 30), "/");
        } else {
            setcookie("remember_me_token", "", time() - 3600, "/", "", true, true);
            setcookie("user_email", "", time() - 3600, "/");
        }

        header("Location: p4.php");
        exit;
    } else {
        header("Location: p3.php?err=1");
        exit;
    }
} else {
    header("Location: p3.php");
    exit;
}
?>
