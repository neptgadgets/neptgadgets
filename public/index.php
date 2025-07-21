<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

// Fetch featured and new/hot products
$featured = $pdo->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 4")->fetchAll();
$new_arrivals = $pdo->query("SELECT * FROM products WHERE is_new = 1 OR is_hot = 1 ORDER BY created_at DESC LIMIT 4")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEPT GADGETS – Phone and Accessories</title>
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
    <a href="/public/catalog.php" class="btn">Shop Now</a>
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
    <section class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2>Welcome to NEPT GADGETS</h2>
                <p>Your trusted shop in Entebbe for the latest mobile phones, accessories, and electronics. Quality products, great prices, and fast customer support!</p>
                <p><strong>Location:</strong> <?= $business_location ?> | <strong>Contact:</strong> <?= $business_contact ?></p>
            </div>
            <div class="col-md-5 text-center">
                <img src="/assets/hero-gadgets.png" alt="Gadgets" style="max-width:90%;height:auto;">
            </div>
        </div>
    </section>
    <section class="mb-5">
        <h3>Featured Gadgets</h3>
        <div class="row">
            <?php foreach ($featured as $product): ?>
                <div class="col-md-3 col-6 mb-3">
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%;height:150px;object-fit:cover;">
                        <h5><?= htmlspecialchars($product['name']) ?></h5>
                        <p><strong>$<?= number_format($product['price'],2) ?></strong></p>
                        <a href="/public/product.php?id=<?= $product['id'] ?>" class="btn btn-sm">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section>
        <h3>New Arrivals & Hot Deals</h3>
        <div class="row">
            <?php foreach ($new_arrivals as $product): ?>
                <div class="col-md-3 col-6 mb-3">
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%;height:150px;object-fit:cover;">
                        <h5><?= htmlspecialchars($product['name']) ?></h5>
                        <p><strong>$<?= number_format($product['price'],2) ?></strong></p>
                        <a href="/public/product.php?id=<?= $product['id'] ?>" class="btn btn-sm">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<footer class="mt-5">
    <div class="container text-center">
        &copy; <?= date('Y') ?> NEPT GADGETS – Phone and Accessories. All rights reserved.
    </div>
</footer>
</body>
</html>