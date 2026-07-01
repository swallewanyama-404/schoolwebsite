<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$all_staff   = getAllStaff($pdo);
$departments = getAllDepartments($pdo);

$grouped = [];
foreach ($all_staff as $member) {
    $dept = $member['department_name'] ?? 'General';
    $grouped[$dept][] = $member;
}
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <h1>Our Staff</h1>
        <p>Meet the dedicated educators and administrators shaping our students' futures</p>
        <ul class="breadcrumb"><li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li><li>Staff</li></ul>
    </div>
</section>

<main id="main-content">
    <section class="section">
        <div class="container">
            <?php if ($grouped): ?>
                <?php foreach ($grouped as $dept_name => $members): ?>
                <div class="section-header" style="margin-bottom:2rem;">
                    <h2 style="border-bottom:2px solid var(--gold);padding-bottom:.5rem;display:inline-block;">
                        <?php echo htmlspecialchars($dept_name); ?>
                    </h2>
                </div>
                <div class="grid-4" style="margin-bottom:3rem;">
                    <?php foreach ($members as $member): ?>
                    <div class="staff-card">
                        <div class="staff-avatar-wrap">
                            <div class="staff-avatar"><?php echo strtoupper(substr($member['first_name'], 0, 1)); ?></div>
                        </div>
                        <div class="staff-body">
                            <h3><?php echo htmlspecialchars($member['full_name']); ?></h3>
                            <p class="staff-role"><?php echo htmlspecialchars($member['role']); ?></p>
                            <?php if (!empty($member['subjects'])): ?>
                                <p class="staff-sub"><?php echo htmlspecialchars($member['subjects']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center;color:var(--gray-400);padding:3rem 0;">No staff profiles available yet.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
