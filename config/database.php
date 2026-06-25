<?php
// ============================================================
//  config/database.php — Database Connection
//  School Website · Intellectitech Ntinda Hub
//  Include this file on every page that needs the database.
// ============================================================

define('DB_HOST',    'localhost');
define('DB_NAME',    'school_website_db');
define('DB_USER',    'root');        // XAMPP default
define('DB_PASS',    '');            // XAMPP default: blank
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = 'mysql:host=' . DB_HOST
         . ';dbname=' . DB_NAME
         . ';charset=' . DB_CHARSET;

    $pdo = new PDO($dsn, DB_USER, DB_PASS);

    // Show SQL errors clearly during development
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    // Return rows as associative arrays: $row['name'] not $row[0]
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Use real prepared statements (security)
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);

} catch (PDOException $e) {
    // Log to server error log — never show raw error to visitors
    error_log('DB Connection failed: ' . $e->getMessage());
    die('<p style="font-family:Arial;color:red;padding:2rem">
        Database unavailable. Please try again later.</p>');
}
