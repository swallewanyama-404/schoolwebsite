<?php
// ============================================================
//  includes/header.php — Navigation & <head>
//  Include at the top of EVERY public page.
//  The page must set $pageTitle before including this.
// ============================================================

// Load DB + functions if not already loaded
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}
require_once __DIR__ . '/../includes/functions.php';

$schoolName  = getSetting($pdo, 'school_name');
$schoolPhone = getSetting($pdo, 'school_phone');
$schoolEmail = getSetting($pdo, 'school_email');

// Detect current page for active nav highlight
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $schoolName) ?></title>
    <link rel="stylesheet" href="/school-website/assets/css/style.css">
</head>
<body>

<!-- ── TOP BAR ── -->
<div class="top-bar">
    <div class="container top-bar-inner">
        <span>📞 <?= htmlspecialchars($schoolPhone) ?></span>
        <span>✉ <?= htmlspecialchars($schoolEmail) ?></span>
        <a href="/school-website/admin/login.php" class="admin-link">Admin Login</a>
    </div>
</div>

<!-- ── SITE HEADER ── -->
<header class="site-header">
    <div class="container header-inner">
        <a href="/school-website/" class="logo">
            <span class="logo-name"><?= htmlspecialchars($schoolName) ?></span>
            <span class="logo-tagline">Excellence in Education</span>
        </a>

        <!-- Hamburger button — shown only on mobile -->
        <button class="burger" id="burger" aria-label="Open menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <!-- Main navigation -->
        <nav class="main-nav" id="main-nav" role="navigation" aria-label="Main menu">
            <?php
            $navLinks = [
                'index'      => ['Home',       '/school-website/'],
                'about'      => ['About',      '/school-website/about.php'],
                'news'       => ['News',        '/school-website/news.php'],
                'admissions' => ['Admissions',  '/school-website/admissions.php'],
                'staff'      => ['Staff',       '/school-website/staff.php'],
                'gallery'    => ['Gallery',     '/school-website/gallery.php'],
                'contact'    => ['Contact',     '/school-website/contact.php'],
            ];
            foreach ($navLinks as $key => [$label, $href]):
                $active = ($currentPage === $key) ? 'active' : '';
            ?>
            <a href="<?= $href ?>" class="nav-link <?= $active ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
