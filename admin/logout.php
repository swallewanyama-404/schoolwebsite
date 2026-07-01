<?php
// LOGOUT — Destroys AMDIN session and redirects to login
require_once __DIR__ . '/../config/database.php';
if (session_status() ===PHP_SESSION_NONE){
    session_start();
}
$_SESSION = array();
session_destroy();
header('Location: ' . BASE_URL . 'admin/login.php');
exit();
?>
