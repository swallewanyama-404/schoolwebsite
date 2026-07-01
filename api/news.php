<?php
// API/NEWS.PHP — Returns published news as JSON for AJAX
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$news = getPublishedNews($pdo, $limit);

echo json_encode($news);
?>
