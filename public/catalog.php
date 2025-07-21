<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$category_id]);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog – NEPT GADGETS</title>
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
    <h2>Product Catalog</h2>
    <form method="get" class="mb-3">
        <label for="category">Category:</label>
        <select name="category" id="category" onchange="this.form.submit()">
            <option value="0">All</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <div class="row">
        <?php foreach ($products as $product): ?>
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
</main>
<footer class="mt-5">
    <div class="container text-center">
        &copy; <?= date('Y') ?> NEPT GADGETS – Phone and Accessories. All rights reserved.
    </div>
</footer>
</body>
</html>