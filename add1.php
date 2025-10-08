<?php include 'db.php';
if($_POST){
 $title=$_POST['title']; $date=$_POST['date']; $desc=$_POST['desc']; $status=$_POST['status'];
 $sql="INSERT INTO events(title,date,description,status) VALUES('$title','$date','$desc','$status')";
 if($conn->query($sql)) echo "Event Added!";
}
?>
<form method="post">
Title:<input name="title"><br>
Date:<input type="date" name="date"><br>
Description:<textarea name="desc"></textarea><br>
Status:<select name="status"><option>open</option><option>closed</option></select><br>
<input type="submit" value="Add Event">
</form>
<a href="index.php">Back</a>
