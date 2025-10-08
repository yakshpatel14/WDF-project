<?php include 'db.php';
if($_POST){
    $name=$_POST['name']; $email=$_POST['email']; $course=$_POST['course'];
    $sql="INSERT INTO students(name,email,course) VALUES('$name','$email','$course')";
    if($conn->query($sql)) echo "Inserted successfully";
}
?>
<form method="post">
Name:<input name="name"><br>
Email:<input name="email"><br>
Course:<input name="course"><br>
<input type="submit" value="Add">
</form>
<a href="index.php">Back</a>
