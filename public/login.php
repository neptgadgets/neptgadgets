<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
check_csrf();

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: /public/login.php?timeout=1');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: /public/index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="container d-flex justify-content-between align-items-center my-3">
    <a href="/public/index.php" class="btn">Home</a>
    <div>
        <a href="/public/register.php" class="btn">Register</a>
    </div>
</nav>
<main class="container" style="max-width:400px;margin-top:3rem;">
    <h2>Login</h2>
    <?php if (isset($_GET['registered'])): ?><div class="alert alert-success">Registration successful. Please login.</div><?php endif; ?>
    <?php if (isset($_GET['timeout'])): ?><div class="alert alert-warning">You were logged out due to inactivity. Please login again.</div><?php endif; ?>
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
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="/public/register.php" class="btn btn-link">Register</a>
    </form>
</main>
</body>
</html>