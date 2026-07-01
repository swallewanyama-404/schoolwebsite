<?php
    require_once __DIR__ . '/../config/database.php';
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="St. Mary's School - Nurturing excellence in academics, character, and leadership since our founding.">
    <title>St. Mary's School</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%230B1F3A'/%3E%3Ctext x='50' y='63' font-size='40' fill='%23C8952A' text-anchor='middle' font-family='serif'%3ESM%3C/text%3E%3C/svg%3E">
    <script>
        window.BASE_URL = <?php echo BASE_URL; ?>;
    </script>
</head>
<body>

<a href="#main-content" class="skip-link">Skip to main content</a>

<!-- TOP CONTACT BAR -->
<div class="topbar">
    <div class="container topbar-inner">
        <ul class="topbar-contact">
            <li>📞 +256 700 123 456</li>
            <li>✉️ info@stmarys.ac.ug</li>
            <li>📍 Ntinda, Kampala, Uganda</li>
        </ul>
        <ul class="topbar-social">
            <li><a href="#" aria-label="Facebook">f</a></li>
            <li><a href="#" aria-label="Twitter">𝕏</a></li>
            <li><a href="#" aria-label="Instagram">◎</a></li>
            <li><a href="#" aria-label="YouTube">▶</a></li>
        </ul>
    </div>
</div>

<!-- MAIN NAVIGATION -->
<nav class="main-nav">
    <div class="nav-inner">
        <a href="<?php echo BASE_URL; ?>index.php" class="nav-brand">
            <div class="nav-badge">
                <span class="nav-badge-text">SM<br>SCHOOL</span>
            </div>
            <div class="nav-brand-name">
                <span class="school-name">St. Mary's School</span>
                <span class="school-sub">Excellence in Education</span>
            </div>
        </a>
        <button class="burger" id="burger" aria-label="Toggle navigation menu">&#9776;</button>
        <ul class="nav-links" id="nav-links">
            <li><a href="<?php echo BASE_URL; ?>index.php" class="<?php echo $current_page=='index.php'?'active':''; ?>">Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>about.php" class="<?php echo $current_page=='about.php'?'active':''; ?>">About</a></li>
            <li><a href="<?php echo BASE_URL; ?>academics.php" class="<?php echo $current_page=='academics.php'?'active':''; ?>">Academics</a></li>
            <li><a href="<?php echo BASE_URL; ?>admissions.php" class="<?php echo $current_page=='admissions.php'?'active':''; ?>">Admissions</a></li>
            <li><a href="<?php echo BASE_URL; ?>news.php" class="<?php echo $current_page=='news.php'?'active':''; ?>">News</a></li>
            <li><a href="<?php echo BASE_URL; ?>staff.php" class="<?php echo $current_page=='staff.php'?'active':''; ?>">Staff</a></li>
            <li><a href="<?php echo BASE_URL; ?>contact.php" class="<?php echo $current_page=='contact.php'?'active':''; ?>">Contact</a></li>
            <li class="nav-admin"><a href="<?php echo BASE_URL; ?>admin/login.php">Admin Panel</a></li>
        </ul>
    </div>
</nav>
