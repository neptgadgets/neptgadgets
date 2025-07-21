<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
check_csrf();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    echo '<p>Product not found.</p>';
    exit;
}
$product_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$message_sent = false;
if (is_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if ($msg) {
        $stmt = $pdo->prepare('INSERT INTO messages (user_id, product_id, message) VALUES (?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $product['id'], $msg]);
        $message_sent = true;
    }
}
$user_msgs = [];
if (is_logged_in()) {
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE user_id = ? AND product_id = ? ORDER BY created_at DESC');
    $stmt->execute([$_SESSION['user_id'], $product['id']]);
    $user_msgs = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container d-flex justify-content-between align-items-center">
        <h1>NEPT GADGETS</h1>
        <div>
            <a href="https://wa.me/<?= $whatsapp_number ?>" class="icon-btn whatsapp" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="https://t.me/<?= $telegram_username ?>" class="icon-btn telegram" target="_blank"><i class="fab fa-telegram"></i> Telegram</a>
        </div>
    </div>
</header>
<nav class="container d-flex justify-content-between align-items-center my-3">
    <a href="/public/catalog.php" class="btn">Back to Catalog</a>
    <div>
        <?php if (is_logged_in()): ?>
            <?php if (is_admin()): ?>
                <a href="/admin/dashboard.php" class="btn btn-warning">Admin</a>
            <?php endif; ?>
            <span class="me-2">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="/public/logout.php" class="btn btn-danger">Logout</a>
        <?php else: ?>
            <a href="/public/login.php" class="btn">Login</a>
            <a href="/public/register.php" class="btn">Register</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">
    <div class="row">
        <div class="col-md-6">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%;max-width:400px;object-fit:cover;">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <h4 class="mb-3">$<?= number_format($product['price'],2) ?></h4>
            <?php if (is_logged_in()): ?>
                <a href="https://wa.me/<?= $whatsapp_number ?>?text=I'm%20interested%20in%20<?= urlencode($product['name']) ?>" class="btn icon-btn whatsapp mb-2" target="_blank"><i class="fab fa-whatsapp"></i> Message on WhatsApp</a><br>
                <a href="https://t.me/share/url?url=<?= urlencode($product_url) ?>&text=I'm%20interested%20in%20<?= urlencode($product['name']) ?>" class="btn icon-btn telegram" target="_blank"><i class="fab fa-telegram"></i> Message on Telegram</a>
                <hr>
                <h5>Send Inquiry</h5>
                <?php if ($message_sent): ?><div class="alert alert-success">Message sent!</div><?php endif; ?>
                <form method="post">
                    <?= csrf_field() ?>
                    <textarea name="message" class="form-control mb-2" placeholder="Type your question..." required></textarea>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
                <?php if ($user_msgs): ?>
                    <h6 class="mt-3">Your Previous Messages</h6>
                    <ul class="list-group">
                        <?php foreach ($user_msgs as $um): ?>
                            <li class="list-group-item small"><strong><?= $um['created_at'] ?>:</strong> <?= htmlspecialchars($um['message']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <p><a href="/public/login.php" class="btn btn-primary">Login to inquire</a></p>
            <?php endif; ?>
        </div>
    </div>
</main>
<footer class="mt-5">
    <div class="container text-center">
        &copy; <?= date('Y') ?> NEPT GADGETS – Phone and Accessories. All rights reserved.
    </div>
</footer>
</body>
</html>