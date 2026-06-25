<?php
// ============================================================
//  index.php — Homepage
//  school-website/index.php
//  Open at: http://localhost/school-website/
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = getSetting($pdo, 'school_name') . ' — Home';

// ── FETCH DATA FROM DATABASE ──────────────────────────────────

// Latest 3 published news articles
$stmt = $pdo->query(
    'SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image,
            n.published_at,
            nc.name  AS cat_name,
            nc.color AS cat_color
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     WHERE n.is_published = 1
     ORDER BY n.published_at DESC
     LIMIT 3'
);
$latestNews = $stmt->fetchAll();

// Upcoming events (today or future, max 4)
$events = $pdo->query(
    'SELECT * FROM events
     WHERE is_published = 1
       AND event_date >= CURDATE()
     ORDER BY event_date ASC
     LIMIT 4'
)->fetchAll();

// Published testimonials
$testimonials = $pdo->query(
    'SELECT * FROM testimonials
     WHERE is_published = 1
     ORDER BY sort_order ASC
     LIMIT 3'
)->fetchAll();

// Page content blocks
$heroTitle    = getSetting($pdo, 'hero_title');
$heroSubtitle = getSetting($pdo, 'hero_subtitle');
$foundedYear  = getSetting($pdo, 'founded_year');
$totalStudents= getSetting($pdo, 'total_students');
?>
<?php require_once 'includes/header.php'; ?>

<!-- ── HERO SECTION ── -->
<section class="hero">
    <div class="container">
        <h1><?= htmlspecialchars($heroTitle ?: getSetting($pdo,'school_name')) ?></h1>
        <p><?= htmlspecialchars($heroSubtitle ?: 'Shaping tomorrow\'s leaders through quality education.') ?></p>
        <div class="hero-btns">
            <a href="admissions.php" class="btn btn-primary">Apply Now</a>
            <a href="about.php"      class="btn btn-outline">Learn More</a>
        </div>
    </div>
</section>

<!-- ── STATS BAR ── -->
<section class="stats-bar">
    <div class="container stats-grid">
        <?php
        $stats = [
            ['Founded',  $foundedYear ?: '1985'],
            ['Students', ($totalStudents ?: '1200') . '+'],
            ['Teachers', '60+'],
            ['Subjects', '18+'],
        ];
        foreach ($stats as [$label, $value]):
        ?>
        <div>
            <div class="stat-num"><?= htmlspecialchars($value) ?></div>
            <div class="stat-label"><?= htmlspecialchars($label) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── LATEST NEWS ── -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Latest News</h2>
        <p class="section-sub">Stay up to date with what is happening at school.</p>

        <?php if ($latestNews): ?>
        <div class="cards-grid">
            <?php foreach ($latestNews as $article): ?>
            <div class="card">
                <?php if ($article['featured_image']): ?>
                <img class="card-img"
                     src="<?= htmlspecialchars($article['featured_image']) ?>"
                     alt="<?= htmlspecialchars($article['title']) ?>">
                <?php endif; ?>

                <div class="card-body">
                    <span class="card-badge"
                          style="background:<?= htmlspecialchars($article['cat_color'] ?? '#1565C0') ?>">
                        <?= htmlspecialchars($article['cat_name'] ?? 'News') ?>
                    </span>
                    <h3><?= htmlspecialchars($article['title']) ?></h3>
                    <p><?= excerpt($article['excerpt'] ?? '', 120) ?></p>
                </div>

                <div class="card-footer">
                    <a href="article.php?slug=<?= urlencode($article['slug']) ?>">Read more →</a>
                    <span><?= date('d M Y', strtotime($article['published_at'])) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p style="margin-top:2rem">
            <a href="news.php" class="btn btn-blue">View All News</a>
        </p>
        <?php else: ?>
        <p style="color:var(--muted)">No news published yet.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ── UPCOMING EVENTS ── -->
<?php if ($events): ?>
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">Upcoming Events</h2>
        <p class="section-sub">Mark your calendar — don't miss these important dates.</p>

        <?php foreach ($events as $ev): ?>
        <div class="event-item">
            <div class="event-date">
                <div class="day"><?= date('d', strtotime($ev['event_date'])) ?></div>
                <div class="month"><?= date('M Y', strtotime($ev['event_date'])) ?></div>
            </div>
            <div class="event-body">
                <strong><?= htmlspecialchars($ev['title']) ?></strong>
                <small>
                    <?php if ($ev['location']): ?>
                    📍 <?= htmlspecialchars($ev['location']) ?>
                    <?php endif; ?>
                    <?php if ($ev['start_time']): ?>
                    &nbsp;·&nbsp; 🕗 <?= date('g:i A', strtotime($ev['start_time'])) ?>
                    <?php endif; ?>
                </small>
                <?php if ($ev['description']): ?>
                <p style="margin-top:.3rem;color:var(--muted);font-size:.9rem">
                    <?= htmlspecialchars(excerpt($ev['description'], 100)) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ── TESTIMONIALS ── -->
<?php if ($testimonials): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">What People Say</h2>
        <p class="section-sub">Hear from our students and parents.</p>

        <div class="cards-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <p><?= htmlspecialchars($t['content']) ?></p>
                <div class="testimonial-author"><?= htmlspecialchars($t['author_name']) ?></div>
                <div class="testimonial-role"><?= htmlspecialchars($t['author_role'] ?? '') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── CALL TO ACTION ── -->
<section class="section section-alt">
    <div class="container" style="text-align:center">
        <h2 class="section-title">Ready to Join Us?</h2>
        <p class="section-sub" style="margin-bottom:1.5rem">
            Applications for 2026/27 are now open for S1 and S5 entry.
        </p>
        <a href="admissions.php" class="btn btn-primary" style="font-size:1.1rem;padding:.9rem 2.5rem">
            Start Your Application
        </a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
