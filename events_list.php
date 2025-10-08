<?php
// events_list.php
session_start();
require_once 'auth.php';
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: p3.php?err=2");
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT e.id, e.title, e.event_date, u.name AS creator FROM events e JOIN users u ON u.id = e.created_by ORDER BY e.event_date DESC, e.created_at DESC");
    $events = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("List events error: " . $e->getMessage());
    $events = [];
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>All Events</title></head>
<body>
  <h2>All Events</h2>
  <p><a href="event_create.php">Create New Event</a> | <a href="p4.php">Dashboard</a></p>
  <?php if (empty($events)): ?>
    <div>No events found.</div>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <tr><th>Title</th><th>Date</th><th>Creator</th></tr>
      <?php foreach ($events as $ev): ?>
        <tr>
          <td><?php echo htmlspecialchars($ev['title']); ?></td>
          <td><?php echo htmlspecialchars($ev['event_date']); ?></td>
          <td><?php echo htmlspecialchars($ev['creator']); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>
