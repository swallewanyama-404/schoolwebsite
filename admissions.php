<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

$s1_requirements = getRequirementsByLevel($pdo, 'S1');
$s5_requirements = getRequirementsByLevel($pdo, 'S5');
$documents       = getAdmissionDocuments($pdo);
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <h1>Admissions</h1>
        <p>Begin your child's journey with us &mdash; applications for the next academic year are now open</p>
        <ul class="breadcrumb"><li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li><li>Admissions</li></ul>
    </div>
</section>

<main id="main-content">

    <?php if ($success): ?>
    <div class="container" style="padding-top:2rem;">
        <div class="alert alert-success"><?php echo $success; ?></div>
    </div>
    <?php endif; ?>

    <!-- Requirements -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Entry Criteria</span>
                <h2>Admission Requirements</h2>
                <div class="divider"></div>
            </div>
            <div class="grid-2">
                <div class="requirement-card">
                    <span class="tag tag-navy" style="margin-bottom:1rem;display:inline-block;">Senior One (S1)</span>
                    <?php foreach ($s1_requirements as $req): ?>
                        <h3><?php echo htmlspecialchars($req['title']); ?></h3>
                        <p><?php echo htmlspecialchars($req['description']); ?></p>
                    <?php endforeach; ?>
                    <?php if (!$s1_requirements): ?><p>Details coming soon.</p><?php endif; ?>
                </div>
                <div class="requirement-card">
                    <span class="tag tag-gold" style="margin-bottom:1rem;display:inline-block;">Senior Five (S5)</span>
                    <?php foreach ($s5_requirements as $req): ?>
                        <h3><?php echo htmlspecialchars($req['title']); ?></h3>
                        <p><?php echo htmlspecialchars($req['description']); ?></p>
                    <?php endforeach; ?>
                    <?php if (!$s5_requirements): ?><p>Details coming soon.</p><?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Downloadable Forms -->
    <section class="section" style="background:var(--white); padding-top:0;">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Paperwork</span>
                <h2>Download Application Forms</h2>
                <div class="divider"></div>
            </div>
            <div class="grid-3">
                <?php foreach ($documents as $doc): ?>
                <a href="<?php echo BASE_URL; ?>view_document.php?id=<?php echo $doc['id']; ?>" class="download-card">
                    <div class="download-icon">📄</div>
                    <div>
                        <h3><?php echo htmlspecialchars($doc['title']); ?></h3>
                        <p>Click to view / download</p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Enquiry Form -->
    <section class="section" style="background:var(--gray-100);">
        <div class="container" style="max-width:700px;">
            <div class="section-header center">
                <span class="section-label">Get Started</span>
                <h2>Submit an Enquiry</h2>
                <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
                <p style="text-align:center;">Fill in the form below and our admissions team will get back to you shortly.</p>
            </div>
            <div class="form-card">
                <form action="<?php echo BASE_URL; ?>process_enquiry.php" method="POST">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Parent / Guardian Full Name *</label>
                            <input class="form-control" type="text" name="parent_name" placeholder="e.g. Jane Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Parent Phone Number *</label>
                            <input class="form-control" type="tel" name="parent_phone" placeholder="+256 7XX XXX XXX" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Parent Email (optional)</label>
                        <input class="form-control" type="email" name="parent_email" placeholder="you@example.com">
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Student Full Name *</label>
                            <input class="form-control" type="text" name="student_name" placeholder="Student's name" required>
                        </div>
                        <div class="form-group">
                            <label>Entry Level *</label>
                            <select class="form-control" name="entry_level" required>
                                <option value="">-- Select Entry Level --</option>
                                <option value="S1">Senior 1 (S1)</option>
                                <option value="S5">Senior 5 (S5)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Current School</label>
                            <input class="form-control" type="text" name="current_school" placeholder="Name of current school">
                        </div>
                        <div class="form-group">
                            <label>PLE Aggregate (S1 only)</label>
                            <input class="form-control" type="number" name="ple_aggregate" placeholder="4 - 36" min="4" max="36">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Additional Message</label>
                        <textarea class="form-control" name="message" placeholder="Anything else we should know?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Submit Enquiry →</button>
                </form>
            </div>
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
