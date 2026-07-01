<?php

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'contact.php');
    exit;
}

$name    = clean($_POST['name']);
$email   = clean($_POST['email']);
$phone   = clean($_POST['phone'] ?? '');
$subject = clean($_POST['subject']);
$message = clean($_POST['message']);

if (saveContact($pdo, $name, $email, $phone, $subject, $message)) {
    $_SESSION['success'] = 'Message sent successfully!';
} else {
    $_SESSION['success'] = 'Something went wrong. Please try again.';
}

header('Location: ' . BASE_URL . 'contact.php');
exit;
?>
