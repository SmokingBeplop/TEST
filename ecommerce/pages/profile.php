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

$page_title = 'ShopNest – Profile';
$current_page = 'profile';
$root_path = '../';
$logged_in = true;
$user_name = auth_name();
$user_initial = auth_profile_initial();
$user_role = auth_role();
$cart_count = array_sum($_SESSION['cart'] ?? []);

$profile_error = '';
$profile_success = false;

try {
    $pdo = db_connect();
    $stmt = $pdo->prepare('SELECT id, name, email, phone, address, role FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $profile_user = $stmt->fetch();
    if (!$profile_user) {
        throw new RuntimeException('User not found.');
    }
} catch (Exception $e) {
    $profile_error = 'Unable to load profile data. Please try again later.';
    $profile_user = [
        'name' => auth_name(),
        'email' => auth_user_email(),
        'phone' => $user['phone'] ?? '',
        'address' => $user['address'] ?? '',
        'role' => $user_role,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '' || $email === '') {
        $profile_error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profile_error = 'Enter a valid email address.';
    } else {
        try {
            $check = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $check->execute([$email, $user_id]);
            if ($check->fetch()) {
                $profile_error = 'That email is already registered by another account.';
            } else {
                $update = $pdo->prepare('UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?');
                $update->execute([$name, $email, $phone ?: null, $address ?: null, $user_id]);

                auth_login([
                    'id' => $user_id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'role' => $user_role,
                ]);

                $profile_success = true;
                $profile_user['name'] = $name;
                $profile_user['email'] = $email;
                $profile_user['phone'] = $phone;
                $profile_user['address'] = $address;
                $user_name = auth_name();
            }
        } catch (Exception $e) {
            $profile_error = 'Unable to save profile. Please try again later.';
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
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
      <div>
        <p style="color:var(--accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">My Profile</p>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:700;">Your personal account details</h1>
        <p style="color:var(--muted);font-size:0.95rem;margin-top:0.5rem;">Update your contact information and keep your account information current.</p>
      </div>
      <a href="dashboard.php" class="btn btn-ghost">← Back to Dashboard</a>
    </div>

    <?php if ($profile_error): ?>
      <div class="card" style="border-color:var(--danger);background:#fde8e8;color:#86181d;margin-bottom:1rem;">
        <div class="card-body" style="padding:1rem;">
          <?= htmlspecialchars($profile_error) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($profile_success): ?>
      <div class="card" style="border-color:var(--success);background:#e6f8ef;color:#0f5132;margin-bottom:1rem;">
        <div class="card-body" style="padding:1rem;">
          Your profile has been updated successfully.
        </div>
      </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;">
      <div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Edit Personal Details</h3>
          </div>
          <div class="card-body">
            <form method="POST" action="profile.php">
              <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($profile_user['name'] ?? '') ?>" required />
              </div>
              <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profile_user['email'] ?? '') ?>" required />
              </div>
              <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($profile_user['phone'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($profile_user['address'] ?? '') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Account Type</label>
                <input type="text" class="form-control" value="<?= ucfirst(htmlspecialchars($profile_user['role'])) ?>" readonly />
              </div>
              <button type="submit" class="btn btn-primary btn-full btn-lg">Save Changes</button>
            </form>
          </div>
        </div>
      </div>

      <div>
        <div class="card" style="background:var(--card);">
          <div class="card-header">
            <h3 class="card-title">Account Essentials</h3>
          </div>
          <div class="card-body" style="padding:1.5rem;">
            <?php if ($profile_user['role'] === 'seller'): ?>
              <p style="margin-bottom:1rem;">As a seller, keep these details up to date:</p>
              <ul style="margin:0;padding-left:1.25rem;line-height:1.9;">
                <li>Business contact email for customer inquiries and payout notifications.</li>
                <li>Phone number so buyers can reach you for order updates.</li>
                <li>Address for shipping and pickup information.</li>
                <li>Keep your product listings, stock levels, and prices current.</li>
              </ul>
            <?php else: ?>
              <p style="margin-bottom:1rem;">As a customer, your profile helps with:</p>
              <ul style="margin:0;padding-left:1.25rem;line-height:1.9;">
                <li>Accurate shipping address for fast delivery.</li>
                <li>Valid email for order confirmations and updates.</li>
                <li>Phone number for delivery notifications.</li>
                <li>Access to your order history and saved preferences.</li>
              </ul>
            <?php endif; ?>
          </div>
        </div>

        <div class="card mt-3">
          <div class="card-header">
            <h3 class="card-title">Profile Summary</h3>
          </div>
          <div style="padding:1.5rem;">
            <div style="display:flex;gap:1rem;align-items:center;margin-bottom:1rem;">
              <div style="width:50px;height:50px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;color:#fff;">
                <?= htmlspecialchars($user_initial) ?>
              </div>
              <div>
                <div style="font-weight:700;"><?= htmlspecialchars($profile_user['name'] ?? '') ?></div>
                <div style="color:var(--muted);font-size:0.9rem;"><?= ucfirst(htmlspecialchars($profile_user['role'] ?? 'customer')) ?></div>
              </div>
            </div>
            <div style="margin-bottom:0.75rem;">
              <strong>Email</strong>
              <div style="color:var(--muted);font-size:0.95rem;"><?= htmlspecialchars($profile_user['email'] ?? '') ?></div>
            </div>
            <div style="margin-bottom:0.75rem;">
              <strong>Phone</strong>
              <div style="color:var(--muted);font-size:0.95rem;"><?= htmlspecialchars($profile_user['phone'] ?? 'Not set') ?></div>
            </div>
            <div>
              <strong>Address</strong>
              <div style="color:var(--muted);font-size:0.95rem;"><?= nl2br(htmlspecialchars($profile_user['address'] ?? 'Not set')) ?></div>
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
