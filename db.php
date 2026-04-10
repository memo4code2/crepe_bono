<?php
$conn = new mysqli("localhost", "root", "", "crepe_bono");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$createContactTable = "CREATE TABLE IF NOT EXISTS contact (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    Message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$createOrdersTable = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    payment VARCHAR(50) NOT NULL DEFAULT 'cash',
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$createOrderItemsTable = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) UNSIGNED NOT NULL,
    product_id INT(11) UNSIGNED NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$createAdminTable = "CREATE TABLE IF NOT EXISTS admins (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$conn->query($createContactTable) || !$conn->query($createOrdersTable) || !$conn->query($createOrderItemsTable) || !$conn->query($createAdminTable)) {
    die("Database setup failed: " . $conn->error);
}

$result = $conn->query("SHOW COLUMNS FROM order_items LIKE 'product_id'");
if ($result && $result->num_rows === 0) {
    $conn->query("ALTER TABLE order_items ADD COLUMN product_id INT(11) UNSIGNED NULL AFTER order_id");
}

$defaultAdmin = 'mo';
$defaultPassword = '0246';
$checkAdmin = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$checkAdmin->bind_param('s', $defaultAdmin);
$checkAdmin->execute();
$checkAdmin->store_result();
if ($checkAdmin->num_rows === 0) {
    $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
    $insertAdmin = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
    $insertAdmin->bind_param('ss', $defaultAdmin, $passwordHash);
    $insertAdmin->execute();
    $insertAdmin->close();
}
$checkAdmin->close();
?>
