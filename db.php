<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "wdffullpractical";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);
?>
