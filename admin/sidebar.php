<?php
// ============================================================
//  admin/sidebar.php — Shared Admin Sidebar Navigation
//  Include this in every admin page.
// ============================================================
$currentAdmin = basename($_SERVER['PHP_SELF'], '.php');
$unread = (int)$pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
?>
<aside class="admin-sidebar">
    <div class="admin-brand">🏫 Admin Panel</div>
    <nav class="admin-nav">
        <a href="dashboard.php" class="<?= $currentAdmin==='dashboard'?'active':'' ?>">
            📊 Dashboard
        </a>
        <a href="news.php" class="<?= in_array($currentAdmin,['news','add-news','edit-news'])?'active':'' ?>">
            📰 News Articles
        </a>
        <a href="messages.php" class="<?= $currentAdmin==='messages'?'active':'' ?>">
            ✉ Messages
            <?php if ($unread > 0): ?>
            <span class="badge badge-red" style="margin-left:.5rem"><?= $unread ?></span>
            <?php endif; ?>
        </a>
        <a href="/school-website/" target="_blank">🌐 View Website ↗</a>
        <a href="logout.php" style="margin-top:2rem;color:#E06C75">🚪 Logout</a>
    </nav>
    <div style="padding:1.5rem;font-size:.8rem;color:#4A6A8A;margin-top:auto">
        Logged in as:<br>
        <strong style="color:#8BAED0"><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></strong>
    </div>
</aside>
