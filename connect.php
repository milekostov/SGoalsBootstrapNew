<?php
require_once '/home/smoothgoalscom/config.php';
require_once __DIR__ . '/mailer.php';

// spam protection (honeypot + time delay)
if (!empty($_POST['website'])) exit;
if (time() - ($_POST['form_time'] ?? time()) < 3) exit;

// get data
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$goal    = trim($_POST['goal'] ?? '');
$message = trim($_POST['message'] ?? '');

// validation
if (empty($name) || empty($email) || empty($message)) exit;
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) exit;

// split name
$nameParts = explode(" ", $name, 2);
$fName = $nameParts[0];
$lName = $nameParts[1] ?? '';

// connect DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("SG connect.php DB error: " . $conn->connect_error);
    echo "<script>alert('Something went wrong. Please try again later.');window.location.href='contact.html';</script>";
    exit;
}

// insert (prepared statement)
$stmt = $conn->prepare("INSERT INTO userReg (fName, lName, email, phone, interest, questions) VALUES (?, ?, ?, ?, ?, ?)");

$phone    = "";
$interest = $goal;
$questions = $message;

$stmt->bind_param("ssssss", $fName, $lName, $email, $phone, $interest, $questions);

if ($stmt->execute()) {
    // notify owner
    sg_send_mail(
        MAIL_OWNER,
        '',
        "New SmoothGoals Contact",
        "Name: $name\nEmail: $email\nGoal: $goal\n\nMessage:\n$message",
        $email
    );

    // auto-reply to visitor
    sg_send_mail(
        $email,
        $fName,
        "Got your message — Smooth Goals",
        "Hi $fName,\n\n"
        . "Thanks for reaching out. I'll reply within 24 hours.\n\n"
        . "In the meantime, want to talk through your goals live?\n"
        . "Book a free 30-minute call: https://smoothgoals.com/schedule.html\n\n"
        . "— Michael K., Smooth Goals"
    );

    echo "<script>alert('Message sent successfully.');window.location.href='index.html';</script>";
} else {
    error_log("SG connect.php INSERT error: " . $stmt->error);
    echo "<script>alert('Something went wrong. Please try again later.');window.location.href='contact.html';</script>";
}

$stmt->close();
$conn->close();
