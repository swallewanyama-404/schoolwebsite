<?php
// ============================================================
//  includes/functions.php — Helper Functions
//  Include once at the top of every PHP page.
// ============================================================

// ----------------------------------------------------------
// SANITIZE user input — always call this before using
// $_POST or $_GET values in your code or database.
// ----------------------------------------------------------
function clean($data) {
    $data = trim($data);           // remove leading/trailing spaces
    $data = stripslashes($data);   // remove backslashes
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // convert special chars
}

// ----------------------------------------------------------
// GET a setting from the school_info table.
// Uses static cache so we only query the DB once per key.
// Usage: $name = getSetting($pdo, 'school_name');
// ----------------------------------------------------------
function getSetting($pdo, $key) {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    $stmt = $pdo->prepare('SELECT setting_value FROM school_info WHERE setting_key = ?');
    $stmt->execute([$key]);
    return $cache[$key] = ($stmt->fetchColumn() ?: '');
}

// ----------------------------------------------------------
// EXCERPT — truncate text to N characters, strip HTML tags.
// Usage: echo excerpt($article['body'], 150);
// ----------------------------------------------------------
function excerpt($text, $len = 150) {
    $text = strip_tags($text);
    return strlen($text) > $len ? substr($text, 0, $len) . '...' : $text;
}

// ----------------------------------------------------------
// FORMAT DATE — convert DB date to readable format.
// Usage: echo formatDate('2026-06-25');  → Wednesday 25 June 2026
// ----------------------------------------------------------
function formatDate($date) {
    return date('l d F Y', strtotime($date));
}

// ----------------------------------------------------------
// MAKE SLUG — convert text to URL-friendly slug.
// Usage: $slug = makeSlug('S4 Wins Football!');  → s4-wins-football
// ----------------------------------------------------------
function makeSlug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    return preg_replace('/[\s-]+/', '-', $text);
}

// ----------------------------------------------------------
// REQUIRE ADMIN — redirect to login if not logged in.
// Call this at the top of every admin page.
// ----------------------------------------------------------
function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /school-website/admin/login.php');
        exit;
    }
}

// ----------------------------------------------------------
// AUDIT LOG — record an admin action.
// Call after every INSERT / UPDATE / DELETE in admin pages.
// ----------------------------------------------------------
function auditLog($pdo, $adminId, $action, $table, $recordId, $desc) {
    $pdo->prepare(
        'INSERT INTO audit_log
            (admin_id, action, table_name, record_id, description, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)'
    )->execute([
        $adminId, $action, $table, $recordId, $desc, $_SERVER['REMOTE_ADDR']
    ]);
}
