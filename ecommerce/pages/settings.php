<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!auth_is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user = auth_user();
$user_id = $user['id'] ?? null;
if (!$user_id) {
    header('Location: dashboard.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'ShopNest – Settings';
$current_page = 'settings';
$root_path = '../';
$logged_in = true;
$user_name = auth_name();
$cart_count = array_sum($_SESSION['cart'] ?? []);

$success_message = '';
$error_message = '';
$preferences = $_SESSION['preferences'] ?? ['newsletter' => true, 'sms_alerts' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_preferences') {
        $preferences['newsletter'] = isset($_POST['newsletter']);
        $preferences['sms_alerts'] = isset($_POST['sms_alerts']);
        $_SESSION['preferences'] = $preferences;
        $success_message = 'Settings saved successfully.';
    }

    if ($action === 'change_password') {
        $current = trim($_POST['current_password'] ?? '');
        $new = trim($_POST['new_password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if ($current === '' || $new === '' || $confirm === '') {
            $error_message = 'All password fields are required.';
        } elseif ($new !== $confirm) {
            $error_message = 'New passwords do not match.';
        } else {
            try {
                $pdo = db_connect();
                $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
                $stmt->execute([$user_id]);
                $record = $stmt->fetch();
                if (!$record || !password_verify($current, $record['password'])) {
                    $error_message = 'Current password is incorrect.';
                } else {
                    $updated = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $updated->execute([password_hash($new, PASSWORD_DEFAULT), $user_id]);
                    $success_message = 'Your password has been updated.';
                }
            } catch (Exception $e) {
                $error_message = 'Unable to update password. Please try again later.';
            }
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
  <main class="main-content">
    <div class="section">
      <div class="container">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
      <div>
        <p style="color:var(--accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">Account Settings</p>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:700;">Manage your account preferences</h1>
        <p style="color:var(--muted);font-size:0.95rem;margin-top:0.5rem;">Update security settings and notification preferences.</p>
      </div>
      <a href="dashboard.php" class="btn btn-ghost">← Back to Dashboard</a>
    </div>

    <?php if ($success_message): ?>
      <div class="card" style="border-color:var(--success);background:#e6f8ef;color:#0f5132;margin-bottom:1rem;">
        <div class="card-body" style="padding:1rem;">
          <?= htmlspecialchars($success_message) ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
      <div class="card" style="border-color:var(--danger);background:#fde8e8;color:#86181d;margin-bottom:1rem;">
        <div class="card-body" style="padding:1rem;">
          <?= htmlspecialchars($error_message) ?>
        </div>
      </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;">
      <div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Notification Preferences</h3>
          </div>
          <div class="card-body">
            <form method="POST" action="settings.php">
              <input type="hidden" name="action" value="save_preferences" />
              <div class="form-group" style="display:flex;align-items:center;gap:0.75rem;">
                <input type="checkbox" id="newsletter" name="newsletter" <?= $preferences['newsletter'] ? 'checked' : '' ?> />
                <label for="newsletter" class="form-label">Receive promotional emails</label>
              </div>
              <div class="form-group" style="display:flex;align-items:center;gap:0.75rem;">
                <input type="checkbox" id="sms_alerts" name="sms_alerts" <?= $preferences['sms_alerts'] ? 'checked' : '' ?> />
                <label for="sms_alerts" class="form-label">Receive SMS order alerts</label>
              </div>
              <button type="submit" class="btn btn-primary btn-full btn-lg">Save Preferences</button>
            </form>
          </div>
        </div>

        <div class="card mt-3">
          <div class="card-header">
            <h3 class="card-title">Security</h3>
          </div>
          <div class="card-body">
            <form method="POST" action="settings.php">
              <input type="hidden" name="action" value="change_password" />
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required />
              </div>
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required />
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required />
              </div>
              <button type="submit" class="btn btn-primary btn-full btn-lg">Update Password</button>
            </form>
          </div>
        </div>
      </div>

      <div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Account Details</h3>
          </div>
          <div style="padding:1.5rem;">
            <div style="margin-bottom:1rem;">
              <strong>Email</strong>
              <div style="color:var(--muted);font-size:0.95rem;"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <div style="margin-bottom:1rem;">
              <strong>Account Type</strong>
              <div style="color:var(--muted);font-size:0.95rem;"><?= ucfirst(htmlspecialchars($user['role'])) ?></div>
            </div>
            <div style="margin-bottom:1rem;">
              <strong>Notification Status</strong>
              <div style="color:var(--muted);font-size:0.95rem;"><?= $preferences['newsletter'] ? 'Email enabled' : 'Email disabled' ?>, <?= $preferences['sms_alerts'] ? 'SMS enabled' : 'SMS disabled' ?></div>
            </div>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
              <a href="profile.php" class="btn btn-outline btn-sm">Edit Profile</a>
              <a href="notifications.php" class="btn btn-outline btn-sm">View Notifications</a>
            </div>
          </div>
        </div>
      </div>
    </div>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
