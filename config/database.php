<?php
// DATABASE CONFIGURATION — PDO Connection

if (!defined('BASE_URL')) {
 
    $projectDirName = basename(dirname(__DIR__));
    if ($_SERVER['HTTP_HOST'] === 'localhost' ||$_SERVER['SERVER_ADDR'] === '127.0.0.1'){
        define('BASE_URL', '/' . $projectDirName . '/');
    } else {
    define('BASE_URL', '/');
    }
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'school_website_db');
define('DB_USER', 'root');
define('DB_PASS', ''); 

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('DB Connection Failed: ' . $e->getMessage());
}
?>
