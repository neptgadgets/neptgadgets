<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
session_timeout();

$step = 1;
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['find'])) {
        $username = trim($_POST['username']);
        $contact = trim($_POST['contact']);
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username=? AND contact=?');
        $stmt->execute([$username, $contact]);
        $user = $stmt->fetch();
        if ($user) {
            $step = 2;
            $user_id = $user['id'];
        } else {
            $error = 'User not found or contact does not match.';
        }
    } elseif (isset($_POST['resetpw'])) {
        $user_id = intval($_POST['user_id']);
        $newpw = $_POST['newpw'];
        if (strlen($newpw) >= 4) {
            $hash = password_hash($newpw, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?');
            $stmt->execute([$hash, $user_id]);
            $success = 'Password reset. You can now <a href="/public/login.php">login</a>.';
            $step = 3;
        } else {
            $error = 'New password too short.';
            $step = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="container d-flex justify-content-between align-items-center my-3">
    <a href="/public/login.php" class="btn">Login</a>
    <a href="/public/register.php" class="btn">Register</a>
</nav>
<main class="container" style="max-width:400px;">
    <h2>Forgot Password</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($step === 1): ?>
    <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact (as registered)</label>
            <input type="text" name="contact" class="form-control" required>
        </div>
        <button type="submit" name="find" class="btn btn-primary">Find Account</button>
    </form>
    <?php elseif ($step === 2): ?>
    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="user_id" value="<?= $user_id ?>">
        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="newpw" class="form-control" required>
        </div>
        <button type="submit" name="resetpw" class="btn btn-warning">Reset Password</button>
    </form>
    <?php endif; ?>
</main>
</body>
</html>