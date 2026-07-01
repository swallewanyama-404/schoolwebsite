<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdminLogin();

$message = '';

// DELETE
if (isset($_GET['delete'])) {
    deleteStaff($pdo, (int)$_GET['delete']);
    header('Location: ' . BASE_URL . 'admin/manage-staff.php');
    exit;
}

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['staff_id'])) {
    $dept_id       = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
    $first_name    = clean($_POST['first_name']);
    $last_name     = clean($_POST['last_name']);
    $title         = clean($_POST['title']);
    $role          = clean($_POST['role']);
    $subjects      = clean($_POST['subjects']);
    $qualification = clean($_POST['qualification']);
    $bio           = clean($_POST['bio']);
    $photo         = clean($_POST['photo']);
    $email         = clean($_POST['email']);
    $is_management = isset($_POST['is_management']) ? 1 : 0;
    $sort_order    = (int)$_POST['sort_order'];

    createStaff($pdo, $dept_id, $first_name, $last_name, $title, $role,
                $subjects, $qualification, $bio, $photo, $email, $is_management, $sort_order);
    $message = 'Staff member added successfully.';
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff_id'])) {
    $id         = (int)$_POST['staff_id'];
    $dept_id    = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
    $first_name = clean($_POST['first_name']);
    $last_name  = clean($_POST['last_name']);
    $title      = clean($_POST['title']);
    $role       = clean($_POST['role']);
    $subjects   = clean($_POST['subjects']);
    $bio        = clean($_POST['bio']);
    $photo      = clean($_POST['photo']);
    $is_active  = isset($_POST['is_active']) ? 1 : 0;

    updateStaff($pdo, $id, $dept_id, $first_name, $last_name, $title, $role, $subjects, $bio, $photo, $is_active);
    $message = 'Staff member updated.';
}

$allStaff    = getAllStaff($pdo);
$departments = getAllDepartments($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { font-family:Arial,sans-serif; background:#f4f4f4; }
        main { max-width:1100px; margin:2rem auto; padding:0 1rem; }
        .card { background:#fff; padding:1.5rem; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); margin-bottom:2rem; }
        .form-row { display:flex; gap:1rem; flex-wrap:wrap; }
        .form-group { flex:1; min-width:180px; margin-bottom:1rem; }
        label { font-weight:bold; display:block; margin-bottom:0.3rem; }
        input, select, textarea { width:100%; padding:0.5rem; box-sizing:border-box; }
        textarea { height:80px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:0.6rem 0.8rem; border-bottom:1px solid #ddd; font-size:0.85rem; }
        th { background:#0B1F3A; color:#fff; }
        .btn { padding:0.4rem 0.9rem; border:none; border-radius:4px; cursor:pointer; font-size:0.82rem; text-decoration:none; }
        .btn-primary { background:#0B1F3A; color:#fff; }
        .btn-danger  { background:#c0392b; color:#fff; }
        .alert { padding:0.8rem; background:#d4edda; color:#155724; border-radius:4px; margin-bottom:1rem; }
    </style>
</head>
<body>
<main>
    <p><a href="<?php echo BASE_URL; ?>admin/dashboard.php">← Back to Dashboard</a></p>
    <h1>Manage Staff</h1>

    <?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

    <!-- ADD STAFF FORM -->
    <div class="card">
        <h2>Add New Staff Member</h2>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Title</label>
                    <select name="title">
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                        <option value="Dr">Dr</option>
                        <option value="Prof">Prof</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Job Role / Position *</label>
                    <input type="text" name="role" placeholder="e.g. Head of Sciences" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id">
                        <option value="">-- Select Department --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Subjects Taught</label>
                    <input type="text" name="subjects" placeholder="e.g. Physics, Mathematics">
                </div>
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="qualification" placeholder="e.g. B.Ed Physics, Makerere">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Photo Path</label>
                    <input type="text" name="photo" placeholder="assets/images/staff/name.jpg">
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="0">
                </div>
            </div>
            <div class="form-group">
                <label>Bio / About</label>
                <textarea name="bio" placeholder="Brief biography..."></textarea>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_management" value="1">
                    Show in Leadership / Management section
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Add Staff Member</button>
        </form>
    </div>

    <!-- STAFF TABLE -->
    <div class="card">
        <h2>All Staff (<?php echo count($allStaff); ?>)</h2>
        <?php if ($allStaff): ?>
        <table>
            <tr>
                <th>Name</th><th>Role</th><th>Department</th><th>Subjects</th><th>Management</th><th>Actions</th>
            </tr>
            <?php foreach ($allStaff as $s): ?>
            <tr>
                <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                <td><?php echo htmlspecialchars($s['role']); ?></td>
                <td><?php echo htmlspecialchars($s['department_name'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars($s['subjects'] ?? '—'); ?></td>
                <td><?php echo $s['is_management'] ? '✅ Yes' : 'No'; ?></td>
                <td>
                    <a href="<?php echo BASE_URL; ?>admin/manage-staff.php?delete=<?php echo $s['id']; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Delete this staff member?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>No staff added yet.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
