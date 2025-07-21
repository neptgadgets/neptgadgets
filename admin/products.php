<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
session_timeout();
require_admin();
check_csrf();

// Handle add/edit/delete
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $cat = intval($_POST['category']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    $img = '';
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $maxsize = 2*1024*1024;
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $safe_name = preg_replace('/[^a-zA-Z0-9._-]/','_',basename($_FILES['image']['name']));
        if (!in_array($ext, $allowed) || $_FILES['image']['size'] > $maxsize) {
            die('Invalid image file.');
        }
        $img = '/assets/' . uniqid() . '_' . $safe_name;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../' . $img);
    }
    $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image, category, is_featured, is_new, is_hot) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $desc, $price, $img, $cat, $is_featured, $is_new, $is_hot]);
}
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $cat = intval($_POST['category']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    $img = $_POST['old_image'];
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $maxsize = 2*1024*1024;
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $safe_name = preg_replace('/[^a-zA-Z0-9._-]/','_',basename($_FILES['image']['name']));
        if (!in_array($ext, $allowed) || $_FILES['image']['size'] > $maxsize) {
            die('Invalid image file.');
        }
        $img = '/assets/' . uniqid() . '_' . $safe_name;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../' . $img);
    }
    $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, image=?, category=?, is_featured=?, is_new=?, is_hot=? WHERE id=?');
    $stmt->execute([$name, $desc, $price, $img, $cat, $is_featured, $is_new, $is_hot, $id]);
}
if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
    $stmt->execute([$id]);
}
// Category management
if (isset($_POST['add_category'])) {
    $catname = trim($_POST['catname']);
    if ($catname) {
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$catname]);
    }
}
if (isset($_POST['edit_category'])) {
    $catid = intval($_POST['catid']);
    $catname = trim($_POST['catname']);
    if ($catid && $catname) {
        $stmt = $pdo->prepare('UPDATE categories SET name=? WHERE id=?');
        $stmt->execute([$catname, $catid]);
    }
}
if (isset($_POST['delete_category'])) {
    $catid = intval($_POST['catid']);
    if ($catid) {
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id=?');
        $stmt->execute([$catid]);
    }
}
$products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products – NEPT GADGETS</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header><div class="container"><h1>Manage Products</h1></div></header>
<main class="container mt-4">
    <h3>Add Product</h3>
    <form method="post" enctype="multipart/form-data" class="mb-4 row g-3">
        <?= csrf_field() ?>
        <div class="col-md-3"><input name="name" class="form-control" placeholder="Name" required></div>
        <div class="col-md-3"><input name="price" class="form-control" placeholder="Price" type="number" step="0.01" required></div>
        <div class="col-md-3"><select name="category" class="form-control" required>
            <?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="col-md-3"><input type="file" name="image" class="form-control"></div>
        <div class="col-12"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
        <div class="col-12">
            <label><input type="checkbox" name="is_featured"> Featured</label>
            <label><input type="checkbox" name="is_new"> New Arrival</label>
            <label><input type="checkbox" name="is_hot"> Hot Deal</label>
            <button type="submit" name="add" class="btn btn-success ms-2">Add Product</button>
        </div>
    </form>
    <h3>Categories</h3>
    <form method="post" class="mb-3 d-flex align-items-center gap-2">
        <?= csrf_field() ?>
        <input name="catname" class="form-control" placeholder="New category" required>
        <button type="submit" name="add_category" class="btn btn-success">Add</button>
    </form>
    <table class="table table-bordered table-sm mb-4">
        <thead><tr><th>ID</th><th>Name</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <form method="post" class="d-flex align-items-center gap-2">
                    <?= csrf_field() ?>
                    <td><?= $cat['id'] ?><input type="hidden" name="catid" value="<?= $cat['id'] ?>"></td>
                    <td><input name="catname" value="<?= htmlspecialchars($cat['name']) ?>" class="form-control"></td>
                    <td>
                        <button type="submit" name="edit_category" class="btn btn-sm btn-primary">Save</button>
                        <button type="submit" name="delete_category" class="btn btn-sm btn-danger" onclick="return confirm('Delete category?')">Delete</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h3>All Products</h3>
    <table class="table table-bordered table-striped">
        <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Image</th><th>Flags</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <td><?= $p['id'] ?><input type="hidden" name="id" value="<?= $p['id'] ?>"></td>
                <td><input name="name" value="<?= htmlspecialchars($p['name']) ?>" class="form-control"></td>
                <td><input name="price" value="<?= $p['price'] ?>" class="form-control" type="number" step="0.01"></td>
                <td><select name="category" class="form-control">
                    <?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>" <?= $p['category']==$cat['id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
                </select></td>
                <td>
                    <?php if ($p['image']): ?><img src="<?= $p['image'] ?>" style="width:60px;height:40px;object-fit:cover;">
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control">
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($p['image']) ?>">
                </td>
                <td>
                    <label><input type="checkbox" name="is_featured" <?= $p['is_featured']?'checked':'' ?>> F</label>
                    <label><input type="checkbox" name="is_new" <?= $p['is_new']?'checked':'' ?>> N</label>
                    <label><input type="checkbox" name="is_hot" <?= $p['is_hot']?'checked':'' ?>> H</label>
                </td>
                <td>
                    <button type="submit" name="edit" class="btn btn-sm btn-primary">Save</button>
                    <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Delete product?')">Delete</button>
                </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/admin/dashboard.php" class="btn btn-link">Back to Dashboard</a>
</main>
</body>
</html>