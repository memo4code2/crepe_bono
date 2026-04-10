<?php
include "db.php";

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$payment = trim($_POST['payment'] ?? 'cash');
$cart = json_decode($_POST['cart_data'] ?? '[]', true);

$total = 0;
if (!is_array($cart)) {
    $cart = [];
}

foreach ($cart as $item) {
    $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
    $price = isset($item['price']) ? (float)$item['price'] : 0.0;
    $total += $price * $quantity;
}

$errorMessage = '';
if ($name === '' || $phone === '' || $address === '') {
    $errorMessage = 'Please complete all required fields.';
} elseif (count($cart) === 0) {
    $errorMessage = 'Your cart is empty. Add items before placing an order.';
}

if ($errorMessage !== '') {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Order Error</title><link rel='stylesheet' href='style.css'></head><body><header class='navbar'><div class='logo'>Crepe <span>Bono</span></div><nav><a href='index.html'>Home</a><a href='menu.html'>Menu</a><a href='cart.html'>Cart</a></nav></header><section class='checkout'><h2>Order Error</h2><div class='checkout-container'><p style='color:red;'>" . htmlspecialchars($errorMessage) . "</p><a href='checkout.html' class='btn'>Back to Checkout</a></div></section><footer><p>© 2026 Crepe Bono</p></footer></body></html>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO orders (name, phone, address, payment, total_price) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ssssd", $name, $phone, $address, $payment, $total);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $stmt->close();

    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
    if (!$itemStmt) {
        die("Prepare failed: " . $conn->error);
    }

    foreach ($cart as $item) {
        $product_id = isset($item['product_id']) ? (int)$item['product_id'] : null;
        $pname = $item['name'];
        $price = (float)$item['price'];
        $qty = (int)$item['quantity'];
        $itemStmt->bind_param("iisdi", $order_id, $product_id, $pname, $price, $qty);
        $itemStmt->execute();
    }
    $itemStmt->close();

    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Order Confirmed</title><link rel='stylesheet' href='style.css'></head><body><header class='navbar'><div class='logo'>Crepe <span>Bono</span></div><nav><a href='index.html'>Home</a><a href='menu.html'>Menu</a><a href='cart.html'>Cart</a></nav></header><section class='checkout'><h2>Order Placed Successfully ✅</h2><div class='checkout-container'><p>Thank you, " . htmlspecialchars($name) . "!</p><p>Your order #" . $order_id . " has been placed.</p><p>Total: " . number_format($total, 2) . " EGP</p><a href='menu.html' class='btn'>Continue Shopping</a></div></section><footer><p>© 2026 Crepe Bono</p><script>localStorage.removeItem('cart');</script></footer></body></html>";
} else {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Order Error</title><link rel='stylesheet' href='style.css'></head><body><header class='navbar'><div class='logo'>Crepe <span>Bono</span></div><nav><a href='index.html'>Home</a><a href='menu.html'>Menu</a><a href='cart.html'>Cart</a></nav></header><section class='checkout'><h2>Order Error</h2><div class='checkout-container'><p style='color:red;'>Error placing order: " . htmlspecialchars($conn->error) . "</p><a href='checkout.html' class='btn'>Back to Checkout</a></div></section><footer><p>© 2026 Crepe Bono</p></footer></body></html>";
}
?>