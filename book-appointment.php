<?php
header('Content-Type: application/json');

require_once '/home/smoothgoalscom/config.php';
require_once __DIR__ . '/mailer.php';

// spam protection (honeypot)
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => false, 'error' => 'Invalid request.']);
    exit;
}

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$date  = trim($_POST['date'] ?? '');
$time  = trim($_POST['time'] ?? '');
$note  = trim($_POST['note'] ?? '');

// validation
if (empty($name) || empty($email) || empty($date) || empty($time)) {
    echo json_encode(['ok' => false, 'error' => 'Name, email, date, and time are required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid email address.']);
    exit;
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid date.']);
    exit;
}
if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid time.']);
    exit;
}

// format for display
$dt = DateTime::createFromFormat('Y-m-d H:i', "$date $time");
if (!$dt) {
    echo json_encode(['ok' => false, 'error' => 'Invalid date/time.']);
    exit;
}
$prettyDate = $dt->format('l, F j, Y');
$prettyTime = $dt->format('g:i A');

// split name
$nameParts = explode(" ", $name, 2);
$fName = $nameParts[0];
$lName = $nameParts[1] ?? '';

// connect DB (uses the least-privilege user from config)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("SG book-appointment.php DB error: " . $conn->connect_error);
    echo json_encode(['ok' => false, 'error' => 'Could not save booking. Please try again later.']);
    exit;
}

// insert (prepared statement)
$stmt = $conn->prepare(
    "INSERT INTO bookings (fName, lName, email, booking_date, booking_time, note, created_at)
     VALUES (?, ?, ?, ?, ?, ?, NOW())"
);
$stmt->bind_param("ssssss", $fName, $lName, $email, $date, $time, $note);

if (!$stmt->execute()) {
    error_log("SG book-appointment.php INSERT error: " . $stmt->error);
    echo json_encode(['ok' => false, 'error' => 'Could not save booking. Please try again later.']);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();
$conn->close();

// notify owner
sg_send_mail(
    MAIL_OWNER,
    '',
    "New Coaching Call Booking — $prettyDate",
    "New coaching call booked!\n\n"
    . "Name:  $name\n"
    . "Email: $email\n"
    . "Date:  $prettyDate\n"
    . "Time:  $prettyTime\n"
    . ($note ? "\nGoal/Note:\n$note\n" : ""),
    $email
);

// confirmation to visitor
sg_send_mail(
    $email,
    $fName,
    "Your coaching call is confirmed — $prettyDate",
    "Hi $fName,\n\n"
    . "Your free coaching call is confirmed!\n\n"
    . "Date: $prettyDate\n"
    . "Time: $prettyTime\n"
    . "Duration: 30 minutes\n\n"
    . "I'll send you the meeting link before our session.\n\n"
    . "If you need to reschedule, just reply to this email.\n\n"
    . "Looking forward to it!\n"
    . "— Michael K., Smooth Goals"
);

echo json_encode(['ok' => true]);
