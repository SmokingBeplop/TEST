<?php
require_once __DIR__ . '/auth.php';

$root_path = $root_path ?? './';
$current_page = $current_page ?? '';
$cart_count = isset($cart_count) ? intval($cart_count) : array_sum($_SESSION['cart'] ?? []);
$logged_in = $logged_in ?? auth_is_logged_in();
$user_name = $user_name ?? auth_name();
$user_role = $user_role ?? auth_role();
?> 
<nav class="navbar">
  <a href="<?= $root_path ?>index.php" class="nav-brand">Shop<span>Nest</span></a>
  <?php if ($logged_in && $current_page !== 'home'): ?>
    <ul class="nav-links">
      <li><a href="<?= $root_path ?>index.php" class="<?= $current_page === 'home' ? 'active' : '' ?>">Home</a></li>
      <?php if ($user_role !== 'seller'): ?>
        <li><a href="<?= $root_path ?>pages/products.php" class="<?= $current_page === 'products' ? 'active' : '' ?>">Products</a></li>
        <li><a href="<?= $root_path ?>pages/cart.php" class="<?= $current_page === 'cart' ? 'active' : '' ?>">Cart</a></li>
        <li><a href="<?= $root_path ?>pages/order-summary.php" class="<?= $current_page === 'orders' ? 'active' : '' ?>">Orders</a></li>
      <?php endif; ?>
      <?php if ($user_role === 'seller'): ?>
        <li><a href="<?= $root_path ?>pages/manage-products.php" class="<?= $current_page === 'manage-products' ? 'active' : '' ?>">Manage Products</a></li>
      <?php endif; ?>
    </ul>
  <?php endif; ?>
  <div class="nav-actions">
    <?php if ($logged_in && $user_role !== 'seller'): ?>
      <a href="<?= $root_path ?>pages/cart.php" class="cart-icon">🛒 <span class="cart-badge"><?= $cart_count ?></span></a>
    <?php endif; ?>
    <?php if ($logged_in): ?>
      <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
        <span style="font-size:0.9rem;color:var(--muted);">Hi, <?= htmlspecialchars($user_name) ?>!</span>
        <a href="<?= $root_path ?>pages/profile.php" class="btn-nav">Profile</a>
        <a href="<?= $root_path ?>pages/logout.php" class="btn-nav" style="color:#fff;">Sign Out</a>
      </div>
    <?php elseif (empty($hide_sign_in)): ?>
      <a href="<?= $root_path ?>pages/login.php" class="btn-nav">Sign In</a>
    <?php endif; ?>
  </div>
</nav>
