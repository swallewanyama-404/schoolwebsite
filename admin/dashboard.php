<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
if (session_status() === PHP_SESSION_NONE){
    session_star();
}

requireAdminLogin();

$allNews      = getAllNews($pdo);
$unread       = getUnreadCount($pdo);
$enquiries    = getAllEnquiries($pdo);
$school_name  = getSetting($pdo, 'school_name');
$recent_log   = getAuditLog($pdo, 10);

// Count new enquiries
$new_enquiries = array_filter($enquiries, fn($e) => $e['status'] === 'new');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { font-family:Arial,sans-serif; background:#f4f4f4; }
        .topbar { background:#0B1F3A; color:#fff; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; }
        .topbar a { color:#f0a500; text-decoration:none; }
        main { max-width:1100px; margin:2rem auto; padding:0 1rem; }
        .stats { display:flex; flex-wrap:wrap; gap:1rem; margin-bottom:2rem; }
        .stat-card { flex:1; min-width:180px; background:#fff; padding:1.5rem; border-radius:8px; text-align:center; box-shadow:0 1px 4px rgba(0,0,0,0.1); border-top:4px solid #0B1F3A; }
        .stat-card .number { font-size:2rem; font-weight:bold; color:#0B1F3A; }
        .stat-card .label  { color:#666; font-size:0.9rem; margin-top:0.3rem; }
        .quick-links { display:flex; flex-wrap:wrap; gap:1rem; margin-bottom:2rem; }
        .quick-links a { background:#0B1F3A; color:#fff; padding:0.8rem 1.5rem; border-radius:6px; text-decoration:none; font-size:0.95rem; }
        .quick-links a:hover { background:#f0a500; color:#000; }
        .card { background:#fff; padding:1.5rem; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); margin-bottom:1.5rem; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:0.6rem 0.8rem; border-bottom:1px solid #eee; text-align:left; font-size:0.88rem; }
        th { color:#555; font-weight:bold; }
        .badge-red { background:#c0392b; color:#fff; padding:0.15rem 0.5rem; border-radius:8px; font-size:0.75rem; }
    </style>
</head>
<body>

<div class="topbar">
    <span>⚙️ Admin Panel — <?php echo htmlspecialchars($school_name); ?></span>
    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> &nbsp;|&nbsp;
        <a href="<?php echo BASE_URL; ?>index.php" target="_blank">View Site</a> &nbsp;|&nbsp;
        <a href="<?php echo BASE_URL; ?>admin/logout.php">Logout</a>
    </span>
</div>

<main>
    <h1>Dashboard</h1>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-card">
            <div class="number"><?php echo count($allNews); ?></div>
            <div class="label">Total Articles</div>
        </div>
        <div class="stat-card">
            <div class="number" style="color:#c0392b;"><?php echo $unread; ?></div>
            <div class="label">Unread Messages</div>
        </div>
        <div class="stat-card">
            <div class="number" style="color:#f0a500;"><?php echo count($new_enquiries); ?></div>
            <div class="label">New Enquiries</div>
        </div>
        <div class="stat-card">
            <div class="number"><?php echo count($enquiries); ?></div>
            <div class="label">Total Enquiries</div>
        </div>
    </div>

    <!-- QUICK LINKS -->
    <div class="quick-links">
        <a href="<?php echo BASE_URL; ?>admin/manage-news.php">📰 Manage News</a>
        <a href="<?php echo BASE_URL; ?>admin/messages.php">✉️ Messages <?php if ($unread > 0): ?><span class="badge-red"><?php echo $unread; ?></span><?php endif; ?></a>
        <a href="<?php echo BASE_URL; ?>admin/manage-enquiries.php">📋 Enquiries <?php if (count($new_enquiries) > 0): ?><span class="badge-red"><?php echo count($new_enquiries); ?></span><?php endif; ?></a>
        <a href="<?php echo BASE_URL; ?>admin/manage-staff.php">👥 Staff</a>
        <a href="<?php echo BASE_URL; ?>admin/manage-events.php">📅 Events</a>
    </div>

    <!-- RECENT AUDIT LOG -->
    <?php if ($recent_log): ?>
    <div class="card">
        <h2>Recent Activity Log</h2>
        <table>
            <tr><th>Action</th><th>Table</th><th>Admin</th><th>Time</th></tr>
            <?php foreach ($recent_log as $log): ?>
            <tr>
                <td><?php echo htmlspecialchars($log['action']); ?></td>
                <td><?php echo htmlspecialchars($log['table_name'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars($log['admin_name'] ?? 'System'); ?></td>
                <td><?php echo date('d M Y H:i', strtotime($log['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

</main>
</body>
</html>
