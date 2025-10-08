<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Student Management</title>
</head>
<body>
<h2>Student List</h2>
<form method="get">
    <input type="text" name="search" placeholder="Search by name">
    <input type="submit" value="Search">
</form>
<a href="add.php">Add Student</a>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Actions</th></tr>
<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM students WHERE name LIKE '%$search%'";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['email']}</td>
        <td>{$row['course']}</td>
        <td>
          <a href='edit.php?id={$row['id']}'>Edit</a> |
          <a href='delete.php?id={$row['id']}'>Delete</a>
        </td>
    </tr>";
}
?>
</table>
</body>
</html>
