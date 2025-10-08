<?php
session_start();
require_once 'auth.php';
require_once 'db2.php';

if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['remember_me_token'])) {
        $user = auth_check_remember_token($_COOKIE['remember_me_token']);
        if ($user !== false) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
        }
    }
}
if (!isset($_SESSION['user_id'])) {
    header("Location: p3.php?err=2");
    exit;
}
$user_name = htmlspecialchars($_SESSION['user_name']);
$user_id = (int)$_SESSION['user_id'];

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT e.id, e.title, e.description, e.event_date, u.name AS creator
                           FROM events e
                           JOIN users u ON u.id = e.created_by
                           ORDER BY e.event_date DESC, e.created_at DESC
                           LIMIT 5");
    $stmt->execute();
    $events = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to fetch events: " . $e->getMessage());
    $events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dell Technologies - Dashboard</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; background:#f5f7fb; color:#333; }
    header { background:#3f474a; color:#fff; padding:14px 20px; display:flex; justify-content:space-between; align-items:center; }
    .container { max-width:1000px; margin:30px auto; padding:0 20px; }
    .card { background:#fff; border-radius:8px; padding:18px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:16px; }
    .events-list { list-style:none; padding:0; margin:0; }
    .events-list li { padding:12px 0; border-bottom:1px solid #eee; }
    .events-list li:last-child { border-bottom:none; }
    .event-title { font-weight:bold; }
    .small { color:#666; font-size:13px; }
    .actions { margin-top:12px; }
    .btn { padding:8px 12px; border-radius:6px; text-decoration:none; background:#007db8; color:#fff; }
    .btn:hover { background:#005f8a; }
  </style>
</head>
<body>
  <header>
    <div><strong>DELL TECHNOLOGIES</strong></div>
    <div>Welcome, <?php echo $user_name; ?> | <a href="logout.php" style="color:#fff;">Logout</a></div>
  </header>
  <div class="container">
    <div class="card">
      <h2>Dashboard</h2>
      <p class="small">Latest 5 events from the database</p>
      <?php if (count($events) === 0): ?>
        <div>No events found.</div>
      <?php else: ?>
        <ul class="events-list">
          <?php foreach ($events as $ev): ?>
            <li>
              <div class="event-title"><?php echo htmlspecialchars($ev['title']); ?></div>
              <div class="small">Date: <?php echo htmlspecialchars($ev['event_date']); ?> — Created by: <?php echo htmlspecialchars($ev['creator']); ?></div>
              <div><?php echo nl2br(htmlspecialchars($ev['description'])); ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <div class="actions">
        <a class="btn" href="event_create.php">Create Event</a>
        <a class="btn" href="events_list.php" style="background:#4caf50;">View All Events</a>
      </div>
    </div>
  </div>
</body>
</html>
