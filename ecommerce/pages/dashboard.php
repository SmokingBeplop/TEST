<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';

if (!auth_is_logged_in()) {
    header('Location: login.php');
    exit;
}

$page_title = 'ShopNest – Dashboard';
$current_page = 'dashboard';
$root_path = '../';
$logged_in = true;
$user_name = auth_name();
$user_initial = auth_profile_initial();
$user_role = auth_role();
$cart_count = cart_count();
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

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <div class="page-header">
      <div>
        <h1 class="page-title">Good morning, <?= htmlspecialchars($user_name) ?> 👋</h1>
        <p class="page-subtitle">
          <?php if ($user_role === 'seller'): ?>
            Here's what's happening in your store today.
          <?php else: ?>
            Welcome back! Here's your account overview.
          <?php endif; ?>
        </p>
      </div>
      <div style="font-size:0.82rem;color:var(--muted);">📅 April 21, 2025</div>
    </div>

    <?php if ($user_role === 'seller'): ?>
      <!-- SELLER DASHBOARD -->
      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📦</div>
          <div class="stat-value">128</div>
          <div class="stat-label">Total Products</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🛒</div>
          <div class="stat-value">47</div>
          <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-value">312</div>
          <div class="stat-label">Registered Users</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">💰</div>
          <div class="stat-value">₱84K</div>
          <div class="stat-label">Total Revenue</div>
        </div>
      </div>

      <!-- RECENT ORDERS -->
      <div class="card mb-3">
        <div class="card-header">
          <h3 class="card-title">Recent Orders</h3>
          <a href="manage-orders.php" class="btn btn-ghost btn-sm">View All →</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>#ORD-0047</strong></td>
                <td>Juan Dela Cruz</td>
                <td>3 items</td>
                <td><strong>₱15,499</strong></td>
                <td><span class="badge badge-info">Processing</span></td>
                <td>Apr 21, 2025</td>
                <td><a href="manage-orders.php" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
              <tr>
                <td><strong>#ORD-0046</strong></td>
                <td>Maria Santos</td>
                <td>1 item</td>
                <td><strong>₱4,299</strong></td>
                <td><span class="badge badge-success">Delivered</span></td>
                <td>Apr 20, 2025</td>
                <td><a href="manage-orders.php" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
              <tr>
                <td><strong>#ORD-0045</strong></td>
                <td>Pedro Reyes</td>
                <td>2 items</td>
                <td><strong>₱7,850</strong></td>
                <td><span class="badge badge-warning">Shipped</span></td>
                <td>Apr 19, 2025</td>
                <td><a href="manage-orders.php" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
              <tr>
                <td><strong>#ORD-0044</strong></td>
                <td>Ana Lim</td>
                <td>5 items</td>
                <td><strong>₱2,340</strong></td>
                <td><span class="badge badge-success">Delivered</span></td>
                <td>Apr 18, 2025</td>
                <td><a href="manage-orders.php" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
              <tr>
                <td><strong>#ORD-0043</strong></td>
                <td>Carlo Mendoza</td>
                <td>1 item</td>
                <td><strong>₱34,500</strong></td>
                <td><span class="badge badge-danger">Cancelled</span></td>
                <td>Apr 17, 2025</td>
                <td><a href="manage-orders.php" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- BOTTOM ROW -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        <!-- LOW STOCK -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">⚠️ Low Stock Alerts</h3>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Stock</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Smartphone Pro X12</td>
                  <td>4</td>
                  <td><span class="badge badge-danger">Critical</span></td>
                </tr>
                <tr>
                  <td>Wireless Earbuds V3</td>
                  <td>2</td>
                  <td><span class="badge badge-danger">Critical</span></td>
                </tr>
                <tr>
                  <td>Leather Wallet Brown</td>
                  <td>7</td>
                  <td><span class="badge badge-warning">Low</span></td>
                </tr>
                <tr>
                  <td>Gaming Mouse RGB</td>
                  <td>9</td>
                  <td><span class="badge badge-warning">Low</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TOP PRODUCTS -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">🏆 Top Selling Products</h3>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Product</th>
                  <th>Sold</th>
                  <th>Revenue</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>UltraBook Slim 15"</td>
                  <td>24</td>
                  <td>₱828K</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Smartphone Pro X12</td>
                  <td>38</td>
                  <td>₱493K</td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>AirRun Sneakers</td>
                  <td>61</td>
                  <td>₱152K</td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>SmartWatch Series 7</td>
                  <td>29</td>
                  <td>₱124K</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    <?php else: ?>
      <!-- CUSTOMER DASHBOARD -->
      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">🛒</div>
          <div class="stat-value">3</div>
          <div class="stat-label">Orders Placed</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">💳</div>
          <div class="stat-value">₱27K</div>
          <div class="stat-label">Total Spent</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">❤️</div>
          <div class="stat-value">12</div>
          <div class="stat-label">Wishlist Items</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⭐</div>
          <div class="stat-value">4.8</div>
          <div class="stat-label">Average Rating</div>
        </div>
      </div>

      <!-- MY ORDERS -->
      <div class="card mb-3">
        <div class="card-header">
          <h3 class="card-title">My Recent Orders</h3>
          <a href="order-history.php" class="btn btn-ghost btn-sm">View All →</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>#ORD-0047</strong></td>
                <td>3 items</td>
                <td><strong>₱15,499</strong></td>
                <td><span class="badge badge-info">Processing</span></td>
                <td>Apr 21, 2025</td>
                <td><a href="order-history.php?order_id=ORD-0047" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
              <tr>
                <td><strong>#ORD-0046</strong></td>
                <td>1 item</td>
                <td><strong>₱4,299</strong></td>
                <td><span class="badge badge-success">Delivered</span></td>
                <td>Apr 20, 2025</td>
                <td><a href="order-history.php?order_id=ORD-0046" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
              <tr>
                <td><strong>#ORD-0045</strong></td>
                <td>2 items</td>
                <td><strong>₱7,850</strong></td>
                <td><span class="badge badge-warning">Shipped</span></td>
                <td>Apr 19, 2025</td>
                <td><a href="order-history.php?order_id=ORD-0045" class="btn btn-ghost btn-sm">View</a></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- BOTTOM ROW -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        <!-- WISHLIST -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">❤️ My Wishlist</h3>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>UltraBook Slim 15"</td>
                  <td>₱34,500</td>
                  <td><a href="wishlist.php?product_id=3" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                <tr>
                  <td>Wireless Earbuds Pro V3</td>
                  <td>₱1,899</td>
                  <td><a href="wishlist.php?product_id=5" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                <tr>
                  <td>SmartWatch Series 7</td>
                  <td>₱4,299</td>
                  <td><a href="wishlist.php?product_id=4" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ACCOUNT INFO -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">👤 Account Information</h3>
          </div>
          <div style="padding:1.5rem;">
            <div style="display:flex;gap:1rem;margin-bottom:1rem;">
              <div style="width:50px;height:50px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;color:#fff;">
                <?= htmlspecialchars($user_initial) ?>
              </div>
              <div>
                <div style="font-weight:600;"><?= htmlspecialchars($user_name) ?></div>
                <div style="color:var(--muted);font-size:0.9rem;"><?= htmlspecialchars(auth_user_email()) ?></div>
                <div style="color:var(--muted);font-size:0.8rem;">Member since Apr 2025</div>
              </div>
            </div>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
              <a href="profile.php" class="btn btn-outline btn-sm">Edit Profile</a>
              <a href="settings.php" class="btn btn-outline btn-sm">Change Password</a>
            </div>
          </div>
        </div>

      </div>

    <?php endif; ?>

  </main>
</div>

</body>
</html>
