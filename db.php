<?php
$host = 'localhost';
$dbname = 'protinut_db'; // Change this if you have a different DB name
$username = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP password is empty

// Create connection
$conn = new mysqli($host, $username, $password);


// DSN (Data Source Name) for PDO
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Initial table creation if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create cart orders and items tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        country VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        city VARCHAR(100) NOT NULL,
        postcode VARCHAR(20) NOT NULL,
        payment_method VARCHAR(50) DEFAULT 'cod',
        utr_number VARCHAR(100) DEFAULT NULL,
        payment_status ENUM('pending', 'paid', 'unpaid') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
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

    $pdo->exec("CREATE TABLE IF NOT EXISTS blogs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        author VARCHAR(100) DEFAULT 'Admin',
        image VARCHAR(500) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) DEFAULT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subtitle VARCHAR(255) DEFAULT '100% PREMIUM QUALITY',
        title VARCHAR(500) DEFAULT 'Today Elevate Your Energy Levels purefit',
        btn_text VARCHAR(100) DEFAULT 'BUY NOW',
        btn_link VARCHAR(500) DEFAULT 'shop.php',
        hero_image VARCHAR(500) DEFAULT 'assets/img/shop/hero_product.png',
        bg_image VARCHAR(500) DEFAULT 'assets/img/bg/hero_bg.jpg'
    )");

} catch (\PDOException $e) {
    // Attempt to create the database if it doesn't exist, then retry connection
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        try {
            // Connect without specifying a database
            $pdo_no_db = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, $options);
            $pdo_no_db->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
            // Now try connecting to the database again
            $pdo = new PDO($dsn, $username, $password, $options);

            // Re-run table creation after successful database creation
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                country VARCHAR(100) NOT NULL,
                address TEXT NOT NULL,
                city VARCHAR(100) NOT NULL,
                postcode VARCHAR(20) NOT NULL,
                payment_method VARCHAR(50) DEFAULT 'cod',
                utr_number VARCHAR(100) DEFAULT NULL,
                payment_status ENUM('pending', 'paid', 'unpaid') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                product_name VARCHAR(255) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                quantity INT NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            )");

        } catch (\PDOException $e2) {
            die("Database connection or creation failed: " . $e2->getMessage());
        }
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>