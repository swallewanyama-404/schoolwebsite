<?php
// ADMIN LOGIN page
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email']);
    $password = $_POST['password']; // NOT cleaned — password_verify handles this

    $admin = loginAdmin($pdo, $email, $password);

    if ($admin) {
        $_SESSION['admin_id']    = $admin['id'];
        $_SESSION['admin_name']  = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role']  = $admin['role'];
        header('Location: ' . BASE_URL . 'admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login &mdash; St. Mary's School</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body style="background:linear-gradient(135deg,var(--navy),var(--navy-light)); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1.5rem;">
<main style="max-width:400px; width:100%;">
    <div class="form-card" style="text-align:center;">
        <div class="footer-badge" style="margin:0 auto 1.25rem; width:64px; height:64px;">
            <span style="font-family:'Merriweather',serif;font-size:.85rem;font-weight:900;color:#fff;">SM</span>
        </div>
        <h1 style="font-size:1.4rem;margin-bottom:.25rem;">Admin Portal</h1>
        <p style="margin-bottom:1.5rem;">St. Mary's School management login</p>

        <?php if ($error): ?>
            <div class="alert alert-error" style="text-align:left;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" style="text-align:left;">
            <div class="form-group">
                <label>Email Address</label>
                <input class="form-control" type="email" name="email" placeholder="admin@stmarys.ac.ug" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input class="form-control" type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Sign In &rarr;</button>
        </form>
        <p style="margin-top:1.5rem;font-size:.82rem;"><a href="<?php echo BASE_URL; ?>index.php">&larr; Back to Website</a></p>
    </div>
</main>
</body>
</html>
