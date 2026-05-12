<?php
require_once __DIR__ . '/../includes/auth.php';

if (!auth_is_logged_in()) {
    header('Location: login.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = '../';
$page_title = 'ShopNest – Notifications';
$current_page = 'notifications';
$logged_in = true;
$user_name = auth_name();
$cart_count = array_sum($_SESSION['cart'] ?? []);

if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [
        ['id' => 1, 'title' => 'Order confirmed', 'message' => 'Your recent order has been confirmed and is being prepared.', 'date' => 'May 12, 2026', 'read' => false],
        ['id' => 2, 'title' => 'New message from seller', 'message' => 'Your favorite seller has replied to your question.', 'date' => 'May 11, 2026', 'read' => false],
        ['id' => 3, 'title' => 'Special discount', 'message' => 'Enjoy 10% off on select electronics for the next 48 hours.', 'date' => 'May 10, 2026', 'read' => true],
    ];
}

$notifications = &$_SESSION['notifications'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $notificationId = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : null;

    if ($action === 'mark_read' && $notificationId !== null) {
        foreach ($notifications as &$item) {
            if ($item['id'] === $notificationId) {
                $item['read'] = true;
                break;
            }
        }
    }
    if ($action === 'mark_unread' && $notificationId !== null) {
        foreach ($notifications as &$item) {
            if ($item['id'] === $notificationId) {
                $item['read'] = false;
                break;
            }
        }
    }
    if ($action === 'mark_all_read') {
        foreach ($notifications as &$item) {
            $item['read'] = true;
        }
    }
    if ($action === 'clear_all') {
        $notifications = [];
    }
}

$unread_count = count(array_filter($notifications, fn($item) => !$item['read']));
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
    <div class="section">
      <div class="container">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
      <div>
        <p style="color:var(--accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">Notifications</p>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:700;">Stay up to date</h1>
        <p style="color:var(--muted);font-size:0.95rem;margin-top:0.5rem;">All your recent alerts and updates are listed below.</p>
      </div>
      <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
        <span style="font-size:0.9rem;color:var(--muted);">Unread: <strong><?= $unread_count ?></strong></span>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="action" value="mark_all_read" />
          <button type="submit" class="btn btn-ghost btn-sm">Mark all read</button>
        </form>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="action" value="clear_all" />
          <button type="submit" class="btn btn-danger btn-sm">Clear all</button>
        </form>
      </div>
    </div>

    <?php if (empty($notifications)): ?>
      <div class="card">
        <div class="card-body" style="text-align:center;color:var(--muted);padding:2rem;">
          You have no notifications right now.
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Status</th>
                <th>Notification</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($notifications as $item): ?>
                <tr style="background:<?= $item['read'] ? 'transparent' : 'rgba(17, 113, 255, 0.05)' ?>;">
                  <td><?= $item['read'] ? 'Read' : 'Unread' ?></td>
                  <td>
                    <strong><?= htmlspecialchars($item['title']) ?></strong>
                    <div style="color:var(--muted);font-size:0.95rem;margin-top:0.25rem;"><?= htmlspecialchars($item['message']) ?></div>
                  </td>
                  <td><?= htmlspecialchars($item['date']) ?></td>
                  <td>
                    <form method="POST" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                      <input type="hidden" name="notification_id" value="<?= $item['id'] ?>" />
                      <input type="hidden" name="action" value="<?= $item['read'] ? 'mark_unread' : 'mark_read' ?>" />
                      <button type="submit" class="btn btn-ghost btn-sm"><?= $item['read'] ? 'Mark unread' : 'Mark read' ?></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
