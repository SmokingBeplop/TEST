<?php
$page_title   = 'ShopNest – Order Summary';
$current_page = 'orders';
$root_path    = '../';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';
$logged_in    = auth_is_logged_in();
$user_name    = auth_name();
$cart_count   = cart_count();

// --- Order items from the session cart ---
$order_items = cart_items();

$subtotal   = array_sum(array_map(fn($i) => $i['unit_price'] * $i['qty'], $order_items));
$shipping   = 0;
$promo_disc = 0;
$vat        = round($subtotal * 0.12);
$total      = $subtotal + $shipping - $promo_disc + $vat;
$savings    = $promo_disc;

// --- Past orders (replace with DB query) ---
$past_orders = [
  ['id' => '#ORD-0046', 'total' => '₱4,299',  'status' => 'Delivered', 'badge' => 'badge-success'],
  ['id' => '#ORD-0038', 'total' => '₱7,850',  'status' => 'Delivered', 'badge' => 'badge-success'],
  ['id' => '#ORD-0031', 'total' => '₱2,340',  'status' => 'Cancelled', 'badge' => 'badge-danger'],
];

$order_placed = false;
if (isset($_POST['place_order']) && !empty($order_items)) {
    $order_placed = true;
    cart_save([]);
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
<?php
$header_title = $order_placed ? 'Order Confirmed' : 'Checkout &amp; Order Summary';
$header_subtitle = $order_placed ? 'Thank you! Your order has been successfully placed.' : 'Review your order before placing it';
?>

<div style="background:var(--dark);padding:2.5rem 0;">
  <div class="container">
    <p style="color:var(--accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">Almost There!</p>
    <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:700;color:#fff;"><?= $header_title ?></h1>
    <p style="color:rgba(255,255,255,0.55);font-size:0.875rem;margin-top:0.25rem;"><?= $header_subtitle ?></p>
  </div>
</div>

<div class="section">
  <div class="container">

    <!-- STEPS -->
    <div style="display:flex;align-items:center;gap:0;margin-bottom:2.5rem;background:var(--card);border-radius:50px;border:1px solid var(--border);padding:0.4rem;max-width:500px;">
      <div style="flex:1;text-align:center;color:var(--muted);padding:0.5rem;font-size:0.82rem;font-weight:600;">1 · Cart</div>
      <?php if ($order_placed): ?>
        <div style="flex:1;text-align:center;color:var(--muted);padding:0.5rem;font-size:0.82rem;font-weight:600;">2 · Checkout</div>
        <div style="flex:1;text-align:center;background:var(--accent);color:#fff;border-radius:50px;padding:0.5rem;font-size:0.82rem;font-weight:600;">3 · Confirmation</div>
      <?php else: ?>
        <div style="flex:1;text-align:center;background:var(--accent);color:#fff;border-radius:50px;padding:0.5rem;font-size:0.82rem;font-weight:600;">2 · Checkout</div>
        <div style="flex:1;text-align:center;color:var(--muted);padding:0.5rem;font-size:0.82rem;font-weight:600;">3 · Confirmation</div>
      <?php endif; ?>
    </div>

    <?php if ($order_placed): ?>
      <!-- ORDER SUCCESS -->
      <div class="card">
        <div class="card-body order-success">
          <div class="success-icon">🎉</div>
          <h2 class="success-title">Order Placed Successfully!</h2>
          <p class="success-sub">Your order <strong>#ORD-<?= rand(1000, 9999) ?></strong> has been confirmed. A confirmation email has been sent to <strong><?= htmlspecialchars(auth_user_email() ?? 'your email') ?></strong>.</p>
          <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            <a href="products.php" class="btn btn-outline">Continue Shopping</a>
          </div>
        </div>
      </div>
    <?php else: ?>

      <form method="POST" action="order-summary.php">
        <div style="display:grid;grid-template-columns:1fr 400px;gap:2rem;">

          <!-- LEFT: CHECKOUT FORM -->
          <div style="display:flex;flex-direction:column;gap:1.5rem;">

            <!-- SHIPPING INFO -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">📍 Shipping Information</h3>
              </div>
              <div class="card-body">
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control"
                      value="<?= htmlspecialchars($_POST['first_name'] ?? 'Juan') ?>" required />
                  </div>
                  <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control"
                      value="<?= htmlspecialchars($_POST['last_name'] ?? 'Dela Cruz') ?>" required />
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Phone Number</label>
                  <input type="tel" name="phone" class="form-control"
                    value="<?= htmlspecialchars($_POST['phone'] ?? '+63 912 345 6789') ?>" required />
                </div>
                <div class="form-group">
                  <label class="form-label">Complete Shipping Address</label>
                  <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($_POST['address'] ?? '123 Rizal Street, Barangay Centro, Maramag, Bukidnon, 8714') ?></textarea>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">City / Municipality</label>
                    <input type="text" name="city" class="form-control"
                      value="<?= htmlspecialchars($_POST['city'] ?? 'Maramag') ?>" required />
                  </div>
                  <div class="form-group">
                    <label class="form-label">Province</label>
                    <input type="text" name="province" class="form-control"
                      value="<?= htmlspecialchars($_POST['province'] ?? 'Bukidnon') ?>" required />
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Delivery Notes (Optional)</label>
                  <input type="text" name="notes" class="form-control"
                    placeholder="e.g. Leave at the gate, call upon arrival"
                    value="<?= htmlspecialchars($_POST['notes'] ?? '') ?>" />
                </div>
              </div>
            </div>

            <!-- PAYMENT METHOD -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">💳 Payment Method</h3>
              </div>
              <div class="card-body">
                <?php
                $payment_options = [
                  'card'    => '💳 Credit / Debit Card',
                  'gcash'   => '📱 GCash',
                  'banking' => '🏦 Online Banking',
                  'cod'     => '💵 Cash on Delivery (COD)',
                ];
                $selected_pay = $_POST['pay'] ?? 'card';
                ?>
                <div style="display:flex;flex-direction:column;gap:0.75rem;margin-bottom:1.5rem;">
                  <?php foreach ($payment_options as $value => $label): ?>
                    <label style="display:flex;gap:0.75rem;align-items:center;padding:1rem;
                      border:2px solid <?= $selected_pay === $value ? 'var(--accent)' : 'var(--border)' ?>;
                      border-radius:var(--radius-sm);cursor:pointer;
                      background:<?= $selected_pay === $value ? 'rgba(232,98,58,0.04)' : 'transparent' ?>;">
                      <input type="radio" name="pay" value="<?= $value ?>"
                        <?= $selected_pay === $value ? 'checked' : '' ?> />
                      <span style="font-weight:600;"><?= $label ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>

                <?php if ($selected_pay === 'card'): ?>
                  <div style="padding:1.25rem;background:#fafafa;border-radius:var(--radius-sm);border:1px solid var(--border);">
                    <div class="form-group">
                      <label class="form-label">Card Number</label>
                      <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" />
                    </div>
                    <div class="form-row">
                      <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="text" name="card_expiry" class="form-control" placeholder="MM / YY" />
                      </div>
                      <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input type="password" name="card_cvv" class="form-control" placeholder="•••" />
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label">Cardholder Name</label>
                      <input type="text" name="card_name" class="form-control" placeholder="JUAN DELA CRUZ" />
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- ORDER ITEMS REVIEW -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">📦 Items in This Order</h3>
                <a href="cart.php" style="font-size:0.82rem;color:var(--accent);">Edit Cart</a>
              </div>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Qty</th>
                      <th>Unit Price</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($order_items)): ?>
                      <tr>
                        <td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">
                          <div style="font-size:2rem;margin-bottom:0.5rem;">📦</div>
                          <div style="font-weight:600;">No items in this order</div>
                        </td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($order_items as $item): ?>
                        <tr>
                          <td><?= htmlspecialchars($item['name']) ?></td>
                          <td><?= $item['qty'] ?></td>
                          <td>₱<?= number_format($item['unit_price']) ?></td>
                          <td><strong>₱<?= number_format($item['unit_price'] * $item['qty']) ?></strong></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <!-- RIGHT: SUMMARY -->
          <div>
            <div class="card order-summary-card">
              <div class="card-header">
                <h3 class="card-title">Order Total</h3>
              </div>
              <div class="card-body">
                <div class="summary-row">
                  <span>Subtotal</span>
                  <span>₱<?= number_format($subtotal) ?></span>
                </div>
                <div class="summary-row">
                  <span>Shipping</span>
                  <span style="color:var(--success);">Free</span>
                </div>
                <div class="summary-row">
                  <span>Promo</span>
                  <span style="color:var(--danger);">−₱<?= number_format($promo_disc) ?></span>
                </div>
                <div class="summary-row">
                  <span>VAT (12%)</span>
                  <span>₱<?= number_format($vat) ?></span>
                </div>
                <div class="summary-row total">
                  <span>Grand Total</span>
                  <span style="color:var(--accent);">₱<?= number_format($total) ?></span>
                </div>

                <div style="margin:1.25rem 0;padding:0.85rem;background:#d4f0e4;border-radius:var(--radius-sm);font-size:0.82rem;color:var(--success);font-weight:600;">
                  ✅ You save ₱<?= number_format($savings) ?> on this order!
                </div>

                <button type="submit" name="place_order" value="1" class="btn btn-primary btn-full btn-lg" <?= empty($order_items) ? 'disabled' : '' ?>>
                  🛍️ Place Order Now
                </button>
                <a href="cart.php" class="btn btn-ghost btn-full mt-1">← Back to Cart</a>

                <?php if (empty($order_items)): ?>
                  <p style="color:var(--danger);font-size:0.9rem;margin-top:1rem;">Your cart is empty. Add items in the cart before placing an order.</p>
                <?php endif; ?>

                <hr class="divider" />
                <div style="font-size:0.75rem;color:var(--muted);text-align:center;line-height:2;">
                  🔒 SSL Secured Payment<br>
                  📋 Order ID generated upon confirmation<br>
                  📧 Confirmation sent to <?= htmlspecialchars(auth_user_email() ?? 'your email') ?>
                </div>
              </div>
            </div>

            <!-- MY PAST ORDERS -->
            <div class="card mt-2">
              <div class="card-header">
                <h3 class="card-title" style="font-size:0.95rem;">My Past Orders</h3>
              </div>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr><th>Order ID</th><th>Total</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($past_orders as $order): ?>
                      <tr>
                        <td><strong><?= htmlspecialchars($order['id']) ?></strong></td>
                        <td><?= htmlspecialchars($order['total']) ?></td>
                        <td><span class="badge <?= $order['badge'] ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </form>

    <?php endif; ?>

  </div>
</div>

<?php if ($order_placed): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var successCard = document.querySelector('.order-success');
      if (successCard) {
        successCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  </script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>

</body>
</html>