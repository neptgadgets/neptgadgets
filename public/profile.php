<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
session_timeout();
require_login();

$user_id = current_user_id();
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['update'])) {
        $contact = trim($_POST['contact']);
        $stmt = $pdo->prepare('UPDATE users SET contact=? WHERE id=?');
        $stmt->execute([$contact, $user_id]);
        $success = 'Profile updated.';
    }
    if (isset($_POST['changepw'])) {
        $oldpw = $_POST['oldpw'];
        $newpw = $_POST['newpw'];
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id=?');
        $stmt->execute([$user_id]);
        $hash = $stmt->fetchColumn();
        if (password_verify($oldpw, $hash) && strlen($newpw) >= 4) {
            $stmt = $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?');
            $stmt->execute([password_hash($newpw, PASSWORD_BCRYPT), $user_id]);
            $success = 'Password changed.';
        } else {
            $error = 'Old password incorrect or new password too short.';
        }
    }
}
$stmt = $pdo->prepare('SELECT username, contact FROM users WHERE id=?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="container d-flex justify-content-between align-items-center my-3">
    <a href="/public/index.php" class="btn">Home</a>
    <a href="/public/logout.php" class="btn btn-danger">Logout</a>
</nav>
<main class="container" style="max-width:500px;">
    <h2>My Profile</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="post" class="mb-4">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($user['contact']) ?>">
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
    </form>
    <h4>Change Password</h4>
    <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>Old Password</label>
            <input type="password" name="oldpw" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="newpw" class="form-control" required>
        </div>
        <button type="submit" name="changepw" class="btn btn-warning">Change Password</button>
    </form>
</main>
</body>
</html>