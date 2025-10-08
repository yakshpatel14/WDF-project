<?php
// event_create.php - create a new event
session_start();
require_once 'auth.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: p3.php?err=2");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$err = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');

    if ($title === '' || $event_date === '') {
        $err = 'Title and Date are required.';
    } else {
        try {
            $pdo = getPDO();
            $sql = "INSERT INTO events (title, description, event_date, created_by) VALUES (:title, :description, :event_date, :created_by)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'event_date' => $event_date,
                'created_by' => $user_id
            ]);
            $ok = 'Event created successfully.';
        } catch (Exception $e) {
            error_log("Event create error: " . $e->getMessage());
            $err = 'Failed to create event. Try again later.';
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Create Event</title></head>
<body>
  <h2>Create Event</h2>
  <?php if ($err) echo "<div style='color:red;'>".htmlspecialchars($err)."</div>"; ?>
  <?php if ($ok) echo "<div style='color:green;'>".htmlspecialchars($ok)."</div>"; ?>
  <form method="post" action="event_create.php">
    <label>Title: <input type="text" name="title" required></label><br>
    <label>Date: <input type="date" name="event_date" required></label><br>
    <label>Description:<br><textarea name="description" rows="6" cols="40"></textarea></label><br>
    <button type="submit">Create</button>
  </form>
  <p><a href="p4.php">Back to Dashboard</a></p>
</body>
</html>
