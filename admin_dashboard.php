<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Prevent back button access after logout
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$adminUsername = htmlspecialchars($_SESSION['admin_username'] ?? 'Admin');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_username'], $_POST['new_password'])) {
    $newUsername = trim($_POST['new_username']);
    $newPassword = trim($_POST['new_password']);

    if ($newUsername === '' || $newPassword === '') {
        $message = 'Please fill in both username and password.';
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM admins WHERE username = ?');
        $checkStmt->bind_param('s', $newUsername);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $message = 'That admin username already exists.';
        } else {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
            $insertStmt->bind_param('ss', $newUsername, $passwordHash);
            if ($insertStmt->execute()) {
                $message = 'New admin created successfully.';
            } else {
                $message = 'Unable to create new admin.';
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}

$orders = [];
$orderResult = $conn->query(
    'SELECT o.id AS order_id, o.name, o.phone, o.address, o.payment, o.total_price, o.created_at AS order_date, '
    . 'oi.product_id, oi.product_name, oi.price AS item_price, oi.quantity '
    . 'FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id '
    . 'ORDER BY o.created_at DESC, oi.id ASC'
);
if ($orderResult) {
    while ($row = $orderResult->fetch_assoc()) {
        $orderId = $row['order_id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'name' => $row['name'],
                'phone' => $row['phone'],
                'address' => $row['address'],
                'payment' => $row['payment'],
                'total_price' => $row['total_price'],
                'order_date' => $row['order_date'],
                'items' => []
            ];
        }
        if ($row['product_name'] !== null) {
            $orders[$orderId]['items'][] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'price' => $row['item_price'],
                'quantity' => $row['quantity']
            ];
        }
    }
}

$messageResult = $conn->query('SELECT * FROM contact ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Crepe Bono</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <div class="logo">Crepe <span>Bono</span></div>
    <nav>
        <a href="admin_dashboard.php" class="active">Dashboard</a>
        <a href="admin_login.php">Login</a>
        <a href="admin_logout.php" class="order-btn">Logout</a>
    </nav>
</header>
<section class="checkout">
    <h2>Admin Dashboard</h2>
    <div class="checkout-container">
        <div class="checkout-summary" style="width: 320px;">
            <h3>Welcome, <?php echo $adminUsername; ?></h3>
            <p><strong>Orders:</strong> <?php echo count($orders); ?></p>
            <p><strong>Messages:</strong> <?php echo $messageResult ? $messageResult->num_rows : 0; ?></p>
            <a href="admin_dashboard.php?show_add=1" class="btn">Add Admin</a>
            <?php if ($message !== ''): ?>
                <p style="color: #FFD700; margin-top: 15px;"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        </div>
        <div class="checkout-form" style="width: 640px; text-align: left;">
            <?php if (isset($_GET['show_add'])): ?>
                <h3>Add Admin Account</h3>
                <form action="admin_dashboard.php" method="POST">
                    <input type="text" name="new_username" placeholder="New admin username" required>
                    <input type="password" name="new_password" placeholder="New admin password" required>
                    <button type="submit">Create Admin</button>
                </form>
                <hr>
            <?php endif; ?>
            <h3>Recent Orders</h3>
            <?php if (count($orders) === 0): ?>
                <p>No orders found.</p>
            <?php else: ?>
                <?php foreach ($orders as $id => $order): ?>
                    <div class="cart-item">
                        <h4>Order #<?php echo $id; ?> - <?php echo htmlspecialchars($order['order_date']); ?></h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                        <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment']); ?></p>
                        <p><strong>Total:</strong> <?php echo number_format($order['total_price'], 2); ?> EGP</p>
                        <p><strong>Items:</strong></p>
                        <ul>
                            <?php foreach ($order['items'] as $item): ?>
                                <li><?php echo htmlspecialchars($item['product_name']); ?> x<?php echo $item['quantity']; ?> - <?php echo number_format($item['price'], 2); ?> EGP</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<section class="checkout" style="padding-top: 0;">
    <h3>Contact Messages</h3>
    <div class="checkout-container">
        <div class="checkout-summary" style="width: 100%;">
            <?php if (!$messageResult || $messageResult->num_rows === 0): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($msg = $messageResult->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($msg['Name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($msg['Email']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($msg['Message']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($msg['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>
<footer>
    <p>© 2026 Crepe Bono</p>
</footer>
<script src="script.js"></script>
</body>
</html>
