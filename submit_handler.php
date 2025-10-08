    <?php
// submit_handler.php
// Processes POSTed form, validates/sanitizes inputs, appends to CSV and JSON, shows confirmation.

// --- Config: filenames (relative to project root). Change if you want a different folder. ---
$csvFile = __DIR__ . '/submissions.csv';
$jsonFile = __DIR__ . '/submissions.json';

// --- Only accept POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Invalid request method.";
    exit;
}

// --- Helper functions ---
function clean_text($s) {
    // trim and strip tags, convert multiple spaces to single
    $s = trim($s);
    $s = strip_tags($s);
    $s = preg_replace('/\s+/', ' ', $s);
    return $s;
}

function respond_and_exit($message, $is_error = false) {
    $safe = htmlspecialchars($message);
    $color = $is_error ? 'red' : 'green';
    echo "<!doctype html><html><head><meta charset='utf-8'><title>Submission Result</title></head><body style='font-family:Arial;padding:30px;'>";
    echo "<div style='max-width:700px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 8px 18px rgba(0,0,0,0.06)'>";
    echo "<h2 style='color:$color;'>".($is_error ? 'Error' : 'Success')."</h2>";
    echo "<p>$safe</p>";
    echo "<p><a href='form_submit.php'>Back to form</a> | <a href='view_submissions.php'>View submissions</a></p>";
    echo "</div></body></html>";
    exit;
}

// --- Read and sanitize inputs ---
$name_raw    = $_POST['name'] ?? '';
$email_raw   = $_POST['email'] ?? '';
$phone_raw   = $_POST['phone'] ?? '';
$message_raw = $_POST['message'] ?? '';
$form_source = $_POST['form_source'] ?? '';

// Basic presence check
if (!strlen(trim($name_raw)) || !strlen(trim($email_raw)) || !strlen(trim($phone_raw)) || !strlen(trim($message_raw))) {
    respond_and_exit("All fields are required. Please fill every field.", true);
}

// Sanitize
$name = clean_text($name_raw);
$email = filter_var(trim($email_raw), FILTER_SANITIZE_EMAIL);
$phone = preg_replace('/[^\d\+]/', '', $phone_raw); // digits and + only
$message = clean_text($message_raw);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond_and_exit("Invalid email address.", true);
}

// Validate phone: at least 7 digits and at most 15 digits after removing non-digits
$digits = preg_replace('/\D/', '', $phone);
if (strlen($digits) < 7 || strlen($digits) > 15) {
    respond_and_exit("Phone number should contain 7 to 15 digits.", true);
}

// Prepare record data
$record = [
    'timestamp' => date('Y-m-d H:i:s'),
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'message' => $message,
    'source' => $form_source
];

// --- Save to CSV using fputcsv with exclusive lock ---
$csvHeaders = ['timestamp','name','email','phone','message','source'];
$writeCsvOk = false;

try {
    // Ensure file exists and has headers if new
    $isNewCsv = !file_exists($csvFile) || filesize($csvFile) === 0;

    $fp = fopen($csvFile, 'a');
    if ($fp === false) {
        throw new Exception("Cannot open CSV file for writing.");
    }

    // Acquire exclusive lock
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        throw new Exception("Could not lock CSV file.");
    }

    if ($isNewCsv) {
        // write headers first
        if (fputcsv($fp, $csvHeaders) === false) {
            flock($fp, LOCK_UN);
            fclose($fp);
            throw new Exception("Failed to write CSV headers.");
        }
    }

    $row = [$record['timestamp'], $record['name'], $record['email'], $record['phone'], $record['message'], $record['source']];
    if (fputcsv($fp, $row) === false) {
        flock($fp, LOCK_UN);
        fclose($fp);
        throw new Exception("Failed to write CSV row.");
    }

    // flush and unlock
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $writeCsvOk = true;
} catch (Exception $e) {
    // log server-side for debugging, don't show full details to user
    error_log("CSV write error: " . $e->getMessage());
    respond_and_exit("Failed to save submission (CSV). Contact admin.", true);
}

// --- Also append to JSON (structured) for advanced extension ---
$writeJsonOk = false;
try {
    $existing = [];
    if (file_exists($jsonFile) && is_readable($jsonFile)) {
        $txt = file_get_contents($jsonFile);
        $existing = json_decode($txt, true) ?: [];
    }
    $existing[] = $record;
    // Use file_put_contents with LOCK_EX
    $ok = file_put_contents($jsonFile, json_encode($existing, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX);
    if ($ok === false) {
        throw new Exception("Failed to write JSON file.");
    }
    $writeJsonOk = true;
} catch (Exception $e) {
    error_log("JSON write error: " . $e->getMessage());
    // don't fail overall; CSV succeeded so continue
}

// --- Final confirmation message ---
$msg = "Your submission has been saved successfully.";
if (!$writeCsvOk) $msg = "Submission saved failed (CSV).";
if ($writeCsvOk && !$writeJsonOk) $msg .= " (JSON storage failed — CSV succeeded.)";

respond_and_exit($msg, false);
