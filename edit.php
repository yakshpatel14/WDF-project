<?php include 'db.php';
$id=$_GET['id'];
if($_POST){
    $name=$_POST['name']; $email=$_POST['email']; $course=$_POST['course'];
    $conn->query("UPDATE students SET name='$name', email='$email', course='$course' WHERE id=$id");
    echo "Updated successfully";
}
$res=$conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
?>
<form method="post">
Name:<input name="name" value="<?php echo $res['name'];?>"><br>
Email:<input name="email" value="<?php echo $res['email'];?>"><br>
Course:<input name="course" value="<?php echo $res['course'];?>"><br>
<input type="submit" value="Update">
</form>
<a href="index.php">Back</a>
