<?php include 'db.php';
$id=$_GET['id'];
$res=$conn->query("SELECT status FROM users WHERE id=$id")->fetch_assoc();
$new=$res['status']=='active'?'inactive':'active';
$conn->query("UPDATE users SET status='$new' WHERE id=$id");
header("Location: dashboard.php");
?>
