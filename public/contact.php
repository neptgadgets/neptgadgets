<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact – NEPT GADGETS</title>
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
    <h2>Contact Us</h2>
    <p><strong>Business Name:</strong> <?= $business_name ?></p>
    <p><strong>Location:</strong> <?= $business_location ?></p>
    <p><strong>Contact Number:</strong> <?= $business_contact ?></p>
    <div class="mb-3">
        <a href="https://wa.me/<?= $whatsapp_number ?>" class="btn icon-btn whatsapp" target="_blank"><i class="fab fa-whatsapp"></i> Message on WhatsApp</a>
        <a href="https://t.me/<?= $telegram_username ?>" class="btn icon-btn telegram" target="_blank"><i class="fab fa-telegram"></i> Message on Telegram</a>
    </div>
</main>
<footer class="mt-5">
    <div class="container text-center">
        &copy; <?= date('Y') ?> NEPT GADGETS – Phone and Accessories. All rights reserved.
    </div>
</footer>
</body>
</html>