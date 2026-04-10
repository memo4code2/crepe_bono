<?php
session_start();
require 'db.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}

// Prevent back button access after logout
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $stmt = $conn->prepare('SELECT password_hash FROM admins WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($passwordHash);
        if ($stmt->fetch() && password_verify($password, $passwordHash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $stmt->close();
            header('Location: admin_dashboard.php');
            exit;
        }
        $stmt->close();
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Crepe Bono</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <div class="logo">Crepe <span>Bono</span></div>
    <nav>
        <a href="index.html">Home</a>
        <a href="menu.html">Menu</a>
        <a href="cart.html">Cart</a>
        <a href="admin_login.php" class="order-btn">Admin Login</a>
    </nav>
</header>
<section class="checkout">
    <h2>Admin Login</h2>
    <div class="checkout-container">
        <form class="checkout-form" action="admin_login.php" method="POST">
            <?php if ($error !== ''): ?>
                <p style="color:red; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
    </div>
</section>
<footer>
    <p>© 2026 Crepe Bono</p>
</footer>
<script src="script.js"></script>
</body>
</html>
