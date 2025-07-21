<?php
// NEPT GADGETS Installer
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_user = $_POST['admin_user'];
    $admin_pass = $_POST['admin_pass'];

    // Try DB connection
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

        echo '<h2>Installation successful!</h2><a href="/public/index.php">Go to site</a>';
        exit;
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
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <h1>NEPT GADGETS – Installer</h1>
    <?php if (!empty($error)) echo '<p style="color:red">' . htmlspecialchars($error) . '</p>'; ?>
    <form method="post">
        <h3>Database Settings</h3>
        <label>DB Host: <input name="db_host" value="localhost" required></label><br>
        <label>DB Name: <input name="db_name" value="nept_gadgets" required></label><br>
        <label>DB User: <input name="db_user" value="root" required></label><br>
        <label>DB Pass: <input name="db_pass" type="password"></label><br>
        <h3>Admin Account</h3>
        <label>Username: <input name="admin_user" required></label><br>
        <label>Password: <input name="admin_pass" type="password" required></label><br>
        <button type="submit">Install</button>
    </form>
</body>
</html>