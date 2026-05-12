<?php
$page_title   = 'ShopNest – My Cart';
$current_page = 'cart';
$root_path    = '../';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';
$logged_in    = auth_is_logged_in();
$user_name    = auth_name();

if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    cart_add(intval($_GET['id']), 1);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = intval($_POST['product_id'] ?? 0);

    if ($action === 'increase' && $productId > 0) {
        cart_add($productId, 1);
    }
    if ($action === 'decrease' && $productId > 0) {
        $currentQty = cart_session()[$productId] ?? 0;
        cart_set($productId, max(0, $currentQty - 1));
    }
    if ($action === 'remove' && $productId > 0) {
        cart_remove($productId);
    }
}

$cart_items = cart_items();
$cart_count = cart_count();
$subtotal     = array_sum(array_map(fn($i) => $i['unit_price'] * $i['qty'], $cart_items));
$shipping     = ($subtotal >= 1000 && $subtotal > 0) ? 0 : 99;
$promo_disc   = 0;
$vat          = round($subtotal * 0.12);
$total        = $subtotal + $shipping - $promo_disc + $vat;

$promo_code   = isset($_POST['promo']) ? strtoupper(trim($_POST['promo'])) : '';
if ($promo_code === 'SHOPNEST10') {
  $promo_disc = round($subtotal * 0.10);
  $total      = $subtotal + $shipping - $promo_disc + $vat;
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

<?php include '../includes/navbar.php'; ?>

<!-- PAGE HEADER -->
<div style="background:var(--dark);padding:2.5rem 0;">
  <div class="container">
    <p style="color:var(--accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">Checkout</p>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:700;color:#fff;">My Shopping Cart</h1>
    <p style="color:rgba(255,255,255,0.55);font-size:0.875rem;margin-top:0.25rem;">
      <?= $cart_count ?> item<?= $cart_count !== 1 ? 's' : '' ?> in your cart
    </p>
  </div>
</div>

<div class="section">
  <div class="container">

    <!-- STEPS -->
    <div style="display:flex;align-items:center;gap:0;margin-bottom:2.5rem;background:var(--card);border-radius:50px;border:1px solid var(--border);padding:0.4rem;max-width:500px;">
      <div style="flex:1;text-align:center;background:var(--accent);color:#fff;border-radius:50px;padding:0.5rem;font-size:0.82rem;font-weight:600;">1 · Cart</div>
      <div style="flex:1;text-align:center;color:var(--muted);padding:0.5rem;font-size:0.82rem;font-weight:600;">2 · Checkout</div>
      <div style="flex:1;text-align:center;color:var(--muted);padding:0.5rem;font-size:0.82rem;font-weight:600;">3 · Confirmation</div>
    </div>

    <div class="cart-layout">

      <!-- CART ITEMS -->
      <div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Cart Items (<?= $cart_count ?>)</h3>
            <a href="products.php" style="font-size:0.82rem;color:var(--accent);">+ Continue Shopping</a>
          </div>
          <div class="card-body">
            <?php if (empty($cart_items)): ?>
              <div style="text-align:center;padding:3rem 1rem;color:var(--muted);">
                <div style="font-size:3rem;margin-bottom:1rem;">🛒</div>
                <div style="font-weight:600;font-size:1rem;margin-bottom:0.5rem;">Your cart is empty</div>
                <div style="font-size:0.85rem;">Browse our products and add items to your cart.</div>
                <a href="products.php" class="btn btn-primary mt-2">Shop Now</a>
              </div>
            <?php else: ?>
              <?php foreach ($cart_items as $index => $item): ?>
                <div class="cart-item">
                  <div class="cart-item-img"><?= $item['icon'] ?></div>
                  <div>
                    <div class="cart-item-cat"><?= htmlspecialchars($item['category']) ?></div>
                    <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="qty-control">
                      <form method="POST" action="cart.php" style="display:inline;">
                        <input type="hidden" name="action" value="decrease" />
                        <input type="hidden" name="product_id" value="<?= intval($item['id']) ?>" />
                        <button type="submit" class="qty-btn">−</button>
                      </form>
                      <span class="qty-num"><?= $item['qty'] ?></span>
                      <form method="POST" action="cart.php" style="display:inline;">
                        <input type="hidden" name="action" value="increase" />
                        <input type="hidden" name="product_id" value="<?= intval($item['id']) ?>" />
                        <button type="submit" class="qty-btn">+</button>
                      </form>
                    </div>
                  </div>
                  <div class="cart-item-price">
                    <div class="item-total">₱<?= number_format($item['unit_price'] * $item['qty']) ?></div>
                    <div class="item-unit">₱<?= number_format($item['unit_price']) ?> each</div>
                    <form method="POST" action="cart.php">
                      <input type="hidden" name="action" value="remove" />
                      <input type="hidden" name="product_id" value="<?= intval($item['id']) ?>" />
                      <button type="submit" class="remove-btn" title="Remove item">🗑</button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- PROMO -->
        <div class="card mt-2">
          <div class="card-body">
            <form method="POST" action="cart.php" style="display:flex;gap:1rem;">
              <input type="text" name="promo" class="form-control"
                placeholder="Enter promo code (e.g. SHOPNEST10)"
                value="<?= htmlspecialchars($promo_code) ?>" />
              <button type="submit" class="btn btn-outline" style="white-space:nowrap;">Apply Code</button>
            </form>
            <?php if ($promo_code === 'SHOPNEST10' && $promo_disc > 0): ?>
              <p style="margin-top:0.75rem;color:var(--success);font-size:0.85rem;font-weight:600;">
                ✅ Promo applied! You saved ₱<?= number_format($promo_disc) ?>.
              </p>
            <?php elseif ($promo_code && $promo_code !== 'SHOPNEST10'): ?>
              <p style="margin-top:0.75rem;color:var(--danger);font-size:0.85rem;">Invalid promo code.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- ORDER SUMMARY -->
      <div>
        <div class="card order-summary-card">
          <div class="card-header">
            <h3 class="card-title">Order Summary</h3>
          </div>
          <div class="card-body">
            <div class="summary-row">
              <span>Subtotal (<?= $cart_count ?> item<?= $cart_count !== 1 ? 's' : '' ?>)</span>
              <span>₱<?= number_format($subtotal) ?></span>
            </div>
            <div class="summary-row">
              <span>Shipping Fee</span>
              <span style="color:var(--success);"><?= $shipping === 0 ? 'Free' : '₱' . number_format($shipping) ?></span>
            </div>
            <div class="summary-row">
              <span>Promo Discount</span>
              <span style="color:var(--danger);">−₱<?= number_format($promo_disc) ?></span>
            </div>
            <div class="summary-row">
              <span>VAT (12%)</span>
              <span>₱<?= number_format($vat) ?></span>
            </div>
            <div class="summary-row total">
              <span>Total</span>
              <span>₱<?= number_format($total) ?></span>
            </div>

            <a href="order-summary.php" class="btn btn-primary btn-full btn-lg mt-2">
              Proceed to Checkout →
            </a>
            <a href="products.php" class="btn btn-ghost btn-full mt-1" style="text-align:center;">
              ← Continue Shopping
            </a>

            <hr class="divider" />
            <div style="font-size:0.78rem;color:var(--muted);text-align:center;line-height:1.8;">
              🔒 Secure Checkout &nbsp;|&nbsp; 🚚 Free Shipping above ₱1,000<br>
              ↩️ 30-day Easy Returns
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>