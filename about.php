<?php
// ============================================================
//  about.php — About School Page
//  Open at: http://localhost/school-website/about.php
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'About Us — ' . getSetting($pdo, 'school_name');

// Fetch page content blocks
$history    = getSetting($pdo, 'history')      ?: '<p>Our school has a rich history of academic excellence spanning over four decades.</p>';
$mission    = getSetting($pdo, 'mission')      ?: 'To provide holistic, values-based education.';
$vision     = getSetting($pdo, 'vision')       ?: 'To be Uganda\'s leading secondary school.';
$coreValues = getSetting($pdo, 'core_values')  ?: 'Excellence, Integrity, Respect, Service';

// Leadership team (is_management = 1)
$leadership = $pdo->query(
    'SELECT * FROM vw_staff_directory WHERE is_management = 1'
)->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<!-- ── PAGE HERO ── -->
<div class="page-hero">
    <div class="container">
        <h1>About Our School</h1>
        <p>Our history, mission, values, and leadership</p>
        <div class="breadcrumb">
            <a href="/">Home</a> &rsaquo; About
        </div>
    </div>
</div>

<!-- ── HISTORY & MISSION ── -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <!-- History -->
            <div>
                <h2 class="section-title">Our History</h2>
                <div style="color:var(--muted);line-height:1.85">
                    <?= $history ?>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div>
                <div style="background:var(--off);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
                    <h3 style="color:var(--teal);margin-bottom:.75rem">Our Mission</h3>
                    <p style="color:var(--muted)"><?= htmlspecialchars($mission) ?></p>
                </div>
                <div style="background:var(--off);border-radius:var(--radius);padding:1.5rem">
                    <h3 style="color:var(--teal);margin-bottom:.75rem">Our Vision</h3>
                    <p style="color:var(--muted)"><?= htmlspecialchars($vision) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CORE VALUES ── -->
<?php if ($coreValues): ?>
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">Core Values</h2>
        <p class="section-sub">The principles that guide everything we do.</p>
        <div class="value-cards">
            <?php foreach (explode(',', $coreValues) as $i => $value): ?>
            <div class="value-card">
                <h4><?= htmlspecialchars(trim($value)) ?></h4>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── LEADERSHIP TEAM ── -->
<?php if ($leadership): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">School Leadership</h2>
        <p class="section-sub">The team leading our school forward.</p>

        <div class="staff-grid">
            <?php foreach ($leadership as $member): ?>
            <div class="staff-card">
                <img class="staff-photo"
                     src="<?= htmlspecialchars($member['photo'] ?: 'assets/images/staff/default.jpg') ?>"
                     alt="<?= htmlspecialchars($member['full_name']) ?>">
                <h4><?= htmlspecialchars($member['full_name']) ?></h4>
                <p><?= htmlspecialchars($member['role']) ?></p>
                <?php if ($member['qualification']): ?>
                <p class="staff-subjects"><?= htmlspecialchars($member['qualification']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
