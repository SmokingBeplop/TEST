<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function cart_session(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_save(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

function cart_add(int $productId, int $qty = 1): void
{
    $cart = cart_session();
    $cart[$productId] = max(1, ($cart[$productId] ?? 0) + $qty);
    cart_save($cart);
}

function cart_set(int $productId, int $qty): void
{
    $cart = cart_session();
    if ($qty <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $qty;
    }
    cart_save($cart);
}

function cart_remove(int $productId): void
{
    $cart = cart_session();
    if (isset($cart[$productId])) {
        unset($cart[$productId]);
        cart_save($cart);
    }
}

function cart_count(): int
{
    return array_sum(cart_session());
}

function cart_get_product(int $productId): ?array
{
    try {
        $pdo = db_connect();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();
        if ($product) {
            return $product;
        }
    } catch (Exception $e) {
        // ignore and fallback to sample data
    }

    $fallback = [
        1 => ['icon' => '📱', 'badge' => 'Sale', 'category' => 'Electronics', 'name' => 'Smartphone Pro X12', 'unit_price' => 12999, 'price' => 12999.00, 'original_price' => 16500.00, 'reviews' => '⭐⭐⭐⭐⭐ (142 reviews)', 'in_stock' => 1],
        2 => ['icon' => '👟', 'badge' => '', 'category' => 'Footwear', 'name' => 'AirRun Classic Sneakers', 'unit_price' => 2499, 'price' => 2499.00, 'original_price' => null, 'reviews' => '⭐⭐⭐⭐☆ (87 reviews)', 'in_stock' => 1],
        3 => ['icon' => '💻', 'badge' => 'New', 'category' => 'Electronics', 'name' => 'UltraBook Slim 15"', 'unit_price' => 34500, 'price' => 34500.00, 'original_price' => 38000.00, 'reviews' => '⭐⭐⭐⭐⭐ (56 reviews)', 'in_stock' => 1],
        4 => ['icon' => '⌚', 'badge' => '', 'category' => 'Accessories', 'name' => 'SmartWatch Series 7', 'unit_price' => 4299, 'price' => 4299.00, 'original_price' => null, 'reviews' => '⭐⭐⭐⭐☆ (203 reviews)', 'in_stock' => 1],
        5 => ['icon' => '🎧', 'badge' => 'Hot', 'category' => 'Electronics', 'name' => 'Wireless Earbuds Pro V3', 'unit_price' => 1899, 'price' => 1899.00, 'original_price' => 2499.00, 'reviews' => '⭐⭐⭐⭐⭐ (319 reviews)', 'in_stock' => 1],
        6 => ['icon' => '👜', 'badge' => '', 'category' => 'Fashion', 'name' => 'Genuine Leather Handbag', 'unit_price' => 3750, 'price' => 3750.00, 'original_price' => null, 'reviews' => '⭐⭐⭐⭐☆ (74 reviews)', 'in_stock' => 1],
        7 => ['icon' => '🖱️', 'badge' => '', 'category' => 'Electronics', 'name' => 'Gaming Mouse RGB 16000dpi', 'unit_price' => 1250, 'price' => 1250.00, 'original_price' => null, 'reviews' => '⭐⭐⭐⭐⭐ (198 reviews)', 'in_stock' => 1],
        8 => ['icon' => '🌿', 'badge' => '', 'category' => 'Beauty', 'name' => 'Organic Skincare Set', 'unit_price' => 899, 'price' => 899.00, 'original_price' => null, 'reviews' => '⭐⭐⭐⭐☆ (45 reviews)', 'in_stock' => 1],
    ];

    return $fallback[$productId] ?? null;
}

function cart_items(): array
{
    $items = [];
    foreach (cart_session() as $productId => $qty) {
        $product = cart_get_product($productId);
        if ($product === null) {
            continue;
        }

        $unitPrice = $product['price'] ?? $product['unit_price'] ?? 0;
        $items[] = [
            'id' => $productId,
            'icon' => $product['icon'] ?? '📦',
            'category' => $product['category'] ?? 'Product',
            'name' => $product['name'] ?? 'Product',
            'unit_price' => floatval($unitPrice),
            'qty' => $qty,
        ];
    }

    return $items;
}
