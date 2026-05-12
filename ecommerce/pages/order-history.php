<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../includes/db.php';

if (!auth_is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user = auth_user();
if ($user['role'] !== 'customer') {
    header('Location: dashboard.php');
    exit;
}

$page_title = 'ShopNest – My Orders';
$current_page = 'orders';
$root_path = '../';
$logged_in = true;
$user_name = auth_name();
$user_initial = auth_profile_initial();
$user_role = auth_role();
$cart_count = cart_count();

$message = '';
$errors = [];

$order_id = $_GET['order_id'] ?? null;
$view_order = null;

// Dummy orders data for the current user (replace with DB query when orders table is implemented)
$orders = [
    [
        'id' => 'ORD-0047',
        'items' => '3 items',
        'total' => '₱15,499',
        'status' => 'Processing',
        'badge' => 'badge-info',
        'date' => 'Apr 21, 2025'
    ],
    [
        'id' => 'ORD-0046',
        'items' => '1 item',
        'total' => '₱4,299',
        'status' => 'Delivered',
        'badge' => 'badge-success',
        'date' => 'Apr 20, 2025'
    ],
    [
        'id' => 'ORD-0045',
        'items' => '2 items',
        'total' => '₱7,850',
        'status' => 'Shipped',
        'badge' => 'badge-warning',
        'date' => 'Apr 19, 2025'
    ],
];

if ($order_id) {
    // Find the order details
    foreach ($orders as $order) {
        if ($order['id'] === $order_id) {
            $view_order = $order;
            break;
        }
    }
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

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <div class="page-header">
      <div>
        <h1 class="page-title">My Orders</h1>
        <p class="page-subtitle">View and track your order history</p>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($view_order): ?>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Order Details - #<?= htmlspecialchars($view_order['id']) ?></h3>
          <a href="order-history.php" class="btn btn-ghost btn-sm">← Back to Orders</a>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;">
            <div>
              <h4>Order Information</h4>
              <p><strong>Order Date:</strong> <?= htmlspecialchars($view_order['date']) ?></p>
              <p><strong>Status:</strong> <span class="badge <?= htmlspecialchars($view_order['badge']) ?>"><?= htmlspecialchars($view_order['status']) ?></span></p>
              <p><strong>Total:</strong> <?= htmlspecialchars($view_order['total']) ?></p>
            </div>
            <div>
              <h4>Items Ordered</h4>
              <p><?= htmlspecialchars($view_order['items']) ?> (sample data)</p>
              <p>Sample items: Smartphone Pro X12, Wireless Earbuds V3, Gaming Mouse RGB</p>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Order History</h3>
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
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                <td><?= htmlspecialchars($order['items']) ?></td>
                <td><strong><?= htmlspecialchars($order['total']) ?></strong></td>
                <td><span class="badge <?= htmlspecialchars($order['badge']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                <td><?= htmlspecialchars($order['date']) ?></td>
                <td>
                  <a href="order-history.php?order_id=<?= htmlspecialchars($order['id']) ?>" class="btn btn-ghost btn-sm">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php endif; ?>

  </main>
</div>

</body>
</html>