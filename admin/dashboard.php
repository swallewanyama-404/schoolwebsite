<?php
// ============================================================
//  admin/dashboard.php — Admin Dashboard
//  Open at: http://localhost/school-website/admin/dashboard.php
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Guard: redirect to login if not authenticated
requireAdmin();

$adminName = $_SESSION['admin_name'];
$pageTitle = 'Dashboard';

// Dashboard stat counts
$totalNews      = (int)$pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 1')->fetchColumn();
$totalDrafts    = (int)$pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 0')->fetchColumn();
$newEnquiries   = (int)$pdo->query("SELECT COUNT(*) FROM admissions_enquiries WHERE status = 'new'")->fetchColumn();
$unreadMessages = (int)$pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();

// Recent 5 news articles
$recentNews = $pdo->query(
    'SELECT id, title, is_published, created_at FROM news ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

// Recent 5 contact messages
$recentMessages = $pdo->query(
    'SELECT id, name, subject, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/school-website/assets/css/style.css">
</head>
<body>

<div class="admin-layout">

    <!-- ── SIDEBAR ── -->
    <?php include 'sidebar.php'; ?>

    <!-- ── MAIN CONTENT ── -->
    <main class="admin-main">
        <h1 class="admin-title">Welcome back, <?= htmlspecialchars($adminName) ?> 👋</h1>

        <!-- Stat cards -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-card-num" style="color:var(--blue)"><?= $totalNews ?></div>
                <div class="stat-card-label">Published Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-num" style="color:var(--orange)"><?= $newEnquiries ?></div>
                <div class="stat-card-label">New Enquiries</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-num" style="color:var(--teal)"><?= $unreadMessages ?></div>
                <div class="stat-card-label">Unread Messages</div>
            </div>
        </div>

        <!-- Quick actions -->
        <div style="display:flex;gap:.75rem;margin-bottom:2rem;flex-wrap:wrap">
            <a href="add-news.php" class="btn btn-blue btn-sm">+ Add Article</a>
            <a href="messages.php" class="btn btn-sm">View Messages <?= $unreadMessages > 0 ? "($unreadMessages)" : '' ?></a>
            <a href="/school-website/" target="_blank" class="btn btn-sm">View Website ↗</a>
        </div>

        <!-- Recent articles -->
        <h2 style="font-size:1.15rem;margin-bottom:1rem;color:var(--navy)">Recent Articles</h2>
        <table class="admin-table" style="margin-bottom:2rem">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recentNews): ?>
                <?php foreach ($recentNews as $n): ?>
                <tr>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td>
                        <span class="badge <?= $n['is_published'] ? 'badge-green' : 'badge-orange' ?>">
                            <?= $n['is_published'] ? 'Published' : 'Draft' ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($n['created_at'])) ?></td>
                    <td>
                        <a href="edit-news.php?id=<?= $n['id'] ?>"
                           style="color:var(--blue);font-size:.9rem">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="4" style="color:var(--muted)">No articles yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Recent messages -->
        <h2 style="font-size:1.15rem;margin-bottom:1rem;color:var(--navy)">Recent Messages</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recentMessages): ?>
                <?php foreach ($recentMessages as $m): ?>
                <tr style="<?= !$m['is_read'] ? 'font-weight:600' : '' ?>">
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['subject']) ?></td>
                    <td><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                    <td>
                        <span class="badge <?= $m['is_read'] ? 'badge-green' : 'badge-red' ?>">
                            <?= $m['is_read'] ? 'Read' : 'Unread' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="4" style="color:var(--muted)">No messages yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    </main>
</div>

</body>
</html>
