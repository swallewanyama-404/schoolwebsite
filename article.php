<?php
// ============================================================
//  article.php — Single Article Page
//  Open at: http://localhost/school-website/article.php?slug=your-slug
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get slug from URL — redirect if missing
$slug = clean($_GET['slug'] ?? '');
if (!$slug) {
    header('Location: news.php');
    exit;
}

// Fetch article
$stmt = $pdo->prepare(
    'SELECT n.*, nc.name AS cat_name, nc.color AS cat_color
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     WHERE n.slug = ? AND n.is_published = 1
     LIMIT 1'
);
$stmt->execute([$slug]);
$article = $stmt->fetch();

// Redirect if not found
if (!$article) {
    header('Location: news.php');
    exit;
}

// Increment view counter
$pdo->prepare('UPDATE news SET views = views + 1 WHERE id = ?')
    ->execute([$article['id']]);

$pageTitle = $article['title'];

// Related articles (same category, not this article)
$related = [];
if ($article['category_id']) {
    $relStmt = $pdo->prepare(
        'SELECT id, title, slug, excerpt, featured_image
         FROM news
         WHERE category_id = ? AND id != ? AND is_published = 1
         ORDER BY published_at DESC LIMIT 3'
    );
    $relStmt->execute([$article['category_id'], $article['id']]);
    $related = $relStmt->fetchAll();
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
    <div class="container">
        <span class="card-badge"
              style="background:<?= htmlspecialchars($article['cat_color'] ?? '#1565C0') ?>;margin-bottom:.75rem;display:inline-block">
            <?= htmlspecialchars($article['cat_name'] ?? 'News') ?>
        </span>
        <h1 style="font-size:2rem;max-width:800px;margin:0 auto"><?= htmlspecialchars($article['title']) ?></h1>
        <p style="margin-top:.75rem">
            <?= date('d F Y', strtotime($article['published_at'])) ?>
            &nbsp;·&nbsp; <?= number_format($article['views']) ?> views
        </p>
        <div class="breadcrumb">
            <a href="/">Home</a> &rsaquo;
            <a href="news.php">News</a> &rsaquo;
            <?= htmlspecialchars(substr($article['title'], 0, 40)) ?>...
        </div>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:820px">

        <!-- Featured image -->
        <?php if ($article['featured_image']): ?>
        <img src="<?= htmlspecialchars($article['featured_image']) ?>"
             alt="<?= htmlspecialchars($article['title']) ?>"
             style="width:100%;border-radius:var(--radius-lg);margin-bottom:2rem;max-height:450px;object-fit:cover">
        <?php endif; ?>

        <!-- Article body — HTML from database is rendered directly -->
        <div class="article-body">
            <?= $article['body'] ?>
        </div>

        <!-- Back link -->
        <div style="margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid var(--border)">
            <a href="news.php" style="color:var(--blue)">← Back to all news</a>
        </div>
    </div>
</section>

<!-- RELATED ARTICLES -->
<?php if ($related): ?>
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">Related Articles</h2>
        <div class="cards-grid">
            <?php foreach ($related as $r): ?>
            <div class="card">
                <?php if ($r['featured_image']): ?>
                <img class="card-img"
                     src="<?= htmlspecialchars($r['featured_image']) ?>"
                     alt="">
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= htmlspecialchars($r['title']) ?></h3>
                    <p><?= excerpt($r['excerpt'] ?? '', 100) ?></p>
                </div>
                <div class="card-footer">
                    <a href="article.php?slug=<?= urlencode($r['slug']) ?>">Read more →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
