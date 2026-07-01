<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdminLogin();

$message = '';

// DELETE
if (isset($_GET['delete'])) {
    deleteEvent($pdo, (int)$_GET['delete']);
    header('Location: ' . BASE_URL . 'admin/manage-events.php');
    exit;
}

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = clean($_POST['title']);
    $description = clean($_POST['description']);
    $location    = clean($_POST['location']);
    $event_date  = clean($_POST['event_date']);
    $start_time  = clean($_POST['start_time']);
    $end_time    = clean($_POST['end_time']);
    $admin_id    = $_SESSION['admin_id'];

    createEvent($pdo, $title, $description, $location, $event_date, $start_time, $end_time, $admin_id);
    logAction($pdo, $admin_id, 'created_event', 'events', 0, "Created event: $title");
    $message = 'Event created successfully.';
}

$allEvents = getAllEvents($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { font-family:Arial,sans-serif; background:#f4f4f4; }
        main { max-width:1000px; margin:2rem auto; padding:0 1rem; }
        .card { background:#fff; padding:1.5rem; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); margin-bottom:2rem; }
        .form-row { display:flex; gap:1rem; flex-wrap:wrap; }
        .form-group { flex:1; min-width:180px; margin-bottom:1rem; }
        label { font-weight:bold; display:block; margin-bottom:0.3rem; }
        input, textarea { width:100%; padding:0.5rem; box-sizing:border-box; }
        textarea { height:80px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:0.7rem; border-bottom:1px solid #ddd; font-size:0.88rem; }
        th { background:#0B1F3A; color:#fff; }
        .btn { padding:0.4rem 0.9rem; border:none; border-radius:4px; cursor:pointer; font-size:0.82rem; text-decoration:none; }
        .btn-primary { background:#0B1F3A; color:#fff; }
        .btn-danger  { background:#c0392b; color:#fff; }
        .alert { padding:0.8rem; background:#d4edda; color:#155724; border-radius:4px; margin-bottom:1rem; }
        .past { color:#aaa; }
    </style>
</head>
<body>
<main>
    <p><a href="<?php echo BASE_URL; ?>admin/dashboard.php">← Back to Dashboard</a></p>
    <h1>Manage Events</h1>

    <?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

    <!-- ADD EVENT FORM -->
    <div class="card">
        <h2>Add New Event</h2>
        <form method="POST">
            <div class="form-group">
                <label>Event Title *</label>
                <input type="text" name="title" placeholder="e.g. Sports Day 2026" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Event Date *</label>
                    <input type="date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time">
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="end_time">
                </div>
            </div>
            <div class="form-group">
                <label>Location / Venue</label>
                <input type="text" name="location" placeholder="e.g. School Main Hall">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Full description of the event..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Event</button>
        </form>
    </div>

    <!-- EVENTS TABLE -->
    <div class="card">
        <h2>All Events (<?php echo count($allEvents); ?>)</h2>
        <?php if ($allEvents): ?>
        <table>
            <tr><th>Title</th><th>Date</th><th>Time</th><th>Location</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($allEvents as $ev): ?>
            <?php $isPast = strtotime($ev['event_date']) < strtotime('today'); ?>
            <tr class="<?php echo $isPast ? 'past' : ''; ?>">
                <td><?php echo htmlspecialchars($ev['title']); ?></td>
                <td><?php echo date('d M Y', strtotime($ev['event_date'])); ?></td>
                <td><?php echo $ev['start_time'] ? date('H:i', strtotime($ev['start_time'])) : '—'; ?></td>
                <td><?php echo htmlspecialchars($ev['location'] ?? '—'); ?></td>
                <td><?php echo $isPast ? '<span style="color:#aaa;">Past</span>' : '<span style="color:green;">Upcoming</span>'; ?></td>
                <td>
                    <a href="<?php echo BASE_URL; ?>admin/manage-events.php?delete=<?php echo $ev['id']; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Delete this event?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>No events yet.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
