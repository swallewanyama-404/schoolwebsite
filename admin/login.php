<?php
// ============================================================
//  admin/login.php — Admin Login Page
//  Open at: http://localhost/school-website/admin/login.php
// ============================================================
session_start();

// Already logged in? Go straight to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']    ?? '');
    $pass  =       $_POST['password'] ?? '';   // raw — checked with password_verify()

    // Fetch active admin by email
    $stmt = $pdo->prepare(
        'SELECT * FROM admin_users WHERE email = ? AND is_active = 1 LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify password hash
    if ($user && password_verify($pass, $user['password'])) {
        // Set session
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_role'] = $user['role'];

        // Update last_login timestamp
        $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')
            ->execute([$user['id']]);

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/school-website/assets/css/style.css">
    <style>
        body { background: var(--off); display: flex; align-items: center;
               justify-content: center; min-height: 100vh; }
        .login-box {
            background: var(--white); border-radius: var(--radius-lg);
            box-shadow: var(--shadow); padding: 2.5rem; width: 100%; max-width: 380px;
        }
        .login-logo { text-align: center; margin-bottom: 1.75rem; }
        .login-logo .logo-name { font-size: 1.25rem; color: var(--navy); font-weight: 700; }
        .login-logo small      { display: block; color: var(--muted); font-size: .85rem; }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-logo">
        <div class="logo-name">Admin Panel</div>
        <small>School Website Management</small>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   placeholder="admin@school.ug"
                   required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Your password" required>
        </div>

        <button type="submit" class="btn btn-blue" style="width:100%;margin-top:.5rem">
            Login to Admin Panel
        </button>
    </form>

    <p style="text-align:center;margin-top:1.25rem;font-size:.875rem">
        <a href="/school-website/">← Back to website</a>
    </p>
</div>

</body>
</html>
