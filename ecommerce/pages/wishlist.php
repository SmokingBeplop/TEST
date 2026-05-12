<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';

if (!auth_is_logged_in()) {
    header('Location: login.php');
    exit;
}

$page_title = 'ShopNest – Wishlist';
$current_page = 'wishlist';
$root_path = '../';
$logged_in = true;
$user_name = auth_name();
$user_initial = auth_profile_initial();
$user_role = auth_role();
$cart_count = cart_count();

$wishlist_product_ids = [3, 5, 4];
$wishlist_products = array_filter(array_map(fn($id) => cart_get_product($id), $wishlist_product_ids));

$selected_product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;
$selected_product = null;
if ($selected_product_id) {
    $selected_product = cart_get_product($selected_product_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="dashboard-layout">
  <?php include __DIR__ . '/../includes/dashboard-sidebar.php'; ?>

  <main class="main-content">

    <div class="page-header">
      <div>
        <h1 class="page-title">My Wishlist</h1>
        <p class="page-subtitle">Your saved favorites are all here.</p>
      </div>
      <a href="dashboard.php" class="btn btn-ghost btn-sm">← Back to Dashboard</a>
    </div>

    <?php if ($selected_product && $selected_product_id): ?>
      <div class="card mb-3">
        <div class="card-header">
          <h3 class="card-title">Product Details</h3>
          <a href="wishlist.php" class="btn btn-ghost btn-sm">Back to Wishlist</a>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:120px 1fr;gap:1.5rem;align-items:center;">
            <div style="font-size:3rem;line-height:1;"><?= htmlspecialchars($selected_product['icon'] ?? '📦') ?></div>
            <div>
              <h2 style="margin:0 0 0.5rem;"><?= htmlspecialchars($selected_product['name'] ?? 'Product') ?></h2>
              <p style="margin:0 0 0.75rem;color:var(--muted);font-size:0.95rem;"><?= htmlspecialchars($selected_product['category'] ?? 'Category') ?></p>
              <p style="margin:0 0 1rem;font-size:1.25rem;font-weight:700;">₱<?= number_format(floatval($selected_product['price'] ?? $selected_product['unit_price'] ?? 0), 2) ?></p>
              <p style="margin:0 0 1.25rem;"><?= htmlspecialchars($selected_product['reviews'] ?? '') ?></p>
              <a href="products.php?q=<?= urlencode($selected_product['name'] ?? '') ?>" class="btn btn-primary btn-sm">Browse Similar</a>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Wishlist Items</h3>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($wishlist_products as $index => $product): ?>
              <tr>
                <td><?= htmlspecialchars($product['name'] ?? 'Product') ?></td>
                <td>₱<?= number_format(floatval($product['price'] ?? $product['unit_price'] ?? 0), 2) ?></td>
                <td><span class="badge <?= empty($product['in_stock']) ? 'badge-danger' : 'badge-success' ?>"><?= empty($product['in_stock']) ? 'Out of stock' : 'In stock' ?></span></td>
                <td><a href="wishlist.php?product_id=<?= intval($wishlist_product_ids[$index]) ?>" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

</body>
</html>