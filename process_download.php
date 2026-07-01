<?php
// PROCESS_DOWNLOAD.PHP — Serves downloadable files securely

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Must have a valid ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . BASE_URL . 'admissions.php');
    exit;
}

$id   = (int)$_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM admissions_documents WHERE id = ? AND is_active = 1');
$stmt->execute([$id]);
$doc  = $stmt->fetch();

// Document not found or inactive
if (!$doc) {
    header('Location: ' . BASE_URL . 'admissions.php');

}

// Build the full server path
$file_path = __DIR__ . '/' . $doc['filename'];

// File must physically exist on server
if (!file_exists($file_path)) {
    // Log missing file but don't expose path to user
    error_log("Download file not found: $file_path");
    $_SESSION['download_error'] = 'Sorry, this file is temporarily unavailable. Please contact the school.';
    header('Location: ' . BASE_URL . 'admissions.php');
    exit;
}


// Increment download counter
incrementDownloads($pdo, $id);

// Log the download action
$current_admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
logAction($pdo, $current_admin_id,
'file_downloaded', 'admissions_documents', $id,
          'Downloaded: ' . $doc['title'] . ' | IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

// Serve the file as a download or inline stream
$filename    = basename($doc['filename']);
$file_size   = filesize($file_path);
$mime_type   = 'application/pdf';

header('Content-Type: ' . $mime_type);

// If stream parameter is set to 1, show it inline inside the webpage layout container
if (isset($_GET['stream']) && $_GET['stream'] == 1) {
    header('Content-Disposition: inline; filename="' . $filename . '"');
} else {
    header('Content-Disposition: attachment; filename="' . $filename . '"');
}

header('Content-Length: ' . $file_size);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Output file contents
if (ob_get_level()) {
    ob_end_clean();
}
readfile($file_path);
exit;
?>
