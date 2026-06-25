<?php
// ============================================================
//  news.php — News Listing Page
//  Open at: http://localhost/school-website/news.php
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'News & Announcements';

// ── PAGINATION ────────────────────────────────────────────────
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$offset  = ($page - 1) * $perPage;

// ── OPTIONAL CATEGORY FILTER ─────────────────────────────────
$catSlug   = clean($_GET['category'] ?? '');
$catWhere  = '';
$catParams = [];
if ($catSlug) {
    $catWhere  = 'AND nc.slug = ?';
    $catParams = [$catSlug];
}

// Total count for pagination
$countSql = "SELECT COUNT(*) FROM news n
             LEFT JOIN news_categories nc ON nc.id = n.category_id
             WHERE n.is_published = 1 $catWhere";
$total = $pdo->prepare($countSql);
$total->execute($catParams);
$totalRows  = (int)$total->fetchColumn();
$totalPages = (int)ceil($totalRows / $perPage);

// Articles
$sql = "SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image,
               n.published_at, n.views,
               nc.name AS cat_name, nc.color AS cat_color
        FROM news n
        LEFT JOIN news_categories nc ON nc.id = n.category_id
        WHERE n.is_published = 1 $catWhere
        ORDER BY n.published_at DESC
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($catParams);
$articles = $stmt->fetchAll();

// All categories for filter tabs
$categories = $pdo->query('SELECT * FROM news_categories ORDER BY name')->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
    <div class="container">
        <h1>News &amp; Announcements</h1>
        <p>Latest updates from the school</p>
        <div class="breadcrumb"><a href="/">Home</a> &rsaquo; News</div>
    </div>
</div>

<section class="section">
    <div class="container">

        <!-- Category filter tabs -->
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.75rem">
            <a href="news.php"
               class="btn btn-sm <?= !$catSlug ? 'btn-blue' : 'btn-outline-dark' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="news.php?category=<?= urlencode($cat['slug']) ?>"
               class="btn btn-sm"
               style="background:<?= $catSlug === $cat['slug'] ? $cat['color'] : 'transparent' ?>;
                      color:<?= $catSlug === $cat['slug'] ? '#fff' : $cat['color'] ?>;
                      border:1.5px solid <?= $cat['color'] ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Articles grid -->
        <?php if ($articles): ?>
        <div class="cards-grid">
            <?php foreach ($articles as $a): ?>
            <div class="card">
                <?php if ($a['featured_image']): ?>
                <img class="card-img"
                     src="<?= htmlspecialchars($a['featured_image']) ?>"
                     alt="<?= htmlspecialchars($a['title']) ?>">
                <?php endif; ?>

                <div class="card-body">
                    <span class="card-badge"
                          style="background:<?= htmlspecialchars($a['cat_color'] ?? '#1565C0') ?>">
                        <?= htmlspecialchars($a['cat_name'] ?? 'News') ?>
                    </span>
                    <h3><?= htmlspecialchars($a['title']) ?></h3>
                    <p><?= excerpt($a['excerpt'] ?? '', 130) ?></p>
                </div>

                <div class="card-footer">
                    <a href="article.php?slug=<?= urlencode($a['slug']) ?>">Read more →</a>
                    <span><?= date('d M Y', strtotime($a['published_at'])) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="display:flex;gap:.5rem;margin-top:2rem;flex-wrap:wrap">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="news.php?page=<?= $i ?><?= $catSlug ? '&category=' . urlencode($catSlug) : '' ?>"
               class="btn btn-sm <?= $i === $page ? 'btn-blue' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <p style="color:var(--muted);padding:2rem 0">No articles found for this category.</p>
        <?php endif; ?>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
