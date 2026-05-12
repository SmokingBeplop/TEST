<footer>
  <div class="container" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;padding:2rem 0;color:rgba(255,255,255,0.75);">
    <div>
      <strong>ShopNest</strong> — modern PHP storefront.
    </div>
    <div style="display:flex;gap:1rem;flex-wrap:wrap;font-size:0.95rem;">
      <?php if (($current_page ?? '') !== 'home' && ($user_role ?? '') !== 'seller'): ?>
        <a href="<?= $root_path ?? './' ?>pages/products.php">Products</a>
        <a href="<?= $root_path ?? './' ?>pages/cart.php">Cart</a>
        <a href="<?= $root_path ?? './' ?>pages/order-summary.php">Orders</a>
      <?php endif; ?>
      <?php if (($user_role ?? '') === 'seller'): ?>
        <!-- No admin link for sellers -->
      <?php else: ?>
        <!-- No admin link for customers either, as per request -->
      <?php endif; ?>
    </div>
  </div>
</footer>
