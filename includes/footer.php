<?php
// ============================================================
//  includes/footer.php — Footer & closing HTML
//  Include at the BOTTOM of every public page.
// ============================================================
$schoolName    = isset($pdo) ? getSetting($pdo, 'school_name')    : 'School';
$schoolAddress = isset($pdo) ? getSetting($pdo, 'school_address') : '';
$schoolPhone   = isset($pdo) ? getSetting($pdo, 'school_phone')   : '';
$schoolEmail   = isset($pdo) ? getSetting($pdo, 'school_email')   : '';
?>

<footer class="site-footer">
    <div class="container footer-grid">

        <!-- About column -->
        <div class="footer-col">
            <h3><?= htmlspecialchars($schoolName) ?></h3>
            <p><?= htmlspecialchars($schoolAddress) ?></p>
            <p style="margin-top:.5rem">
                📞 <?= htmlspecialchars($schoolPhone) ?><br>
                ✉ <?= htmlspecialchars($schoolEmail) ?>
            </p>
        </div>

        <!-- Quick links -->
        <div class="footer-col">
            <h4>Quick Links</h4>
            <a href="/school-website/about.php">About Us</a>
            <a href="/school-website/admissions.php">Admissions</a>
            <a href="/school-website/news.php">News &amp; Events</a>
            <a href="/school-website/staff.php">Our Staff</a>
            <a href="/school-website/contact.php">Contact</a>
        </div>

        <!-- Academic links -->
        <div class="footer-col">
            <h4>Academics</h4>
            <a href="/school-website/admissions.php#requirements">Entry Requirements</a>
            <a href="/school-website/admissions.php#downloads">Download Forms</a>
            <a href="/school-website/gallery.php">Photo Gallery</a>
            <a href="/school-website/news.php?category=academics">Academic News</a>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($schoolName) ?>. All rights reserved.
               &nbsp;|&nbsp; Developed by Intellectitech</p>
        </div>
    </div>
</footer>

<script src="/school-website/assets/js/main.js"></script>
</body>
</html>
