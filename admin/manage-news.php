<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdminLogin();

$message = '';

// DELETE
if (isset($_GET['delete'])) {
    deleteNews($pdo, (int)$_GET['delete']);
    logAction($pdo, $_SESSION['admin_id'], 'deleted_news', 'news', (int)$_GET['delete'], 'Deleted news article');
    header('Location: ' . BASE_URL . 'admin/manage-news.php');
    exit;
}

// TOGGLE PUBLISH
if (isset($_GET['toggle'], $_GET['status'])) {
    togglePublish($pdo, (int)$_GET['toggle'], (int)$_GET['status']);
    header('Location: ' . BASE_URL . 'admin/manage-news.php');
    exit;
}

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['news_id'])) {
    $category_id = (int)$_POST['category_id'];
    $title       = clean($_POST['title']);
    $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $_POST['title'])));
    $excerpt     = clean($_POST['excerpt']);
    $body        = clean($_POST['body']);
    $image       = clean($_POST['featured_image']);
    $author_id   = $_SESSION['admin_id'];

    if (createNews($pdo, $category_id, $title, $slug, $excerpt, $body, $image, $author_id)) {
        logAction($pdo, $author_id, 'created_news', 'news', 0, "Created article: $title");
        $message = 'Article created successfully.';
    } else {
        $message = 'Error creating article. Title may already exist.';
    }
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_id'])) {
    $id          = (int)$_POST['news_id'];
    $category_id = (int)$_POST['category_id'];
    $title       = clean($_POST['title']);
    $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $_POST['title'])));
    $excerpt     = clean($_POST['excerpt']);
    $body        = clean($_POST['body']);
    $image       = clean($_POST['featured_image']);

    updateNews($pdo, $id, $category_id, $title, $slug, $excerpt, $body, $image);
    $message = 'Article updated successfully.';
}

$allNews    = getAllNews($pdo);
$categories = getAllCategories($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage News</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; }
        main { max-width:1100px; margin:2rem auto; padding:0 1rem; }
        form input, form select, form textarea { width:100%; padding:0.5rem; margin-top:0.3rem; box-sizing:border-box; }
        form textarea { height:150px; }
        .form-group { margin-bottom:1rem; }
        label { font-weight:bold; }
        .btn { padding:0.5rem 1.2rem; border:none; border-radius:4px; cursor:pointer; }
        .btn-primary { background:#0B1F3A; color:#fff; }
        .btn-danger  { background:#c0392b; color:#fff; }
        .btn-success { background:#27ae60; color:#fff; }
        table { width:100%; border-collapse:collapse; background:#fff; margin-top:1rem; }
        th, td { padding:0.7rem 1rem; border-bottom:1px solid #ddd; text-align:left; }
        th { background:#0B1F3A; color:#fff; }
        .alert { padding:0.8rem 1rem; border-radius:4px; margin-bottom:1rem; background:#d4edda; color:#155724; }
        .card { background:#fff; padding:1.5rem; border-radius:8px; margin-bottom:2rem; box-shadow:0 1px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<main>
    <p><a href="<?php echo BASE_URL; ?>admin/dashboard.php">← Back to Dashboard</a></p>
    <h1>Manage News Articles</h1>

    <?php if ($message): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- ADD NEW ARTICLE FORM -->
    <div class="card">
        <h2>Add New Article</h2>
        <form method="POST">
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Article Title *</label>
                <input type="text" name="title" placeholder="Enter article title" required>
            </div>
            <div class="form-group">
                <label>Excerpt (short summary shown on listing page)</label>
                <textarea name="excerpt" placeholder="2-3 sentence summary of the article..." style="height:80px;"></textarea>
            </div>
            <div class="form-group">
                <label>Full Article Body *</label>
                <textarea name="body" placeholder="Write the full article content here..." required></textarea>
            </div>
            <div class="form-group">
                <label>Featured Image Path (optional)</label>
                <input type="text" name="featured_image" placeholder="assets/images/news/photo.jpg">
            </div>
            <button type="submit" class="btn btn-primary">Publish Article</button>
        </form>
    </div>

    <!-- ALL ARTICLES TABLE -->
    <div class="card">
        <h2>All Articles (<?php echo count($allNews); ?>)</h2>
        <?php if ($allNews): ?>
        <table>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Views</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($allNews as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td><?php echo htmlspecialchars($item['category_name'] ?? '—'); ?></td>
                <td>
                    <?php if ($item['is_published']): ?>
                        <span style="color:green; font-weight:bold;">Live</span>
                    <?php else: ?>
                        <span style="color:orange;">Draft</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $item['views']; ?></td>
                <td><?php echo date('d M Y', strtotime($item['created_at'])); ?></td>
                <td>
                    <?php if ($item['is_published']): ?>
                        <a href="?<?php echo BASE_URL; ?>admin/manage-news.phptoggle=<?php echo $item['id']; ?>&status=0" class="btn btn-primary" style="font-size:0.8rem;">Unpublish</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>admin/manage-news.php?toggle=<?php echo $item['id']; ?>&status=1" class="btn btn-success" style="font-size:0.8rem;">Publish</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>admin/manage-new.php?delete=<?php echo $item['id']; ?>"
                       class="btn btn-danger" style="font-size:0.8rem;"
                       onclick="return confirm('Delete this article permanently?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>No articles yet. Add one above.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
