<?php
// view_submissions.php
$csvFile = __DIR__ . '/submissions.csv';
$rows = [];

if (file_exists($csvFile) && is_readable($csvFile)) {
    if (($fp = fopen($csvFile, 'r')) !== false) {
        // Read headers
        $headers = fgetcsv($fp);
        while (($line = fgetcsv($fp)) !== false) {
            $rows[] = $line;
        }
        fclose($fp);
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Submissions - CSV Viewer</title>
  <style>
    body { font-family: Arial, sans-serif; padding:20px; background:#f8fafc; }
    table { border-collapse:collapse; width:100%; max-width:1100px; margin:0 auto; background:#fff; box-shadow:0 8px 20px rgba(0,0,0,0.05); }
    th, td { padding:10px 12px; border:1px solid #eee; text-align:left; vertical-align:top; }
    th { background:#f1f5f9; }
    .note { max-width:1100px; margin:12px auto; color:#666; }
  </style>
</head>
<body>
  <h2 style="text-align:center;">Submissions (CSV)</h2>
  <p class="note" style="text-align:center;">This view reads the CSV file and displays rows. Use this for demo / evaluation.</p>

  <?php if (empty($rows)): ?>
    <p style="text-align:center;">No submissions found. <a href="form_submit.php">Submit one</a></p>
  <?php else: ?>
    <table>
      <thead>
        <tr><?php foreach ($headers as $h): ?><th><?php echo htmlspecialchars($h); ?></th><?php endforeach; ?></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <?php foreach ($r as $c): ?>
              <td><?php echo nl2br(htmlspecialchars($c)); ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p style="text-align:center;margin-top:12px;"><a href="form_submit.php">Back to form</a></p>
  <?php endif; ?>
</body>
</html>
