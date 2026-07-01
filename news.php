<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$news       = getPublishedNews($pdo, 20);
$categories = getAllCategories($pdo);
$active_cat = $_GET['category'] ?? '';
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <h1>News &amp; Announcements</h1>
        <p>Stay up to date with everything happening at our school</p>
        <ul class="breadcrumb"><li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li><li>News</li></ul>
    </div>
</section>

<main id="main-content">
    <section class="section">
        <div class="container">

            <!-- Category Filter -->
            <div class="filter-bar">
                <a href="<?php echo BASE_URL; ?>news.php" class="filter-btn <?php echo $active_cat==''?'filter-btn-active':''; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?php echo BASE_URL; ?>news.php?category=<?php echo $cat['slug']; ?>"
                       class="filter-btn <?php echo $active_cat==$cat['slug']?'filter-btn-active':''; ?>"
                       style="<?php echo $active_cat==$cat['slug']?('background:'.htmlspecialchars($cat['color']).';color:#fff;'):''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- News Grid -->
            <?php if ($news): ?>
                <div class="grid-3">
                    <?php foreach ($news as $article): ?>
                    <div class="news-card">
                        <div class="news-card-img">📰</div>
                        <div class="news-card-body">
                            <span class="tag" style="background:<?php echo $article['category_color'] ?? '#607080'; ?>;color:#fff;">
                                <?php echo htmlspecialchars($article['category_name'] ?? 'General'); ?>
                            </span>
                            <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                            <?php if (!empty($article['excerpt'])): ?>
                                <p><?php echo htmlspecialchars($article['excerpt']); ?></p>
                            <?php endif; ?>
                            <p class="news-card-meta">
                                By <?php echo htmlspecialchars($article['author_name'] ?? 'Admin'); ?>
                                &mdash; <?php echo date('d M Y', strtotime($article['published_at'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align:center;color:var(--gray-400);padding:3rem 0;">No news articles published yet. Check back soon.</p>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
