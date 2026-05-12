<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';

$user_role = $user_role ?? auth_role();
$current_page = $current_page ?? '';
$cart_count = $cart_count ?? array_sum($_SESSION['cart'] ?? []);
$user_name = $user_name ?? auth_name();
$user_initial = $user_initial ?? auth_profile_initial();
?>
<aside class="sidebar">
  <div style="padding:1.5rem 1.75rem;border-bottom:1px solid rgba(255,255,255,0.08);margin-bottom:0.5rem;">
    <div style="display:flex;align-items:center;gap:0.85rem;">
      <div style="width:42px;height:42px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;color:#fff;">
        <?= htmlspecialchars($user_initial) ?>
      </div>
      <div>
        <div style="color:#fff;font-weight:600;font-size:0.9rem;"><?= htmlspecialchars($user_name) ?></div>
        <div style="color:rgba(255,255,255,0.45);font-size:0.75rem;"><?= ucfirst(htmlspecialchars($user_role)) ?></div>
      </div>
    </div>
  </div>

  <div class="sidebar-label">Main</div>
  <ul class="sidebar-menu">
    <li><a href="dashboard.php" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>">📊 &nbsp;Dashboard</a></li>
    <?php if ($user_role !== 'seller'): ?>
      <li><a href="products.php" class="<?= $current_page === 'products' ? 'active' : '' ?>">🛍️ &nbsp;Products</a></li>
      <li><a href="cart.php" class="<?= $current_page === 'cart' ? 'active' : '' ?>">🛒 &nbsp;My Cart</a></li>
      <li><a href="order-history.php" class="<?= $current_page === 'orders' ? 'active' : '' ?>">📦 &nbsp;My Orders</a></li>
    <?php endif; ?>
  </ul>

  <div class="sidebar-label">Account</div>
  <ul class="sidebar-menu">
    <li><a href="profile.php" class="<?= $current_page === 'profile' ? 'active' : '' ?>">👤 &nbsp;Profile</a></li>
    <li><a href="notifications.php" class="<?= $current_page === 'notifications' ? 'active' : '' ?>">🔔 &nbsp;Notifications</a></li>
    <li><a href="settings.php" class="<?= $current_page === 'settings' ? 'active' : '' ?>">⚙️ &nbsp;Settings</a></li>
  </ul>

  <?php if ($user_role === 'seller'): ?>
  <div class="sidebar-label">Admin</div>
  <ul class="sidebar-menu">
    <li><a href="manage-products.php" class="<?= $current_page === 'manage-products' ? 'active' : '' ?>">📦 &nbsp;Manage Products</a></li>
    <li><a href="manage-orders.php" class="<?= $current_page === 'manage-orders' ? 'active' : '' ?>">📋 &nbsp;Manage Orders</a></li>
    <li><a href="manage-users.php" class="<?= $current_page === 'manage-users' ? 'active' : '' ?>">👥 &nbsp;Manage Users</a></li>
  </ul>
  <?php endif; ?>

  <div style="margin-top:auto;padding:1.5rem 1.75rem;">
    <a href="logout.php" class="btn btn-danger btn-sm btn-full" style="color:#fff;">🚪 &nbsp;Sign Out</a>
  </div>
</aside>
