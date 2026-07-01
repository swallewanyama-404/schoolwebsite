<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$mission     = getPageContent($pdo, 'about', 'mission');
$vision      = getPageContent($pdo, 'about', 'vision');
$core_values = getPageContent($pdo, 'about', 'core_values');
$management  = getManagementStaff($pdo);
$school_name = getSetting($pdo, 'school_name');
$founded     = getSetting($pdo, 'founded_year');
$students    = getSetting($pdo, 'total_students');
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <h1>About <?php echo htmlspecialchars($school_name ?: "St. Mary's School"); ?></h1>
        <p>Established in <?php echo htmlspecialchars($founded ?: '1985'); ?> &mdash; proudly serving over <?php echo htmlspecialchars($students ?: '1,200'); ?> students</p>
        <ul class="breadcrumb"><li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li><li>About</li></ul>
    </div>
</section>

<main id="main-content">

    <section class="section">
        <div class="container">
            <div class="grid-3">
                <div class="mission-card blue">
                    <h2>🎯 Our Mission</h2>
                    <p><?php echo htmlspecialchars($mission ?: "To provide holistic, quality education that empowers students to achieve academic excellence and become responsible, principled citizens."); ?></p>
                </div>
                <div class="mission-card gold">
                    <h2>🔭 Our Vision</h2>
                    <p><?php echo htmlspecialchars($vision ?: "To be a leading institution recognized for nurturing confident, innovative, and compassionate leaders of tomorrow."); ?></p>
                </div>
                <div class="mission-card green">
                    <h2>💎 Core Values</h2>
                    <p><?php echo htmlspecialchars($core_values ?: "Integrity, Excellence, Respect, Discipline, and Service to community and country."); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background:var(--white); padding-top:0;">
        <div class="container">
            <div class="section-header center">
                <span class="section-label">Meet Our Team</span>
                <h2>School Leadership</h2>
                <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
            </div>
            <?php if ($management): ?>
                <div class="grid-4">
                    <?php foreach ($management as $member): ?>
                    <div class="leadership-card">
                        <div class="leadership-avatar"><?php echo strtoupper(substr($member['first_name'], 0, 1)); ?></div>
                        <h3><?php echo htmlspecialchars($member['full_name']); ?></h3>
                        <p class="role"><?php echo htmlspecialchars($member['role']); ?></p>
                        <?php if (!empty($member['department_name'])): ?>
                            <p class="dept"><?php echo htmlspecialchars($member['department_name']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align:center;color:var(--gray-400);">No leadership profiles available yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <section style="background:var(--gray-100); padding:3.5rem 0;">
        <div class="container">
            <div class="grid-3" style="text-align:center;">
                <div>
                    <div class="stat-number" style="color:var(--navy);"><?php echo htmlspecialchars($founded ?: '1985'); ?></div>
                    <p style="font-weight:700;color:var(--gray-800);">Years of Excellence</p>
                </div>
                <div>
                    <div class="stat-number" style="color:var(--navy);"><?php echo htmlspecialchars($students ?: '1200'); ?>+</div>
                    <p style="font-weight:700;color:var(--gray-800);">Current Enrollment</p>
                </div>
                <div>
                    <div class="stat-number" style="color:var(--navy);">40+</div>
                    <p style="font-weight:700;color:var(--gray-800);">Qualified Staff Members</p>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
