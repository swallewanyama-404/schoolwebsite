<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$o_level_subjects = getSubjectsByLevel($pdo, 'O_LEVEL');
$a_level_subjects = getSubjectsByLevel($pdo, 'A_LEVEL');
$departments      = getAllDepartments($pdo);
$faqs             = getFaqsByCategory($pdo, 'academics');
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <h1>Academics</h1>
        <p>A comprehensive UNEB-aligned curriculum at both O Level and A Level</p>
        <ul class="breadcrumb"><li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li><li>Academics</li></ul>
    </div>
</section>

<main id="main-content">

    <!-- Departments -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Our Faculty</span>
                <h2>Academic Departments</h2>
                <div class="divider"></div>
            </div>
            <div class="grid-3">
                <?php foreach ($departments as $dept): ?>
                <div class="dept-card">
                    <h3><?php echo htmlspecialchars($dept['name']); ?></h3>
                    <p><?php echo htmlspecialchars($dept['description']); ?></p>
                    <?php if (!empty($dept['head_of_dept'])): ?>
                        <p style="color:var(--gray-400);font-size:.8rem;margin-top:.5rem;">Head of Department: <?php echo htmlspecialchars($dept['head_of_dept']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- O Level Subjects -->
    <section class="section" style="background:var(--white); padding-top:0;">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Lower Secondary</span>
                <h2>O Level Subjects (S1 &ndash; S4)</h2>
                <div class="divider"></div>
            </div>
            <div>
                <?php foreach ($o_level_subjects as $sub): ?>
                <span class="subject-pill <?php echo $sub['is_compulsory'] ? 'pill-navy' : 'pill-gray'; ?>">
                    <?php echo htmlspecialchars($sub['name']); ?>
                    <?php if ($sub['is_compulsory']): ?><small>★</small><?php endif; ?>
                </span>
                <?php endforeach; ?>
            </div>
            <p style="margin-top:1rem;font-size:.85rem;color:var(--gray-400);">★ = Compulsory subject</p>
        </div>
    </section>

    <!-- A Level Subjects -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Upper Secondary</span>
                <h2>A Level Subjects (S5 &ndash; S6)</h2>
                <div class="divider"></div>
            </div>
            <div>
                <?php foreach ($a_level_subjects as $sub): ?>
                <span class="subject-pill pill-green"><?php echo htmlspecialchars($sub['name']); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($faqs): ?>
    <section class="section" style="background:var(--gray-100);">
        <div class="container" style="max-width:800px;">
            <div class="section-header center">
                <span class="section-label">Questions?</span>
                <h2>Academic FAQs</h2>
                <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
            </div>
            <?php foreach ($faqs as $faq): ?>
                <details class="faq">
                    <summary><?php echo htmlspecialchars($faq['question']); ?></summary>
                    <div class="faq-body"><?php echo htmlspecialchars($faq['answer']); ?></div>
                </details>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php require_once 'includes/footer.php'; ?>
