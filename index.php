<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$school_name = getSetting($pdo, 'school_name');
$hero_title  = getPageContent($pdo, 'home', 'hero_title');
$hero_sub    = getPageContent($pdo, 'home', 'hero_subtitle');
$founded     = getSetting($pdo, 'founded_year');
$students    = getSetting($pdo, 'total_students');
$events      = getUpcomingEvents($pdo, 3);
$testimonials= getTestimonials($pdo);
?>

<main id="main-content">

    <!-- HERO -->
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-content">
                <span class="hero-badge">🎓 Admissions Open For 2027</span>
                <h1><?php echo htmlspecialchars($hero_title ?: "Shaping Minds, Building"); ?> <span>Character</span></h1>
                <p class="hero-sub"><?php echo htmlspecialchars($hero_sub ?: "St. Mary's School provides a nurturing environment where students excel academically, grow in faith, and develop into confident, principled leaders of tomorrow."); ?></p>
                <div class="hero-actions">
                    <a href="<?php echo BASE_URL; ?>admissions.php" class="btn btn-primary">Apply Now →</a>
                    <a href="<?php echo BASE_URL; ?>about.php" class="btn btn-outline">Learn More</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-emblem">
                    <div class="hero-emblem-inner">
                        <span class="emblem-cross">✚</span>
                        <span class="emblem-name">ST. MARY'S<br>SCHOOL</span>
                        <span class="emblem-motto">Est. <?php echo htmlspecialchars($founded ?: '1985'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats-bar">
        <div class="stats-bar-inner">
            <div class="stat-item">
                <span class="stat-number"><?php echo htmlspecialchars($founded ?: '1985'); ?></span>
                <span class="stat-label">Year Founded</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo htmlspecialchars($students ?: '1200'); ?>+</span>
                <span class="stat-label">Students</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">98%</span>
                <span class="stat-label">Pass Rate</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">40+</span>
                <span class="stat-label">Qualified Staff</span>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="section">
        <div class="container">
            <div class="section-header center">
                <span class="section-label">Why Choose Us</span>
                <h2>A Foundation For Lifelong Success</h2>
                <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
            </div>
            <div class="grid-3">
                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3>Academic Excellence</h3>
                    <p>A rigorous UNEB-aligned curriculum delivered by experienced, dedicated educators committed to every student's success.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🤝</div>
                    <h3>Character Formation</h3>
                    <p>We nurture integrity, discipline, and compassion alongside academics, preparing well-rounded young leaders.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏆</div>
                    <h3>Co-Curricular Excellence</h3>
                    <p>From sports to music and clubs, students discover and develop their talents beyond the classroom.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- LATEST NEWS via AJAX -->
    <section class="section" style="background:var(--white); padding-top:0;">
        <div class="container">
            <div class="section-header center">
                <span class="section-label">Stay Informed</span>
                <h2>Latest News &amp; Announcements</h2>
                <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
            </div>
            <div id="news-feed" class="grid-3">
                <p style="text-align:center;grid-column:1/-1;color:var(--gray-400);">Loading news...</p>
            </div>
            <p style="text-align:center;margin-top:2.5rem;">
                <a href="<?php echo BASE_URL; ?>news.php" class="btn btn-navy">View All News →</a>
            </p>
        </div>
    </section>

    <!-- UPCOMING EVENTS -->
    <?php if ($events): ?>
    <section class="section" style="background:var(--gray-100);">
        <div class="container">
            <div class="section-header center">
                <span class="section-label">Mark Your Calendar</span>
                <h2>Upcoming Events</h2>
                <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
            </div>
            <div class="grid-3">
                <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <div class="event-date-box">
                        <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                        <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                    </div>
                    <div class="event-info">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <?php if (!empty($event['location'])): ?>
                            <p class="event-location">📍 <?php echo htmlspecialchars($event['location']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- TESTIMONIALS -->
    <?php if ($testimonials): ?>
    <section class="section">
        <div class="container">
            <div class="section-header center">
                <span class="section-label">Testimonials</span>
                <h2>What Our Community Says</h2>
                <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
            </div>
            <div class="grid-3">
                <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card">
                    <p class="testimonial-text">"<?php echo htmlspecialchars($t['content']); ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><?php echo strtoupper(substr($t['author_name'],0,1)); ?></div>
                        <div>
                            <div class="testimonial-name"><?php echo htmlspecialchars($t['author_name']); ?></div>
                            <div class="testimonial-role"><?php echo htmlspecialchars($t['author_role']); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA STRIP -->
    <section style="background:linear-gradient(135deg,var(--navy),var(--navy-light)); padding:3.5rem 0; text-align:center;">
        <div class="container">
            <h2 style="color:#fff; margin-bottom:1rem;">Ready to Join Our School Family?</h2>
            <p style="color:rgba(255,255,255,.75); max-width:560px; margin:0 auto 1.75rem;">
                Applications for the upcoming academic year are now open. Give your child the foundation they deserve.
            </p>
            <a href="<?php echo BASE_URL; ?>admissions.php" class="btn btn-primary">Start Your Application →</a>
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
