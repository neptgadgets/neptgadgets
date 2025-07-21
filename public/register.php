<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
check_csrf();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $contact = trim($_POST['contact']);
    if (strlen($username) < 3 || strlen($password) < 4) {
        $error = 'Username or password too short.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, contact, role) VALUES (?, ?, ?, "user")');
            $stmt->execute([$username, $hash, $contact]);
            header('Location: /public/login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="container d-flex justify-content-between align-items-center my-3">
    <a href="/public/index.php" class="btn">Home</a>
    <div>
        <a href="/public/login.php" class="btn">Login</a>
    </div>
</nav>
<main class="container" style="max-width:400px;margin-top:3rem;">
    <h2>Register</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact (optional)</label>
            <input type="text" name="contact" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="/public/login.php" class="btn btn-link">Login</a>
    </form>
</main>
</body>
</html>