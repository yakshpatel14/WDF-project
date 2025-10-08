<?php
session_start();
include 'db2.php';
if($_SESSION['role'] != 'admin') die("Access restricted");

$id = $_GET['id'];
$user = $conn->query("SELECT * FROM users WHERE id='$id'")->fetch_assoc();

if($_POST){
    $username = $_POST['username'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $conn->query("UPDATE users SET username='$username', role='$role', status='$status' WHERE id='$id'");
    header("Location: dashboard.php");
    exit;
}
?>
<form method="post">
    <h2>Edit User</h2>
    Username: <input name="username" value="<?= $user['username'] ?>" required><br><br>
    Role: <select name="role">
        <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
    </select><br><br>
    Status: <select name="status">
        <option value="active" <?= $user['status']=='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= $user['status']=='inactive'?'selected':'' ?>>Inactive</option>
    </select><br><br>
    <input type="submit" value="Update User">
</form>
