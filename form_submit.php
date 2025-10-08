<?php
// form_submit.php
// Simple form that POSTs to submit_handler.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Submit Form - WDF Practical</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, Helvetica, sans-serif; background:#f5f7fb; padding:30px; }
    .card { background:#fff; padding:20px; border-radius:8px; max-width:600px; margin:0 auto; box-shadow:0 8px 20px rgba(0,0,0,0.06); }
    label { display:block; margin-top:12px; font-weight:600; }
    input[type="text"], input[type="email"], textarea { width:100%; padding:10px; border:1px solid #d0d7de; border-radius:6px; }
    button { margin-top:14px; padding:10px 16px; border:none; border-radius:6px; background:#007db8; color:#fff; cursor:pointer; }
    .note { color:#666; font-size:13px; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Event / Contact Form</h2>
    <p class="note">All fields are required. Data will be stored in CSV (and JSON optionally).</p>

    <form action="submit_handler.php" method="post" novalidate>
      <label for="name">Full name</label>
      <input id="name" name="name" type="text" maxlength="120" required>

      <label for="email">Email address</label>
      <input id="email" name="email" type="email" maxlength="200" required>

      <label for="phone">Phone (digits only)</label>
      <input id="phone" name="phone" type="text" maxlength="20" required placeholder="e.g. 9876543210">

      <label for="message">Message / Event details</label>
      <textarea id="message" name="message" rows="5" maxlength="2000" required></textarea>

      <input type="hidden" name="form_source" value="wdf_practical">
      <button type="submit">Submit</button>
    </form>

    <p style="margin-top:12px;"><a href="view_submissions.php">View submissions (CSV)</a></p>
  </div>
</body>
</html>
