<?php

// PROCESS_ENQUIRY.PHP — Updated for new admissions_enquiries table
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admissions.php');
    exit;
}

$parent_name    = clean($_POST['parent_name']);
$parent_phone   = clean($_POST['parent_phone']);
$parent_email   = clean($_POST['parent_email']);
$student_name   = clean($_POST['student_name']);
$entry_level    = clean($_POST['entry_level']);
$current_school = clean($_POST['current_school']);
$ple_aggregate  = !empty($_POST['ple_aggregate']) ? (int)$_POST['ple_aggregate'] : null;
$message        = clean($_POST['message']);

if (saveEnquiry($pdo, $parent_name, $parent_phone, $parent_email, $student_name,
                $entry_level, $current_school, $ple_aggregate, $message)) {
    $_SESSION['success'] = 'Enquiry submitted! We will contact you shortly.';
} else {
    $_SESSION['success'] = 'Something went wrong. Please try again.';
}

header('Location: ' . BASE_URL . 'admissions.php');
exit;
?>
