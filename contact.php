
<?php
require('db.php');

$formFeedback = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['Name'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $message = trim($_POST['Message'] ?? '');

    if ($name !== '' && $email !== '' && $message !== '') {
        $sql = "INSERT INTO contact (id, Name, Email, Message) VALUES (NULL, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $name, $email, $message);
            if ($stmt->execute()) {
                $formFeedback = "<p style='color:green;'>Message sent successfully ✅</p>";
            } else {
                $formFeedback = "<p style='color:red;'>Database error: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        } else {
            $formFeedback = "<p style='color:red;'>Database prepare error: " . htmlspecialchars($conn->error) . "</p>";
        }
    } else {
        $formFeedback = "<p style='color:red;'>Please fill in all fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Crepe Bono</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<header class="navbar">
    <div class="logo">Crepe <span>Bono</span></div>

    <nav>
        <a href="index.html">Home</a>
        <a href="menu.html">Menu</a>
        <a href="about.html">About</a>
        <a href="contact.php" class="active">Contact</a>
        <a href="admin_login.php">Admin</a>
        <a href="cart.html" class="order-btn">Cart <span class="cart-badge" id="cart-count">0</span></a>
    </nav>
</header>

<!-- Contact Section -->
<section class="contact">

    <h2>Contact Us</h2>
    <?php echo $formFeedback; ?>

    <div class="contact-container">

        <!-- Form -->
        <form  method="post"  class="contact-form">
            <input  name="Name" type="text" placeholder="Your Name" required>
            <input  name="Email" type="email" placeholder="Your Email" required>
            <textarea  name="Message" placeholder="Your Message" rows="5" required> </textarea>
            <button type="submit">Send Message</button>
        </form>

        <!-- Info -->
        <div class="contact-info">
            <h3>Get In Touch</h3>
            <p>📞 Phone: 01000000000</p>
            <p>📍 Location: Cairo, Egypt</p>
            <p>📧 Email: crepebono@email.com</p>
        </div>

    </div>

</section>

<!-- Footer -->
<footer>
    <p>© 2026 Crepe Bono</p>
</footer>

<script src="script.js"></script>
</body>
</html>