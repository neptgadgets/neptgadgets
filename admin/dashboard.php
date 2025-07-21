<?php
require_once __DIR__ . '/../includes/auth.php';
session_timeout();
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header><div class="container"><h1>Admin Dashboard</h1></div></header>
<main class="container mt-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="product-card text-center">
                <h3>Products</h3>
                <a href="/admin/products.php" class="btn btn-primary">Manage Products</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="product-card text-center">
                <h3>Users</h3>
                <a href="/admin/users.php" class="btn btn-primary">Manage Users</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="product-card text-center">
                <h3>Messages</h3>
                <a href="/admin/messages.php" class="btn btn-primary">View Messages</a>
            </div>
        </div>
    </div>
    <div class="mt-4 text-center">
        <a href="/public/index.php" class="btn btn-link">View Shop</a>
        <a href="/admin/logout.php" class="btn btn-danger">Logout</a>
    </div>
</main>
</body>
</html>