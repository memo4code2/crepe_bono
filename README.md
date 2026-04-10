# Crepe Bono - Crepe Ordering Website
![Crepe Bono](crepe-bono.png)
A complete web application for ordering crepes online, featuring a shopping cart, contact form, and admin dashboard.

## Features

- **Menu Page**: Browse and add crepes to cart (Savory and Sweet options).
- **Shopping Cart**: Add, remove, and update quantities; persistent via localStorage.
- **Checkout**: Place orders with customer details and payment options.
- **Contact Form**: Send messages to the business.
- **Admin Dashboard**: View orders, contact messages, and manage admin accounts.
- **Responsive Design**: Works on desktop and mobile.

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript (localStorage for cart).
- **Backend**: PHP (with MySQLi for database).
- **Database**: MySQL (auto-creates tables).
- **Security**: Session-based admin login with password hashing.

## Requirements

- **Web Server**: Apache (via XAMPP, WAMP, or similar).
- **PHP**: Version 7.4 or higher.
- **MySQL**: Version 5.7 or higher.
- **Browser**: Modern browser with JavaScript enabled.

## Installation & Setup

### 1. Download & Extract
- Download the project files.
- Extract to your web server's document root (e.g., `C:\xampp\htdocs\crepe_bono` or `/var/www/html/crepe_bono`).

### 2. Set Up Database
- Start your MySQL server (via XAMPP control panel).
- Open phpMyAdmin or MySQL command line.
- Create a new database named `crepe_bono`:
  ```sql
  CREATE DATABASE crepe_bono;
  ```
- (Optional) Create a MySQL user with privileges on this database, and update `db.php` with credentials.

### 3. Configure Database Connection
- Open `db.php` in the project root.
- Update the MySQL connection details if needed (default: localhost, root, no password):
  ```php
  $conn = new mysqli("localhost", "root", "", "crepe_bono");
  ```

### 4. Run the Application
- Start your web server (Apache via XAMPP).
- Open a browser and go to: `http://localhost/crepe_bono/` (adjust path if different).
- The database tables will be created automatically on first access.

### 5. Default Admin Login
- Username: `mo`
- Password: `0246`
- Access: `http://localhost/crepe_bono/admin_login.php`

## Project Structure

```
crepe_bono/
├── index.html          # Home page
├── menu.html           # Menu with cart functionality
├── cart.html           # Shopping cart page
├── checkout.html       # Checkout form
├── contact.php         # Contact form
├── process_order.php   # Order processing script
├── admin_login.php     # Admin login page
├── admin_dashboard.php # Admin dashboard
├── admin_logout.php    # Admin logout script
├── db.php              # Database connection & table creation
├── script.js           # JavaScript for cart and UI
├── style.css           # CSS styles
├── Photos/             # Image assets
└── README.md           # This file
```

## Database Tables

The application auto-creates these tables:

- **`contact`**: Stores contact form messages.
- **`orders`**: Stores order details (customer info, total).
- **`order_items`**: Stores individual items per order.
- **`admins`**: Stores admin user accounts.

## Usage

### For Customers
1. Visit the home page and navigate to the menu.
2. Add items to the cart.
3. Proceed to checkout and place an order.
4. Contact via the contact form.

### For Admins
1. Log in via the admin login page.
2. View orders and messages in the dashboard.
3. Add new admin accounts if needed.

## Security Notes

- Admin sessions expire on logout; back button is blocked via cache headers.
- Passwords are hashed using PHP's `password_hash()`.
- Use HTTPS in production for secure data transmission.

## Troubleshooting

- **Database Connection Error**: Ensure MySQL is running and the database exists.
- **Tables Not Created**: Check MySQL user permissions.
- **Cart Not Working**: Ensure JavaScript is enabled in your browser.
- **Admin Login Fails**: Verify default credentials or check for typos.

## License

This project is for educational purposes. Modify and distribute as needed.

## Contact

For issues or contributions, feel free to reach out via the contact form in the app.
