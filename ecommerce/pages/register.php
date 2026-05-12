<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (auth_is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$register_error = '';
$first_name = '';
$last_name = '';
$email = '';
$phone = '';
$address = '';
$role = 'customer';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');
    $role = trim($_POST['role'] ?? 'customer');

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '' || $confirm === '') {
        $register_error = 'Please complete all required fields.';
    } elseif ($password !== $confirm) {
        $register_error = 'Passwords do not match.';
    } elseif (!in_array($role, ['customer', 'seller'])) {
        $register_error = 'Invalid role selected.';
    } else {
        try {
            $pdo = db_connect();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([trim($first_name . ' ' . $last_name), $email, $phone ?: null, $address ?: null, $hashed_password, $role]);
            $user_id = $pdo->lastInsertId();
            auth_login([
                'id' => $user_id,
                'name' => trim($first_name . ' ' . $last_name),
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'role' => $role,
            ]);
            header('Location: dashboard.php');
            exit;
        } catch (Exception $e) {
            $register_error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShopNest – Create Account</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

<div class="auth-page">

  <!-- LEFT VISUAL -->
  <div class="auth-visual" style="background:linear-gradient(135deg,#1A1A2E 0%,#2E2E55 100%);">
    <div class="auth-visual-text">
      <div class="auth-visual-icon">🎉</div>
      <div class="auth-visual-title">Join ShopNest Today</div>
      <p class="auth-visual-sub">Get access to exclusive deals, track your orders, and manage your wishlist — all in one place.</p>
      <div style="margin-top:2rem;display:flex;flex-direction:column;gap:0.85rem;text-align:left;">
        <div style="display:flex;gap:0.75rem;align-items:center;color:rgba(255,255,255,0.8);font-size:0.9rem;">
          <span style="color:var(--accent);font-size:1.1rem;">✓</span> Free shipping on first order
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;color:rgba(255,255,255,0.8);font-size:0.9rem;">
          <span style="color:var(--accent);font-size:1.1rem;">✓</span> Exclusive member-only discounts
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;color:rgba(255,255,255,0.8);font-size:0.9rem;">
          <span style="color:var(--accent);font-size:1.1rem;">✓</span> Order tracking & history
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;color:rgba(255,255,255,0.8);font-size:0.9rem;">
          <span style="color:var(--accent);font-size:1.1rem;">✓</span> 30-day easy returns
        </div>
      </div>
    </div>
  </div>

  <!-- RIGHT FORM -->
  <div class="auth-form-wrap" style="overflow-y:auto;">
    <div class="auth-form-box" style="max-width:480px;">

      <div class="auth-logo">Shop<span>Nest</span></div>
      <h2 class="auth-title">Create Your Account</h2>
      <p class="auth-subtitle">Already have an account? <a href="login.php">Sign in</a></p>

      <?php if ($register_error): ?>
        <div class="card" style="border-color:var(--danger);background:#fde8e8;color:#86181d;margin-bottom:1rem;">
          <div class="card-body" style="padding:1rem;">
            <?= htmlspecialchars($register_error) ?>
          </div>
        </div>
      <?php endif; ?>

      <form action="register.php" method="POST">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" placeholder="Juan" value="<?= htmlspecialchars($first_name) ?>" required />
          </div>
          <div class="form-group">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" value="<?= htmlspecialchars($last_name) ?>" required />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="juan@email.com" value="<?= htmlspecialchars($email) ?>" required />
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="tel" name="phone" class="form-control" placeholder="+63 9XX XXX XXXX" value="<?= htmlspecialchars($phone) ?>" />
        </div>

        <div class="form-group">
          <label class="form-label">Shipping Address</label>
          <textarea name="address" class="form-control" rows="2" placeholder="Street, Barangay, City, Province"><?= htmlspecialchars($address) ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Account Type</label>
          <select name="role" class="form-control" required>
            <option value="customer" <?= $role === 'customer' ? 'selected' : '' ?>>Customer</option>
            <option value="seller" <?= $role === 'seller' ? 'selected' : '' ?>>Seller</option>
          </select>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required />
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required />
          </div>
        </div>

        <div style="font-size:0.82rem;color:var(--muted);margin-bottom:1.25rem;display:flex;gap:0.5rem;align-items:flex-start;">
          <input type="checkbox" style="margin-top:3px;" />
          I agree to ShopNest's <a href="#" style="color:var(--accent);">Terms of Service</a> and <a href="#" style="color:var(--accent);">Privacy Policy</a>
        </div>

        <button type="submit" class="btn btn-primary btn-full btn-lg">Create Account →</button>

      </form>

      <div class="auth-switch">
        Already have an account? <a href="login.php">Sign in here</a>
      </div>

    </div>
  </div>

</div>

</body>
</html>
