<?php
require_once __DIR__ . '/../includes/db.php';

$current_page = 'manage-products';
$root_path = '../';
$cart_count = 0;
$logged_in = true;
$user_name = 'Admin';

$pdo = null;
$message = '';
$errors = [];

try {
    $pdo = db_connect();
} catch (Exception $e) {
    $errors[] = 'Database connection error: ' . htmlspecialchars($e->getMessage());
}

$product = [
    'id' => 0,
    'icon' => '📦',
    'badge' => '',
    'category' => 'Electronics',
    'name' => '',
    'description' => '',
    'price' => '0.00',
    'original_price' => '',
    'reviews' => '',
    'in_stock' => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    $icon = trim($_POST['icon'] ?? '📦');
    $badge = trim($_POST['badge'] ?? '');
    $category = trim($_POST['category'] ?? 'Electronics');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = str_replace(',', '', trim($_POST['price'] ?? '0'));
    $original_price = str_replace(',', '', trim($_POST['original_price'] ?? ''));
    $reviews = trim($_POST['reviews'] ?? '');
    $in_stock = isset($_POST['in_stock']) ? 1 : 0;

    if ($name === '') {
        $errors[] = 'Product name is required.';
    }
    if (!is_numeric($price) || floatval($price) < 0) {
        $errors[] = 'Valid price is required.';
    }

    if (empty($errors)) {
        if (!$pdo) {
            $errors[] = 'Cannot process the request because the database is unavailable.';
        } else {
            if ($action === 'create') {
                $stmt = $pdo->prepare('INSERT INTO products (icon, badge, category, name, description, price, original_price, reviews, in_stock) VALUES (:icon, :badge, :category, :name, :description, :price, :original_price, :reviews, :in_stock)');
                $stmt->execute([ 
                    'icon' => $icon,
                    'badge' => $badge ?: null,
                    'category' => $category,
                    'name' => $name,
                    'description' => $description,
                    'price' => number_format(floatval($price), 2, '.', ''),
                    'original_price' => $original_price !== '' ? number_format(floatval($original_price), 2, '.', '') : null,
                    'reviews' => $reviews ?: null,
                    'in_stock' => $in_stock,
                ]);
                $message = 'Product created successfully.';
            } elseif ($action === 'update' && $id > 0) {
                $stmt = $pdo->prepare('UPDATE products SET icon = :icon, badge = :badge, category = :category, name = :name, description = :description, price = :price, original_price = :original_price, reviews = :reviews, in_stock = :in_stock WHERE id = :id');
                $stmt->execute([ 
                    'icon' => $icon,
                    'badge' => $badge ?: null,
                    'category' => $category,
                    'name' => $name,
                    'description' => $description,
                    'price' => number_format(floatval($price), 2, '.', ''),
                    'original_price' => $original_price !== '' ? number_format(floatval($original_price), 2, '.', '') : null,
                    'reviews' => $reviews ?: null,
                    'in_stock' => $in_stock,
                    'id' => $id,
                ]);
                $message = 'Product updated successfully.';
            } elseif ($action === 'delete' && $id > 0) {
                $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
                $stmt->execute(['id' => $id]);
                $message = 'Product deleted successfully.';
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id']) && $pdo) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if ($row) {
        $product = array_merge($product, $row);
    }
}

$products = [];
if ($pdo) {
    $products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShopNest – Manage Products</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="section" style="padding-top:3rem; padding-bottom:3rem;">
  <div class="container">
    <div class="section-header">
      <div class="section-tag">Admin</div>
      <h2 class="section-title">Manage Products</h2>
      <p class="section-desc">Create, edit, and delete product listings using a real MySQL database.</p>
    </div>

    <?php if ($message): ?>
      <div class="card" style="border-color:var(--success);background:#edf7ef;color:#1f6f3d;">
        <div class="card-body" style="padding:1rem 1.5rem;"><?= htmlspecialchars($message) ?></div>
      </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="card" style="border-color:var(--danger);background:#fde8e8;color:#86181d;">
        <div class="card-body" style="padding:1rem 1.5rem;">
          <ul style="margin:0;list-style:disc inside;">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 420px;gap:2rem;">
      <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
          <h3 class="card-title">Products</h3>
          <a href="manage-products.php" class="btn btn-sm btn-ghost">+ New Product</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($products)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--muted);">No products available.</td></tr>
              <?php else: ?>
                <?php foreach ($products as $row): ?>
                  <tr>
                    <td><?= intval($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td>₱<?= number_format((float) $row['price'], 2) ?></td>
                    <td><span class="badge <?= $row['in_stock'] ? 'badge-success' : 'badge-warning' ?>"><?= $row['in_stock'] ? 'In Stock' : 'Out of Stock' ?></span></td>
                    <td style="white-space:nowrap;">
                      <a href="manage-products.php?action=edit&id=<?= intval($row['id']) ?>" class="btn btn-ghost btn-sm">Edit</a>
                      <form method="POST" action="manage-products.php" style="display:inline-block;margin:0;">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id" value="<?= intval($row['id']) ?>" />
                        <button type="submit" class="btn btn-danger btn-sm" style="margin-left:0.25rem;">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?= $product['id'] ? 'Edit Product' : 'Add Product' ?></h3>
        </div>
        <div class="card-body">
          <form method="POST" action="manage-products.php">
            <input type="hidden" name="action" value="<?= $product['id'] ? 'update' : 'create' ?>" />
            <input type="hidden" name="id" value="<?= intval($product['id']) ?>" />

            <div class="form-group">
              <label class="form-label">Icon</label>
              <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($product['icon']) ?>" placeholder="📱" />
            </div>
            <div class="form-group">
              <label class="form-label">Badge</label>
              <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($product['badge']) ?>" placeholder="Sale, New, Hot" />
            </div>
            <div class="form-group">
              <label class="form-label">Category</label>
              <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($product['category']) ?>" placeholder="Electronics" />
            </div>
            <div class="form-group">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required />
            </div>
            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Price</label>
                <input type="text" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required />
              </div>
              <div class="form-group">
                <label class="form-label">Original Price</label>
                <input type="text" name="original_price" class="form-control" value="<?= htmlspecialchars($product['original_price']) ?>" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Reviews</label>
              <input type="text" name="reviews" class="form-control" value="<?= htmlspecialchars($product['reviews']) ?>" placeholder="⭐⭐⭐⭐⭐ (123 reviews)" />
            </div>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
              <input type="checkbox" id="in_stock" name="in_stock" value="1" <?= $product['in_stock'] ? 'checked' : '' ?> />
              <label for="in_stock" style="cursor:pointer;">In stock</label>
            </div>
            <button type="submit" class="btn btn-primary btn-full btn-lg"><?= $product['id'] ? 'Update Product' : 'Create Product' ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
