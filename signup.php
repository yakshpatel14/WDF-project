<?php
require_once 'db2.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    if ($password !== $repassword) {
        header("Location: p2.html?err=1"); // or display message
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    // Unique check
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s",$email); $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: p2.html?err=2");
        exit;
    }
    $name = $firstname . ' ' . $lastname;
    $role = 'user'; $status = 'active';
    $stmt = $conn->prepare("INSERT INTO users (name,email,password_hash,role,status) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $name, $email, $hash, $role, $status);
    $stmt->execute();
    header("Location: p3.php?msg=signup");
    exit;
}
?>
