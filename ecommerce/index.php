<?php
require_once 'includes/auth.php';

$page_title    = 'ShopNest – Home';
$current_page  = 'home';
$root_path     = './';
$cart_count    = 0;        // Replace with session value, e.g. $_SESSION['cart_count'] ?? 0
$logged_in     = auth_is_logged_in();
$user_role     = $logged_in ? auth_role() : '';
$hide_sign_in  = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="hero-content">
      <span class="hero-tag">✦ New Collection 2026</span>
      <h1>Shop the <em>Finest</em> Products Online</h1>
      <p>Discover thousands of curated products — from electronics to fashion — delivered right to your doorstep.</p>
      <div class="hero-btns">
        <?php if ($user_role !== 'seller' && !$logged_in): ?>
          <a href="pages/register.php" class="btn btn-outline btn-lg" style="color:#fff;border-color:rgba(255,255,255,0.4);">Create Account</a>
          <div style="margin-top:1rem;font-size:0.95rem;color:rgba(255,255,255,0.8);">
            Already have an account? <a href="pages/login.php" style="color:#fff;text-decoration:underline;">Sign In</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($user_role !== 'seller'): ?>
<!-- CATEGORY STRIP -->
<section class="section-sm" style="background:#fff; border-bottom:1px solid var(--border);">
  <div class="container">
    <div style="display:grid; grid-template-columns:repeat(6,1fr); gap:1rem; text-align:center;">
      <?php
      $categories = [
        ['icon' => '📱', 'label' => 'Electronics'],
        ['icon' => '👗', 'label' => 'Fashion'],
        ['icon' => '🏠', 'label' => 'Home & Living'],
        ['icon' => '💄', 'label' => 'Beauty'],
        ['icon' => '⚽', 'label' => 'Sports'],
        ['icon' => '📚', 'label' => 'Books'],
      ];
      foreach ($categories as $cat): ?>
        <a href="pages/products.php?category=<?= urlencode($cat['label']) ?>" class="cat-strip-item" style="text-decoration:none;color:var(--dark);">
          <div style="font-size:2.2rem;margin-bottom:0.4rem;"><?= $cat['icon'] ?></div>
          <div style="font-size:0.8rem;font-weight:600;"><?= htmlspecialchars($cat['label']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div class="section-tag">Handpicked for You</div>
      <h2 class="section-title">Featured Products</h2>
      <p class="section-desc">Our best-selling items loved by thousands of shoppers across the country.</p>
    </div>

    <div class="products-grid">
      <?php
      $featured_products = [
        ['icon' => '📱', 'badge' => 'Sale',  'category' => 'Electronics', 'name' => 'Smartphone Pro X12',   'price' => '₱12,999', 'original' => '₱16,500'],
        ['icon' => '👟', 'badge' => '',       'category' => 'Footwear',    'name' => 'AirRun Classic Sneakers','price' => '₱2,499',  'original' => ''],
        ['icon' => '💻', 'badge' => 'New',    'category' => 'Electronics', 'name' => 'UltraBook Slim 15"',   'price' => '₱34,500', 'original' => '₱38,000'],
        ['icon' => '⌚', 'badge' => '',       'category' => 'Accessories', 'name' => 'SmartWatch Series 7',  'price' => '₱4,299',  'original' => ''],
      ];
      $add_to_cart_url = $logged_in ? 'pages/products.php' : 'pages/login.php';
      foreach ($featured_products as $product): ?>
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
            <div class="product-price">
              <?= htmlspecialchars($product['price']) ?>
              <?php if ($product['original']): ?>
                <span class="original"><?= htmlspecialchars($product['original']) ?></span>
              <?php endif; ?>
            </div>
            <a href="<?= htmlspecialchars($add_to_cart_url) ?>" class="btn btn-primary btn-sm btn-full">Add to Cart</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>
<?php endif; ?>

<!-- WHY US BANNER -->
<section class="section-sm" style="background:var(--dark);color:#fff;">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:2rem;text-align:center;">
      <?php
      $perks = [
        ['icon' => '🚚', 'title' => 'Free Shipping',    'desc' => 'On all orders above ₱1,000'],
        ['icon' => '🔒', 'title' => 'Secure Payments',  'desc' => '100% safe and encrypted'],
        ['icon' => '↩️', 'title' => 'Easy Returns',     'desc' => '30-day return policy'],
        ['icon' => '💬', 'title' => '24/7 Support',     'desc' => 'Always here to help'],
      ];
      foreach ($perks as $perk): ?>
        <div>
          <div style="font-size:2rem;margin-bottom:0.5rem;"><?= $perk['icon'] ?></div>
          <div style="font-weight:700;margin-bottom:0.25rem;"><?= htmlspecialchars($perk['title']) ?></div>
          <div style="font-size:0.82rem;color:rgba(255,255,255,0.55);"><?= htmlspecialchars($perk['desc']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>