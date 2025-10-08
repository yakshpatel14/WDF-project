<?php
session_start();
include 'db2.php';
if($_SESSION['role'] != 'admin') die("Access restricted");

if($_POST){
    $username = $_POST['username'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $conn->query("INSERT INTO users (username, role, status) VALUES ('$username', '$role', '$status')");
    header("Location: dashboard.php");
    exit;
}
?>
<form method="post">
    <h2>Add User</h2>
    Username: <input name="username" required><br><br>
    Role: <select name="role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select><br><br>
    Status: <select name="status">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select><br><br>
    <input type="submit" value="Add User">
</form>
