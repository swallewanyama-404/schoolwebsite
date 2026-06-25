<?php
// ============================================================
//  process_contact.php — Contact Form Handler
//  This file receives POST data from contact.php
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Only accept POST requests — redirect anything else
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// ── STEP 1: SANITIZE ─────────────────────────────────────────
$name    = clean($_POST['name']    ?? '');
$email   = clean($_POST['email']   ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = clean($_POST['message'] ?? '');

// ── STEP 2: VALIDATE ─────────────────────────────────────────
$errors = [];

if (empty($name))                              $errors[] = 'Your name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
if (empty($subject))                            $errors[] = 'A subject is required.';
if (empty($message))                            $errors[] = 'Please write a message.';
if (strlen($message) < 10)                      $errors[] = 'Message must be at least 10 characters.';

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: contact.php');
    exit;
}

// ── STEP 3: SAVE TO DATABASE ─────────────────────────────────
$stmt = $pdo->prepare(
    'INSERT INTO contact_messages (name, email, subject, message, ip_address)
     VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([
    $name, $email, $subject, $message,
    $_SERVER['REMOTE_ADDR']
]);

// ── STEP 4: REDIRECT WITH SUCCESS ────────────────────────────
$_SESSION['success'] = 'Thank you ' . $name . '! Your message has been received. We will reply within 24 hours.';
header('Location: contact.php');
exit;
