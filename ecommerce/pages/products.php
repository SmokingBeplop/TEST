<?php
$page_title   = 'ShopNest – Products';
$current_page = 'products';
$root_path    = '../';
require_once $root_path . 'includes/auth.php';
$logged_in    = auth_is_logged_in();
$user_name    = auth_name();
require_once $root_path . 'includes/cart.php';
$cart_count   = cart_count();

$selected_cat = isset($_GET['category']) ? $_GET['category'] : 'All';
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : 50000;
$avail = isset($_GET['avail']) ? $_GET['avail'] : 'In Stock';
$persistentFilters = array_filter([
  'q' => $search_query,
  'min_price' => isset($_GET['min_price']) ? $_GET['min_price'] : null,
  'max_price' => isset($_GET['max_price']) ? $_GET['max_price'] : null,
  'avail' => $avail !== 'All' ? $avail : null,
], fn($value) => $value !== null && $value !== '');

$categories = ['All'];
$cat_counts = ['All' => 0];
$products = [];
$total_products = 0;

require_once $root_path . 'includes/db.php';

try {
  $pdo = db_connect();
  $query = 'SELECT * FROM products WHERE 1=1';
  $params = [];

  if ($selected_cat !== 'All') {
    $query .= ' AND category = :category';
    $params['category'] = $selected_cat;
  }
  if ($search_query !== '') {
    $query .= ' AND name LIKE :search';
    $params['search'] = '%' . $search_query . '%';
  }
  if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    $query .= ' AND price >= :min_price';
    $params['min_price'] = $min_price;
  }
  if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    $query .= ' AND price <= :max_price';
    $params['max_price'] = $max_price;
  }
  if ($avail === 'In Stock') {
    $query .= ' AND in_stock = 1';
  } elseif ($avail === 'Out of Stock') {
    $query .= ' AND in_stock = 0';
  }

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $products = $stmt->fetchAll();

  $total_products = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
  $categories = ['All'];
  $cat_counts = ['All' => $total_products];
  $category_stats = $pdo->query('SELECT category, COUNT(*) AS count FROM products GROUP BY category ORDER BY category')->fetchAll();

  foreach ($category_stats as $row) {
    $categories[] = $row['category'];
    $cat_counts[$row['category']] = (int) $row['count'];
  }
} catch (Exception $e) {
  $all_products = [
    ['id' => 1, 'icon' => '📱', 'badge' => 'Sale', 'category' => 'Electronics', 'name' => 'Smartphone Pro X12',      'price' => '₱12,999', 'original' => '₱16,500', 'reviews' => '⭐⭐⭐⭐⭐ (142 reviews)', 'in_stock' => 1],
    ['id' => 2, 'icon' => '👟', 'badge' => '',     'category' => 'Footwear',    'name' => 'AirRun Classic Sneakers',  'price' => '₱2,499',  'original' => '',         'reviews' => '⭐⭐⭐⭐☆ (87 reviews)', 'in_stock' => 1],
    ['id' => 3, 'icon' => '💻', 'badge' => 'New',  'category' => 'Electronics', 'name' => 'UltraBook Slim 15"',       'price' => '₱34,500', 'original' => '₱38,000', 'reviews' => '⭐⭐⭐⭐⭐ (56 reviews)', 'in_stock' => 1],
    ['id' => 4, 'icon' => '⌚', 'badge' => '',     'category' => 'Accessories', 'name' => 'SmartWatch Series 7',       'price' => '₱4,299',  'original' => '',         'reviews' => '⭐⭐⭐⭐☆ (203 reviews)', 'in_stock' => 1],
    ['id' => 5, 'icon' => '🎧', 'badge' => 'Hot',  'category' => 'Electronics', 'name' => 'Wireless Earbuds Pro V3',  'price' => '₱1,899',  'original' => '₱2,499',  'reviews' => '⭐⭐⭐⭐⭐ (319 reviews)', 'in_stock' => 1],
    ['id' => 6, 'icon' => '👜', 'badge' => '',     'category' => 'Fashion',     'name' => 'Genuine Leather Handbag',  'price' => '₱3,750',  'original' => '',         'reviews' => '⭐⭐⭐⭐☆ (74 reviews)', 'in_stock' => 1],
    ['id' => 7, 'icon' => '🖱️', 'badge' => '',    'category' => 'Electronics', 'name' => 'Gaming Mouse RGB 16000dpi','price' => '₱1,250',  'original' => '',         'reviews' => '⭐⭐⭐⭐⭐ (198 reviews)', 'in_stock' => 1],
    ['id' => 8, 'icon' => '🌿', 'badge' => '',     'category' => 'Beauty',      'name' => 'Organic Skincare Set',     'price' => '₱899',    'original' => '',         'reviews' => '⭐⭐⭐⭐☆ (45 reviews)', 'in_stock' => 1],
  ];

  $categories = ['All', 'Electronics', 'Fashion', 'Home', 'Beauty', 'Footwear', 'Accessories'];
  $products = array_filter($all_products, function($p) use ($selected_cat, $search_query, $min_price, $max_price, $avail) {
    $cat_match = ($selected_cat === 'All' || $p['category'] === $selected_cat);
    $search_match = ($search_query === '' || stripos($p['name'], $search_query) !== false);
    $price_value = floatval(str_replace(['₱', ',', ' '], '', $p['price']));
    $price_match = $price_value >= $min_price && $price_value <= $max_price;
    $avail_match = true;
    if ($avail === 'In Stock') {
      $avail_match = !empty($p['in_stock']);
    } elseif ($avail === 'Out of Stock') {
      $avail_match = empty($p['in_stock']);
    }
    return $cat_match && $search_match && $price_match && $avail_match;
  });
  $total_products = count($all_products);
  $cat_counts = ['All' => $total_products, 'Electronics' => 4, 'Fashion' => 1, 'Home' => 0, 'Beauty' => 1, 'Footwear' => 1, 'Accessories' => 1];
}

$showing = count($products);
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

<?php include '../includes/navbar.php'; ?>

<!-- PAGE HEADER -->
<div style="background:var(--dark);padding:3rem 0;border-bottom:1px solid rgba(255,255,255,0.05);">
  <div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <div>
        <p style="color:var(--accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">Explore</p>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:700;color:#fff;">All Products</h1>
        <p style="color:rgba(255,255,255,0.55);font-size:0.875rem;margin-top:0.25rem;">
          Showing <?= $showing ?> of <?= $total_products ?> products
        </p>
      </div>
      <form method="GET" action="products.php" class="search-bar">
        <input type="text" name="q" placeholder="Search products..." value="<?= htmlspecialchars($search_query) ?>" />
        <button type="submit">Search</button>
      </form>
    </div>
  </div>
</div>

<div class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:240px 1fr;gap:2.5rem;">

      <!-- SIDEBAR FILTERS -->
      <aside>
        <div class="card" style="position:sticky;top:90px;">
          <div class="card-header">
            <h3 class="card-title">Filters</h3>
            <a href="products.php" style="font-size:0.8rem;color:var(--accent);">Clear All</a>
          </div>
          <div class="card-body">
            <form method="GET" action="products.php">
              <div style="margin-bottom:1.5rem;">
                <div style="font-weight:600;font-size:0.82rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.75rem;">Category</div>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                  <?php foreach ($cat_counts as $cat => $count): ?>
                    <label style="display:flex;gap:0.5rem;align-items:center;font-size:0.875rem;cursor:pointer;">
                      <input type="radio" name="category" value="<?= htmlspecialchars($cat) ?>"
                        <?= ($selected_cat === $cat ? 'checked' : '') ?> />
                      <?= htmlspecialchars($cat) ?> (<?= intval($count) ?>)
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <hr class="divider" />

              <div style="margin-bottom:1.5rem;">
                <div style="font-weight:600;font-size:0.82rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.75rem;">Price Range</div>
                <div style="display:flex;gap:0.5rem;align-items:center;">
                  <input type="number" name="min_price" class="form-control" placeholder="Min"
                    value="<?= htmlspecialchars($_GET['min_price'] ?? '0') ?>" style="font-size:0.82rem;padding:0.5rem;" />
                  <span style="color:var(--muted);">–</span>
                  <input type="number" name="max_price" class="form-control" placeholder="Max"
                    value="<?= htmlspecialchars($_GET['max_price'] ?? '50000') ?>" style="font-size:0.82rem;padding:0.5rem;" />
                </div>
              </div>

              <hr class="divider" />

              <div style="margin-bottom:1.5rem;">
                <div style="font-weight:600;font-size:0.82rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.75rem;">Availability</div>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                  <?php foreach (['In Stock', 'Out of Stock', 'All'] as $avail): ?>
                    <label style="display:flex;gap:0.5rem;align-items:center;font-size:0.875rem;cursor:pointer;">
                      <input type="radio" name="avail" value="<?= htmlspecialchars($avail) ?>"
                        <?= (($_GET['avail'] ?? 'In Stock') === $avail ? 'checked' : '') ?> />
                      <?= htmlspecialchars($avail) ?>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <hr class="divider" />
              <button type="submit" class="btn btn-primary btn-full">Apply Filters</button>
            </form>
          </div>
        </div>
      </aside>

      <!-- PRODUCT GRID -->
      <div>
        <!-- FILTER BAR -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
          <div class="filter-bar" style="margin-bottom:0;">
            <?php foreach ($categories as $cat): ?>
              <a href="products.php?<?= htmlspecialchars(http_build_query(array_merge($persistentFilters, ['category' => $cat, 'page' => 1]))) ?>"
                class="filter-pill <?= ($selected_cat === $cat ? 'active' : '') ?>">
                <?= htmlspecialchars($cat) ?>
              </a>
            <?php endforeach; ?>
          </div>
          <select name="sort" class="form-control" style="width:auto;font-size:0.82rem;padding:0.45rem 1rem;">
            <option>Sort: Featured</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
            <option>Newest First</option>
          </select>
        </div>

        <?php if (empty($products)): ?>
          <div style="text-align:center;padding:4rem;color:var(--muted);">
            <div style="font-size:3rem;margin-bottom:1rem;">🔍</div>
            <div style="font-weight:600;">No products found</div>
            <p style="margin-top:0.5rem;font-size:0.875rem;">Try adjusting your filters or search term.</p>
            <a href="products.php" class="btn btn-primary mt-2">Clear Filters</a>
          </div>
        <?php else: ?>
          <div class="products-grid">
            <?php foreach ($products as $product): ?>
              <div class="product-card">
                <div class="product-img">
                  <?= $product['icon'] ?>
                  <?php if ($product['badge']): ?>
                    <span class="product-badge"><?= htmlspecialchars($product['badge']) ?></span>
                  <?php endif; ?>
                </div>
                <div class="product-info">
                  <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                  <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                  <div style="font-size:0.78rem;color:var(--muted);margin-bottom:0.5rem;"><?= $product['reviews'] ?></div>
                  <div class="product-price">
                    <?= htmlspecialchars($product['price']) ?>
                    <?php if (!empty($product['original'])): ?>
                      <span class="original"><?= htmlspecialchars($product['original']) ?></span>
                    <?php endif; ?>
                  </div>
                  <a href="cart.php?action=add&id=<?= intval($product['id'] ?? 0) ?>" class="btn btn-primary btn-sm btn-full">🛒 Add to Cart</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- PAGINATION -->
          <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:2.5rem;">
            <a href="products.php?<?= htmlspecialchars(http_build_query(array_merge($persistentFilters, ['page' => max(1, ($_GET['page'] ?? 1) - 1)]))) ?>" class="btn btn-ghost btn-sm">← Prev</a>
            <?php for ($i = 1; $i <= 3; $i++): ?>
              <a href="products.php?<?= htmlspecialchars(http_build_query(array_merge($persistentFilters, ['page' => $i]))) ?>" class="btn <?= (($_GET['page'] ?? 1) == $i ? 'btn-primary' : 'btn-ghost') ?> btn-sm"><?= $i ?></a>
            <?php endfor; ?>
            <a href="products.php?<?= htmlspecialchars(http_build_query(array_merge($persistentFilters, ['page' => ($_GET['page'] ?? 1) + 1]))) ?>" class="btn btn-ghost btn-sm">Next →</a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>