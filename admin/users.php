<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
session_timeout();
require_admin();
check_csrf();

// Handle add/delete
if (isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $contact = trim($_POST['contact']);
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role, contact) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $hash, $role, $contact]);
}
if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
    $stmt->execute([$id]);
}
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $contact = trim($_POST['contact']);
    $role = $_POST['role'];
    $stmt = $pdo->prepare('UPDATE users SET username=?, contact=?, role=? WHERE id=?');
    $stmt->execute([$username, $contact, $role, $id]);
}
if (isset($_POST['resetpw'])) {
    $id = intval($_POST['id']);
    $newpw = $_POST['newpw'];
    if (strlen($newpw) >= 4) {
        $hash = password_hash($newpw, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?');
        $stmt->execute([$hash, $id]);
    }
}
$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header><div class="container"><h1>Manage Users</h1></div></header>
<main class="container mt-4">
    <h3>Add User</h3>
    <form method="post" class="mb-4 row g-3">
        <?= csrf_field() ?>
        <div class="col-md-3"><input name="username" class="form-control" placeholder="Username" required></div>
        <div class="col-md-3"><input name="password" class="form-control" placeholder="Password" type="password" required></div>
        <div class="col-md-3"><input name="contact" class="form-control" placeholder="Contact"></div>
        <div class="col-md-2"><select name="role" class="form-control"><option value="user">User</option><option value="admin">Admin</option></select></div>
        <div class="col-md-1"><button type="submit" name="add" class="btn btn-success">Add</button></div>
    </form>
    <h3>All Users</h3>
    <table class="table table-bordered table-striped">
        <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Contact</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= htmlspecialchars($u['contact']) ?></td>
                <td><?= $u['created_at'] ?></td>
                <td>
                    <form method="post" class="d-inline-flex gap-1 align-items-center">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <input name="username" value="<?= htmlspecialchars($u['username']) ?>" class="form-control form-control-sm" style="width:100px;">
                        <input name="contact" value="<?= htmlspecialchars($u['contact']) ?>" class="form-control form-control-sm" style="width:120px;">
                        <select name="role" class="form-control form-control-sm" style="width:80px;"><option value="user" <?= $u['role']=='user'?'selected':'' ?>>User</option><option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option></select>
                        <button type="submit" name="edit" class="btn btn-sm btn-primary">Save</button>
                    </form>
                    <form method="post" class="d-inline-flex gap-1 align-items-center">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <input name="newpw" type="password" class="form-control form-control-sm" placeholder="New password" style="width:120px;">
                        <button type="submit" name="resetpw" class="btn btn-sm btn-warning">Reset PW</button>
                    </form>
                    <?php if ($u['role'] !== 'admin'): ?>
                    <form method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/admin/dashboard.php" class="btn btn-link">Back to Dashboard</a>
</main>
</body>
</html>