<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
$success = '';
$error = '';

requireAdminLogin();
$sucess = '';

// DELETE
if (isset($_GET['delete'])) {
    deleteContact($pdo, (int)$_GET['delete']);
    header('Location: ' . BASE_URL . 'admin/messages.php');
    exit;

}

// MARK AS READ
if (isset($_GET['read'])) {
    markMessageRead($pdo, (int)$_GET['read']);
    header('Location: ' . BASE_URL . 'admin/messages.php');
    exit;
}
// REPLY TO MESSAGE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $id           = (int)$_POST['message_id'];
    $reply_text   = clean($_POST['reply_text']);
    $sender_email = clean($_POST['sender_email']);
    $sender_name  = clean($_POST['sender_name']);
    $subject      = clean($_POST['reply_subject']);

    // Save reply as sent and mark message as read
    $stmt = $pdo->prepare(
        'UPDATE contact_messages SET is_read = 1, replied_at = NOW() WHERE id = ?'
    );
    $stmt->execute([$id]);

    // Send email using PHP mail()
    $to      = $sender_email;
    $subj    = 'Re: ' . $subject;
    $headers = "From: " . getSetting($pdo, 'school_email') . "\r\n";
    $headers .= "Reply-To: " . getSetting($pdo, 'school_email') . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body  = "Dear " . $sender_name . ",\n\n";
    $body .= $reply_text . "\n\n";
    $body .= "---\n";
    $body .= getSetting($pdo, 'school_name') . "\n";
    $body .= getSetting($pdo, 'school_phone') . "\n";
    $body .= getSetting($pdo, 'school_email');

    // Log the action
    logAction($pdo, $_SESSION['admin_id'], 'replied_message', 'contact_messages', $id,
              "Replied to message from $sender_name ($sender_email)");

    // mail() works on live server — on localhost it may not send
    // but the replied_at timestamp is saved either way
    @mail($to, $subj, $body, $headers);

    $success = "Reply recorded. Message marked as replied.";
}

$contacts     = getAllContacts($pdo);
$unread_count = getUnreadCount($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Messages</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
body { font-family:Arial,sans-serif; background:#f4f4f4; }
        main { max-width:1200px; margin:2rem auto; padding:0 1rem; }
        table { width:100%; border-collapse:collapse; background:#fff; }
        th, td { padding:0.7rem; border-bottom:1px solid #ddd; font-size:0.85rem; vertical-align:top; }
        th { background:#0B1F3A; color:#fff; }
        tr.unread { background:#fffde7; }
        tr.replied { background:#f0fff4; }
        .btn { padding:0.35rem 0.8rem; border:none; border-radius:4px; cursor:pointer; font-size:0.8rem; text-decoration:none; display:inline-block; margin:0.2rem 0; }
        .btn-primary { background:#0B1F3A; color:#fff; }
        .btn-success { background:#27ae60; color:#fff; }
        .btn-danger  { background:#c0392b; color:#fff; }
        .btn-warning { background:#f0a500; color:#000; }
        .badge { padding:0.2rem 0.6rem; border-radius:10px; color:#fff; font-size:0.75rem; font-weight:bold; }
        .card { background:#fff; padding:1.5rem; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); margin-bottom:1.5rem; }
        .alert-success { padding:0.8rem; background:#d4edda; color:#155724; border-radius:4px; margin-bottom:1rem; }
        .alert-error   { padding:0.8rem; background:#f8d7da; color:#721c24; border-radius:4px; margin-bottom:1rem; }

        /* Reply modal */
        .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center; }
        .modal-overlay.active { display:flex; }
        .modal { background:#fff; border-radius:8px; padding:2rem; width:90%; max-width:600px; position:relative; }
        .modal h2 { margin-top:0; }
        .modal textarea { width:100%; height:150px; padding:0.5rem; box-sizing:border-box; margin-top:0.3rem; }
        .modal input  { width:100%; padding:0.5rem; box-sizing:border-box; margin-top:0.3rem; }
        .modal-close { position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.3rem; cursor:pointer; color:#555; }
        .form-group { margin-bottom:1rem; }
        label { font-weight:bold; display:block; }
    </style>
</head>
<body>
<main>
    <p><a href="<?php echo BASE_URL; ?>admin/dashboard.php">← Back to Dashboard</a></p>
    <h1>Contact Messages
        <?php if ($unread_count > 0): ?>
            <span class="badge"><?php echo $unread_count; ?> unread</span>
        <?php endif; ?>
    </h1>
 <?php if ($success): ?><div class="alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert-error"><?php echo $error; ?></div><?php endif; ?>

    <div class="card">
        <?php if ($contacts): ?>
        <table>
            <tr>
                <th>#</th>
                <th>From</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($contacts as $msg): ?>
            <?php
                $rowClass = '';
                if ($msg['replied_at'])  $rowClass = 'replied';
                elseif (!$msg['is_read']) $rowClass = 'unread';
            ?>
            <tr class="<?php echo $rowClass; ?>">
                <td><?php echo $msg['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong><br>
                    <small><?php echo htmlspecialchars($msg['email']); ?></small>
                </td>
                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                <td style="max-width:180px;">
                    <?php echo htmlspecialchars(substr($msg['message'], 0, 100)); ?>
                    <?php if (strlen($msg['message']) > 100): ?>...<?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($msg['phone'] ?? '—'); ?></td>
                <td><?php echo date('d M Y', strtotime($msg['created_at'])); ?></td>
                <td>
                    <?php if ($msg['replied_at']): ?>
                        <span class="badge" style="background:#27ae60;">Replied</span><br>
                        <small style="color:#888;"><?php echo date('d M', strtotime($msg['replied_at'])); ?></small>
                    <?php elseif ($msg['is_read']): ?>
                        <span class="badge" style="background:#607080;">Read</span>
                    <?php else: ?>
                        <span class="badge" style="background:#e74c3c;">Unread</span>
                    <?php endif; ?>
                </td>
                <td>
                    <!-- REPLY BUTTON -->
                    <button class="btn btn-success"
                        onclick="openReply(
                            <?php echo $msg['id']; ?>,
                            '<?php echo addslashes(htmlspecialchars($msg['name'])); ?>',
                            '<?php echo addslashes(htmlspecialchars($msg['email'])); ?>',
                            '<?php echo addslashes(htmlspecialchars($msg['subject'])); ?>',
                            '<?php echo addslashes(htmlspecialchars($msg['message'])); ?>'
                        )">
                        ✉️ Reply
                    </button>

                    <?php if (!$msg['is_read']): ?>
                        <a href="<?php echo BASE_URL; ?>admin/messages.php?read=<?php echo $msg['id']; ?>" class="btn btn-warning">Mark Read</a>
                    <?php endif; ?>

                    <a href="<?php echo BASE_URL; ?>admin/messages.php?delete=<?php echo $msg['id']; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Delete this message permanently?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>No messages yet.</p>
        <?php endif; ?>
    </div>
</main>

<!-- REPLY MODAL -->
<div class="modal-overlay" id="replyModal">
    <div class="modal">
        <button class="modal-close" onclick="closeReply()">✕</button>
        <h2>Reply to Message</h2>

        <form method="POST">
            <input type="hidden" name="message_id"    id="modal_id">
            <input type="hidden" name="sender_email"  id="modal_email">
            <input type="hidden" name="sender_name"   id="modal_name">

            <div class="form-group">
                <label>To</label>
                <input type="text" id="modal_display_email" disabled style="background:#eee;">
            </div>

            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="reply_subject" id="modal_subject">
            </div>

            <div class="form-group">
                <label>Original Message</label>
                <div id="modal_original" style="background:#f9f9f9; padding:0.8rem; border-radius:4px; font-size:0.85rem; color:#555; margin-top:0.3rem; border-left:3px solid #ddd;">
                </div>
            </div>

            <div class="form-group">
                <label>Your Reply *</label>
                <textarea name="reply_text" placeholder="Type your reply here..." required></textarea>
            </div>

            <button type="submit" class="btn btn-success" style="padding:0.6rem 1.5rem; font-size:0.95rem;">
                Send Reply
            </button>
            <button type="button" class="btn" style="background:#ccc;" onclick="closeReply()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openReply(id, name, email, subject, message) {
    document.getElementById('modal_id').value            = id;
    document.getElementById('modal_email').value         = email;
    document.getElementById('modal_name').value          = name;
    document.getElementById('modal_subject').value       = subject;
    document.getElementById('modal_display_email').value = name + ' <' + email + '>';
    document.getElementById('modal_original').innerText  = message;
    document.getElementById('replyModal').classList.add('active');
}

function closeReply() {
    document.getElementById('replyModal').classList.remove('active');
}

// Close modal if clicking outside
document.getElementById('replyModal').addEventListener('click', function(e) {
    if (e.target === this) closeReply();
});
</script>
</body>
</html>