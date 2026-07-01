<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdminLogin();
$success= '';

// UPDATE STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enquiry_id'])) {
    $id     = (int)$_POST['enquiry_id'];
    $status = clean($_POST['status']);
    $notes  = clean($_POST['admin_notes']);
    $send_reply  = isset($_POST['send_reply']) ? true : false;
    $reply_text  = clean($_POST['reply_text'] ?? '');
    $parent_email= clean($_POST['parent_email'] ?? '');
    $parent_name = clean($_POST['parent_name'] ?? '');
    $student_name= clean($_POST['student_name'] ?? '');
    updateEnquiryStatus($pdo, $id, $status, $notes);
    // Log the action
    logAction($pdo, $_SESSION['admin_id'], 'updated_enquiry', 'admissions_enquiries', $id,
              "Status changed to: $status for student: $student_name");

    // Send email reply if requested and email exists
    if ($send_reply && !empty($parent_email) && !empty($reply_text)) {
        $school_name  = getSetting($pdo, 'school_name');
        $school_email = getSetting($pdo, 'school_email');
        $school_phone = getSetting($pdo, 'school_phone');

        // Build subject based on status
        $subjects = [
            'contacted' => 'Update on Your Admissions Enquiry — ' . $school_name,
            'enrolled'  => 'Congratulations! Admission Approved — ' . $school_name,
            'declined'  => 'Admissions Enquiry Update — ' . $school_name,
            'new'       => 'Admissions Enquiry Received — ' . $school_name,
        ];
        $email_subject = $subjects[$status] ?? 'Admissions Update — ' . $school_name;

        $headers  = "From: $school_email\r\n";
        $headers .= "Reply-To: $school_email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $body  = "Dear $parent_name,\n\n";
        $body .= "Thank you for your enquiry regarding admission for $student_name.\n\n";
        $body .= $reply_text . "\n\n";
        $body .= "---\n";
        $body .= "$school_name\n";
        $body .= "Phone: $school_phone\n";
        $body .= "Email: $school_email";

        @mail($parent_email, $email_subject, $body, $headers);

        $success = "Status updated to '" . strtoupper($status) . "' and reply sent to $parent_email.";
    } else {
        $success = "Enquiry status updated to '" . strtoupper($status) . "'.";}
    header('Location: ' . BASE_URL . 'admin/manage-enquiries.php');
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    deleteEnquiry($pdo, (int)$_GET['delete']);
    header('Location: '. BASE_URL . 'admin/manage-enquiries.php');
    exit;
}

$enquiries = getAllEnquiries($pdo);
$statuses  = [
    'new'       => ['color' => '#e74c3c', 'label' => 'New'],
    'contacted' => ['color' => '#f0a500', 'label' => 'Contacted'],
    'enrolled'  => ['color' => '#27ae60', 'label' => 'Enrolled'],
    'declined'  => ['color' => '#95a5a6', 'label' => 'Declined'],
];

// Count by status
$counts = ['new' => 0, 'contacted' => 0, 'enrolled' => 0, 'declined' => 0];
foreach ($enquiries as $e) { $counts[$e['status']]++; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Enquiries</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { font-family:Arial,sans-serif; background:#f4f4f4; }
        main { max-width:1300px; margin:2rem auto; padding:0 1rem; }
        .stats { display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
        .stat { flex:1; min-width:120px; background:#fff; padding:1rem; border-radius:8px; text-align:center; box-shadow:0 1px 4px rgba(0,0,0,0.08); }
        .stat .num { font-size:1.8rem; font-weight:bold; }
        .stat .lbl { font-size:0.82rem; color:#666; }
        table { width:100%; border-collapse:collapse; background:#fff; }
        th, td { padding:0.6rem 0.7rem; border-bottom:1px solid #ddd; font-size:0.83rem; vertical-align:top; }
        th { background:#0B1F3A; color:#fff; }
        .badge { padding:0.2rem 0.6rem; border-radius:8px; color:#fff; font-size:0.75rem; font-weight:bold; }
        .btn { padding:0.35rem 0.8rem; border:none; border-radius:4px; cursor:pointer; font-size:0.8rem; text-decoration:none; display:inline-block; margin:0.15rem 0; }
        .btn-primary { background:#0B1F3A; color:#fff; }
        .btn-success { background:#27ae60; color:#fff; }
        .btn-danger  { background:#c0392b; color:#fff; }
        .btn-warning { background:#f0a500; color:#000; }
        .card { background:#fff; padding:1.5rem; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); }
        .alert-success { padding:0.8rem; background:#d4edda; color:#155724; border-radius:4px; margin-bottom:1rem; }

        /* Action modal */
        .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:flex-start; padding-top:3rem; }
        .modal-overlay.active { display:flex; }
        .modal { background:#fff; border-radius:8px; padding:2rem; width:90%; max-width:650px; position:relative; max-height:85vh; overflow-y:auto; }
        .modal h2 { margin-top:0; }
        .modal-close { position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.3rem; cursor:pointer; }
        .form-group { margin-bottom:1rem; }
        label { font-weight:bold; display:block; margin-bottom:0.3rem; }
        select, textarea, input[type=text] { width:100%; padding:0.5rem; box-sizing:border-box; }
        textarea { height:120px; }
        .info-box { background:#f0f4ff; border-left:4px solid #0B1F3A; padding:0.8rem 1rem; border-radius:4px; font-size:0.85rem; margin-bottom:1rem; }
        .info-box p { margin:0.2rem 0; }
        .status-enrolled { background:#f0fff4; }
        .status-declined { background:#fff5f5; }
        .status-contacted { background:#fffde7; }
    </style>
</head>
<body>
<main>
    <p><a href="<?php echo BASE_URL; ?>admin/dashboard.php">← Back to Dashboard</a></p>
    <h1>Admissions Enquiries (<?php echo count($enquiries); ?>)</h1>
     <?php if ($success): ?><div class="alert-success"><?php echo $success; ?></div><?php endif; ?>

    <!-- STATUS SUMMARY -->
    <div class="stats">
        <?php foreach ($statuses as $key => $s): ?>
        <div class="stat">
            <div class="num" style="color:<?php echo $s['color']; ?>;"><?php echo $counts[$key]; ?></div>
            <div class="lbl"><?php echo $s['label']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>


    
    <div class="card">
        <?php if ($enquiries): ?>
        <table>
            <tr>
                <th>#</th>
                <th>Parent</th>
                <th>Student</th>
                <th>Level</th>
                <th>School</th>
                <th>PLE</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($enquiries as $enq): ?>
            <?php
                $rowClass = '';
                if ($enq['status'] === 'enrolled')  $rowClass = 'status-enrolled';
                if ($enq['status'] === 'declined')  $rowClass = 'status-declined';
                if ($enq['status'] === 'contacted') $rowClass = 'status-contacted';
            ?>
            <tr class="<?php echo $rowClass; ?>">
                <td><?php echo $enq['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($enq['parent_name']); ?></strong><br>
                    <small>📞 <?php echo htmlspecialchars($enq['parent_phone']); ?></small><br>
                    <?php if (!empty($enq['parent_email'])): ?>
                        <small>✉️ <?php echo htmlspecialchars($enq['parent_email']); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($enq['student_name']); ?></td>
                <td><strong><?php echo $enq['entry_level']; ?></strong></td>
                <td><?php echo htmlspecialchars($enq['current_school'] ?? '—'); ?></td>
                <td><?php echo $enq['ple_aggregate'] ?? '—'; ?></td>
                <td><?php echo date('d M Y', strtotime($enq['created_at'])); ?></td>
                <td>
                    <span class="badge" style="background:<?php echo $statuses[$enq['status']]['color']; ?>;">
                        <?php echo strtoupper($enq['status']); ?>
                    </span>
                    <?php if (!empty($enq['admin_notes'])): ?>
                        <br><small style="color:#888; font-style:italic;"><?php echo htmlspecialchars(substr($enq['admin_notes'], 0, 40)); ?>...</small>
                    <?php endif; ?>
                </td>
                <td>
                    <!-- MANAGE BUTTON -->
                    <button class="btn btn-primary"
                        onclick="openManage(
                            <?php echo $enq['id']; ?>,
                            '<?php echo addslashes($enq['parent_name']); ?>',
                            '<?php echo addslashes($enq['parent_email'] ?? ''); ?>',
                            '<?php echo addslashes($enq['parent_phone']); ?>',
                            '<?php echo addslashes($enq['student_name']); ?>',
                            '<?php echo $enq['entry_level']; ?>',
                            '<?php echo $enq['status']; ?>',
                            '<?php echo addslashes($enq['admin_notes'] ?? ''); ?>'
                        )">
                        ⚙️ Manage
                    </button>

                    <a href="<?php echo BASE_URL; ?>admin/manage-enquiries.php?delete=<?php echo $enq['id']; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Delete this enquiry permanently?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>No enquiries received yet.</p>
        <?php endif; ?>
    </div>
</main>

<!-- MANAGE MODAL -->
<div class="modal-overlay" id="manageModal">
    <div class="modal">
        <button class="modal-close" onclick="closeManage()">✕</button>
        <h2>Manage Enquiry</h2>

        <!-- Enquiry Info Summary -->
        <div class="info-box" id="modal_info"></div>

        <form method="POST">
            <input type="hidden" name="enquiry_id"   id="modal_id">
            <input type="hidden" name="parent_email" id="modal_email">
            <input type="hidden" name="parent_name"  id="modal_pname">
            <input type="hidden" name="student_name" id="modal_sname">

            <!-- STATUS -->
            <div class="form-group">
                <label>Update Status *</label>
                <select name="status" id="modal_status" onchange="updateReplyTemplate()">
                    <option value="new">New</option>
                    <option value="contacted">Contacted — Following up</option>
                    <option value="enrolled">Enrolled — Approved ✅</option>
                    <option value="declined">Declined — Not accepted ❌</option>
                </select>
            </div>

            <!-- ADMIN NOTES -->
            <div class="form-group">
                <label>Internal Admin Notes (not sent to parent)</label>
                <textarea name="admin_notes" id="modal_notes" placeholder="Internal notes for your reference..."></textarea>
            </div>

            <!-- SEND REPLY TOGGLE -->
            <div class="form-group">
                <label>
                    <input type="checkbox" name="send_reply" id="send_reply_chk" onchange="toggleReply()" value="1">
                    Send email reply to parent
                </label>
                <small style="color:#888; display:block; margin-top:0.3rem;">
                    Only works if parent provided an email address.
                    On localhost this is recorded but email may not send — it will work on a live server.
                </small>
            </div>

            <!-- REPLY TEXT (shown when checkbox ticked) -->
            <div id="reply_section" style="display:none;">
                <div class="form-group">
                    <label>Reply Message to Parent</label>
                    <textarea name="reply_text" id="modal_reply"
                        placeholder="This will be emailed to the parent..."></textarea>
                    <small style="color:#888;">The school name, phone and email are auto-appended as a signature.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-success" style="padding:0.6rem 1.5rem; font-size:0.95rem;">
                Save & Send
            </button>
            <button type="button" class="btn" style="background:#ccc;" onclick="closeManage()">Cancel</button>
        </form>
    </div>
</div>
<script>
// Reply templates based on status
const templates = {
    new:       "Thank you for your enquiry. We have received your application and will be in touch shortly.",
    contacted: "Thank you for your interest in our school. We would like to schedule a meeting to discuss the admission of your child. Please contact us at your earliest convenience.",
    enrolled:  "We are pleased to inform you that your child has been accepted for admission. Please visit the school at your earliest convenience to complete the enrollment process and pay the required fees. Congratulations!",
    declined:  "Thank you for your interest in our school. After careful consideration, we regret to inform you that we are unable to offer admission at this time. We encourage you to apply again in the next intake. Thank you for understanding."
};

function openManage(id, pname, email, phone, sname, level, status, notes) {
    document.getElementById('modal_id').value     = id;
    document.getElementById('modal_email').value  = email;
    document.getElementById('modal_pname').value  = pname;
    document.getElementById('modal_sname').value  = sname;
    document.getElementById('modal_notes').value  = notes;
    document.getElementById('modal_status').value = status;

    document.getElementById('modal_info').innerHTML =
        '<p><strong>Parent:</strong> ' + pname + ' &nbsp;|&nbsp; 📞 ' + phone + (email ? ' &nbsp;|&nbsp; ✉️ ' + email : ' &nbsp;|&nbsp; <span style="color:#e74c3c;">No email provided</span>') + '</p>' +
        '<p><strong>Student:</strong> ' + sname + ' &nbsp;|&nbsp; <strong>Applying for:</strong> ' + level + '</p>';

    updateReplyTemplate();
    document.getElementById('manageModal').classList.add('active');
}

function closeManage() {
    document.getElementById('manageModal').classList.remove('active');
    document.getElementById('send_reply_chk').checked = false;
    document.getElementById('reply_section').style.display = 'none';
}

function toggleReply() {
    const show = document.getElementById('send_reply_chk').checked;
    document.getElementById('reply_section').style.display = show ? 'block' : 'none';
}

function updateReplyTemplate() {
    const status = document.getElementById('modal_status').value;
    document.getElementById('modal_reply').value = templates[status] || '';
}

document.getElementById('manageModal').addEventListener('click', function(e) {
    if (e.target === this) closeManage();
});
</script>
</body>
</html>
