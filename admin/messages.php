<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$messages = $pdo->query('SELECT m.*, u.username, p.name AS product_name FROM messages m LEFT JOIN users u ON m.user_id = u.id LEFT JOIN products p ON m.product_id = p.id ORDER BY m.created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header><div class="container"><h1>Messages</h1></div></header>
<main class="container mt-4">
    <h3>User Inquiries</h3>
    <table class="table table-bordered table-striped">
        <thead><tr><th>User</th><th>Product</th><th>Message</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($messages as $msg): ?>
            <tr>
                <td><?= htmlspecialchars($msg['username']) ?></td>
                <td><?= htmlspecialchars($msg['product_name']) ?></td>
                <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                <td><?= $msg['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/admin/dashboard.php" class="btn btn-link">Back to Dashboard</a>
</main>
</body>
</html>