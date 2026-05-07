<?php
// connection.php — Single database connection for the entire Protinut project
// Uses mysqli. Included by both frontend pages and admin panel.

// Disable mysqli default error reporting to prevent blank pages on SQL errors (PHP 8.1+)
mysqli_report(MYSQLI_REPORT_OFF);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'protinut';
$db_username = 'root';
$db_password = '';

// Connect without database first to ensure it exists
$link = mysqli_connect($host, $db_username, $db_password);
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if not exists
mysqli_query($link, "CREATE DATABASE IF NOT EXISTS `$dbname`");
mysqli_select_db($link, $dbname);
mysqli_set_charset($link, "utf8mb4");

// ─── Auto-create tables ───

// Users (frontend customers)
mysqli_query($link, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL DEFAULT '',
    google_id VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    postcode VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Products
mysqli_query($link, "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    stock INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_best_seller TINYINT(1) DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Orders
mysqli_query($link, "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    postcode VARCHAR(20) NOT NULL,
    status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
    payment_method VARCHAR(50) DEFAULT 'cod',
    utr_number VARCHAR(100) DEFAULT NULL,
    payment_status ENUM('pending', 'paid', 'unpaid') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Order Items
mysqli_query($link, "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)");

// Blog Categories
mysqli_query($link, "CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert default categories if none
$cat_check = mysqli_query($link, "SELECT COUNT(*) as cnt FROM blog_categories");
$cat_row = mysqli_fetch_assoc($cat_check);
if ($cat_row['cnt'] == 0) {
    mysqli_query($link, "INSERT INTO blog_categories (name) VALUES ('Nutrition'), ('Workout'), ('Supplements'), ('Recovery'), ('Health')");
}

// Blogs (Updated)
mysqli_query($link, "CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(100) DEFAULT 'Admin',
    image VARCHAR(500) DEFAULT NULL,
    media_type ENUM('image', 'video') DEFAULT 'image',
    likes_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Blog Comments
mysqli_query($link, "CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    media VARCHAR(500) DEFAULT NULL,
    media_type ENUM('image', 'video') DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
)");

// Blog Likes
mysqli_query($link, "CREATE TABLE IF NOT EXISTS blog_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
)");

// Contact submissions
mysqli_query($link, "CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Hero settings
mysqli_query($link, "CREATE TABLE IF NOT EXISTS hero_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtitle VARCHAR(255) DEFAULT '100% PREMIUM QUALITY',
    title VARCHAR(500) DEFAULT 'Today Elevate Your Energy Levels purefit',
    btn_text VARCHAR(100) DEFAULT 'BUY NOW',
    btn_link VARCHAR(500) DEFAULT 'shop.php',
    hero_image VARCHAR(500) DEFAULT 'assets/img/shop/hero_product.png',
    bg_image VARCHAR(500) DEFAULT 'assets/img/bg/hero_bg.jpg'
)");

// Admin table
mysqli_query($link, "CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");

// Admin auth tokens
mysqli_query($link, "CREATE TABLE IF NOT EXISTS ods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ots VARCHAR(100),
    user_id VARCHAR(100),
    uuid VARCHAR(100),
    status VARCHAR(50) DEFAULT 'pending'
)");

// Create default admin user if none exists
$check = mysqli_query($link, "SELECT COUNT(*) as cnt FROM admin");
$row = mysqli_fetch_assoc($check);
if ($row['cnt'] == 0) {
    $default_password = 'admin123';
    $default_hash = password_hash($default_password, PASSWORD_DEFAULT);
    if (!$default_hash) {
        $default_hash = $default_password;
    }
    $default_hash_esc = mysqli_real_escape_string($link, $default_hash);
    mysqli_query($link, "INSERT INTO admin (username, password) VALUES ('admin', '$default_hash_esc')");
}

// Add status column to orders if it doesn't exist (migration safety)
$col_check = mysqli_query($link, "SHOW COLUMNS FROM orders LIKE 'status'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE orders ADD COLUMN status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending' AFTER postcode");
}

// Add payment_method column if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM orders LIKE 'payment_method'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'cod' AFTER status");
}

// Add utr_number column if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM orders LIKE 'utr_number'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE orders ADD COLUMN utr_number VARCHAR(100) DEFAULT NULL AFTER payment_method");
}

// Add payment_status column if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM orders LIKE 'payment_status'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE orders ADD COLUMN payment_status ENUM('pending', 'paid', 'unpaid') DEFAULT 'pending' AFTER utr_number");
}

// ─── Users Table Migrations ───

// Add phone column if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'phone'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER password");
}

// Add country column if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'country'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE users ADD COLUMN country VARCHAR(100) DEFAULT NULL AFTER phone");
}

// Add address column if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'address'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL AFTER country");
}

// Add city column if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'city'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE users ADD COLUMN city VARCHAR(100) DEFAULT NULL AFTER address");
}

// Add category_id to blogs if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM blogs LIKE 'category_id'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE blogs ADD COLUMN category_id INT DEFAULT NULL AFTER id");
}

// Add media_type to blogs if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM blogs LIKE 'media_type'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE blogs ADD COLUMN media_type ENUM('image', 'video') DEFAULT 'image' AFTER image");
}

// Add likes_count to blogs if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM blogs LIKE 'likes_count'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE blogs ADD COLUMN likes_count INT DEFAULT 0 AFTER media_type");
}

// ─── Google OAuth Migration ───
// Add google_id column to users if it doesn't exist
$col_check = mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'google_id'");
if (mysqli_num_rows($col_check) == 0) {
    mysqli_query($link, "ALTER TABLE users ADD COLUMN google_id VARCHAR(100) DEFAULT NULL AFTER password");
}
?>
