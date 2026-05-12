<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (auth_is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$login_error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $login_error = 'Please enter both email and password.';
    } else {
        try {
            $pdo = db_connect();
            $stmt = $pdo->prepare('SELECT id, name, email, phone, address, password, role FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                auth_login([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'address' => $user['address'],
                    'role' => $user['role'],
                ]);
                header('Location: dashboard.php');
                exit;
            } else {
                $login_error = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $login_error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShopNest – Sign In</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>

<div class="auth-page">

  <!-- LEFT VISUAL -->
  <div class="auth-visual">
    <div class="auth-visual-text">
      <div class="auth-visual-icon">🛍️</div>
      <div class="auth-visual-title">Welcome Back to ShopNest</div>
      <p class="auth-visual-sub">Your favorite online store with thousands of products curated just for you.</p>
    </div>
  </div>

  <!-- RIGHT FORM -->
  <div class="auth-form-wrap">
    <div class="auth-form-box">

      <div class="auth-logo">Shop<span>Nest</span></div>
      <h2 class="auth-title">Sign In to Your Account</h2>
      <p class="auth-subtitle">Don't have an account? <a href="register.php">Create one free</a></p>

      <?php if ($login_error): ?>
        <div class="card" style="border-color:var(--danger);background:#fde8e8;color:#86181d;margin-bottom:1rem;">
          <div class="card-body" style="padding:1rem;"><?= htmlspecialchars($login_error) ?></div>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="juan.delacruz@email.com" value="<?= htmlspecialchars($email) ?>" required />
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter your password" required />
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;font-size:0.85rem;">
          <label style="display:flex;gap:0.5rem;align-items:center;cursor:pointer;color:var(--muted);">
            <input type="checkbox" checked /> Remember me
          </label>
          <a href="#" style="color:var(--accent);font-weight:600;">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-full btn-lg">Sign In →</button>

      </form>

      <hr class="divider" />

      <div style="text-align:center;font-size:0.82rem;color:var(--muted);">
        Or continue with
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-top:1rem;">
        <button class="btn btn-ghost">🇬 Google</button>
        <button class="btn btn-ghost">𝕗 Facebook</button>
      </div>

      <div class="auth-switch">
        New to ShopNest? <a href="register.php">Create an account</a>
      </div>

    </div>
  </div>

</div>

</body>
</html>
