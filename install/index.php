<?php
require_once __DIR__ . '/../config/config.php';

$error = $success = '';
$requirements = [];
// Check requirements
if (version_compare(PHP_VERSION, '7.4', '<')) {
    $requirements[] = 'PHP 7.4 or higher is required.';
}
if (!extension_loaded('pdo_mysql')) {
    $requirements[] = 'PDO MySQL extension is required.';
}
if (!is_writable(__DIR__ . '/../assets')) {
    $requirements[] = 'The /assets directory must be writable.';
}
if (!is_writable(__DIR__ . '/../config/config.php')) {
    $requirements[] = 'The config/config.php file must be writable.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($requirements)) {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_user = $_POST['admin_user'];
    $admin_pass = $_POST['admin_pass'];

    try {
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`");

        // Create tables
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin','user') NOT NULL DEFAULT 'user',
            contact VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");
        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE
        ) ENGINE=InnoDB");
        $pdo->exec("CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            image VARCHAR(255),
            category INT,
            is_featured TINYINT(1) DEFAULT 0,
            is_new TINYINT(1) DEFAULT 0,
            is_hot TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB");
        $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            product_id INT,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
        ) ENGINE=InnoDB");

        // Insert default categories
        $pdo->exec("INSERT IGNORE INTO categories (name) VALUES ('Phones'), ('Accessories')");

        // Create admin user
        $hash = password_hash($admin_pass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $stmt->execute([$admin_user, $hash]);

        // Write config
        $config_content = "<?php\ndefine('DB_HOST', '" . addslashes($db_host) . "');\ndefine('DB_NAME', '" . addslashes($db_name) . "');\ndefine('DB_USER', '" . addslashes($db_user) . "');\ndefine('DB_PASS', '" . addslashes($db_pass) . "');\n";
        file_put_contents(__DIR__ . '/../config/config.php', $config_content, FILE_APPEND);

        $success = 'Installation successful!';
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NEPT GADGETS Installer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f8fb; }
        .installer-card { max-width: 500px; margin: 2rem auto; box-shadow: 0 2px 16px #0001; border-radius: 12px; background: #fff; padding: 2rem; }
        .status-list li { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
<div class="installer-card">
    <h1 class="mb-3 text-center" style="color:#0d6efd">NEPT GADGETS <small class="text-muted" style="font-size:0.5em">Installer</small></h1>
    <hr>
    <ul class="status-list">
        <li><strong>PHP Version:</strong> <?= PHP_VERSION ?> <?= version_compare(PHP_VERSION, '7.4', '>=') ? '✅' : '❌' ?></li>
        <li><strong>PDO MySQL:</strong> <?= extension_loaded('pdo_mysql') ? 'Enabled ✅' : 'Missing ❌' ?></li>
        <li><strong>/assets Writable:</strong> <?= is_writable(__DIR__ . '/../assets') ? 'Yes ✅' : 'No ❌' ?></li>
        <li><strong>config/config.php Writable:</strong> <?= is_writable(__DIR__ . '/../config/config.php') ? 'Yes ✅' : 'No ❌' ?></li>
    </ul>
    <?php if ($requirements): ?>
        <div class="alert alert-danger"><strong>Requirements not met:</strong><ul><?php foreach ($requirements as $r) echo "<li>".htmlspecialchars($r)."</li>"; ?></ul></div>
    <?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger">Error: <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            <h4><?= $success ?></h4>
            <p>All database tables and settings have been created.<br>
            <a href="/public/index.php" class="btn btn-success mt-2">Go to Shop</a></p>
        </div>
    <?php else: ?>
    <form method="post" class="mt-3">
        <h4>Database Settings</h4>
        <div class="mb-3">
            <label class="form-label">DB Host</label>
            <input name="db_host" value="localhost" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">DB Name</label>
            <input name="db_name" value="nept_gadgets" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">DB User</label>
            <input name="db_user" value="root" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">DB Pass</label>
            <input name="db_pass" type="password" class="form-control">
        </div>
        <h4>Admin Account</h4>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="admin_user" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="admin_pass" type="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Install</button>
    </form>
    <?php endif; ?>
    <hr>
    <div class="text-center text-muted" style="font-size:0.9em;">&copy; <?= date('Y') ?> NEPT GADGETS – Installer</div>
</div>
</body>
</html>