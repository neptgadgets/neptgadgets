<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    echo '<p>Product not found.</p>';
    exit;
}
$product_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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