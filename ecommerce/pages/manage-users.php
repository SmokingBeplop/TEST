<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!auth_is_logged_in() || auth_role() !== 'seller') {
    header('Location: dashboard.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'ShopNest – Manage Users';
$current_page = 'manage-users';
$root_path = '../';
$logged_in = true;
$user_name = auth_name();
$cart_count = array_sum($_SESSION['cart'] ?? []);
$user_id = auth_user()['id'] ?? null;

$feedback = '';
$error = '';

try {
    $pdo = db_connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $targetId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

        if ($action === 'change_role' && $targetId && isset($_POST['role'])) {
            $role = $_POST['role'] === 'seller' ? 'seller' : 'customer';
            if ($targetId === $user_id) {
                $error = 'You cannot change your own role here.';
            } else {
                $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
                $stmt->execute([$role, $targetId]);
                $feedback = 'User role updated successfully.';
            }
        }
        if ($action === 'delete_user' && $targetId) {
            if ($targetId === $user_id) {
                $error = 'You cannot delete your own account.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
                $stmt->execute([$targetId]);
                $feedback = 'User deleted successfully.';
            }
        }
    }

    $stmt = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
    $error = 'Unable to load user list right now.';
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
    <div class="section">
      <div class="container">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
      <div>
        <p style="color:var(--accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">Manage Users</p>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:700;">User accounts and permissions</h1>
        <p style="color:var(--muted);font-size:0.95rem;margin-top:0.5rem;">View, update roles, and remove user accounts from your store.</p>
      </div>
      <a href="dashboard.php" class="btn btn-ghost">← Back to Dashboard</a>
    </div>

    <?php if ($feedback): ?>
      <div class="card" style="border-color:var(--success);background:#e6f8ef;color:#0f5132;margin-bottom:1rem;">
        <div class="card-body" style="padding:1rem;">
          <?= htmlspecialchars($feedback) ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="card" style="border-color:var(--danger);background:#fde8e8;color:#86181d;margin-bottom:1rem;">
        <div class="card-body" style="padding:1rem;">
          <?= htmlspecialchars($error) ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Role</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="5" style="text-align:center;color:var(--muted);">No users found.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($users as $userItem): ?>
              <tr>
                <td><?= htmlspecialchars($userItem['name']) ?></td>
                <td><?= htmlspecialchars($userItem['email']) ?></td>
                <td><?= ucfirst(htmlspecialchars($userItem['role'])) ?></td>
                <td><?= htmlspecialchars(date('M d, Y', strtotime($userItem['created_at']))) ?></td>
                <td>
                  <form method="POST" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                    <input type="hidden" name="user_id" value="<?= $userItem['id'] ?>" />
                    <select name="role" style="padding:0.5rem;border:1px solid var(--border);border-radius:0.5rem;">
                      <option value="customer" <?= $userItem['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                      <option value="seller" <?= $userItem['role'] === 'seller' ? 'selected' : '' ?>>Seller</option>
                    </select>
                    <button type="submit" name="action" value="change_role" class="btn btn-ghost btn-sm">Update</button>
                    <?php if ($userItem['id'] !== $user_id): ?>
                      <button type="submit" name="action" value="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?');">Delete</button>
                    <?php endif; ?>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
