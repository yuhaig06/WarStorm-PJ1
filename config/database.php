<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'computer_shop');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
} else {
    die("Error creating database: " . $conn->error);
}

// Create products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    stock INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    fullname VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Kiểm tra và thêm các cột mới vào bảng users
$check_columns = $conn->query("SHOW COLUMNS FROM users");
$existing_columns = [];
while ($column = $check_columns->fetch_assoc()) {
    $existing_columns[] = $column['Field'];
}

// Thêm cột fullname nếu chưa tồn tại
if (!in_array('fullname', $existing_columns)) {
    $sql = "ALTER TABLE users ADD COLUMN fullname VARCHAR(100) AFTER email";
    $conn->query($sql);
}

// Thêm cột phone nếu chưa tồn tại
if (!in_array('phone', $existing_columns)) {
    $sql = "ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER fullname";
    $conn->query($sql);
}

// Thêm cột address nếu chưa tồn tại
if (!in_array('address', $existing_columns)) {
    $sql = "ALTER TABLE users ADD COLUMN address TEXT AFTER phone";
    $conn->query($sql);
}

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    note TEXT,
    payment_method ENUM('cod', 'bank', 'momo') DEFAULT 'cod',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($sql);

// Create order_items table
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11),
    product_id INT(11),
    quantity INT(11),
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";
$conn->query($sql);

// Kiểm tra và thêm cột specifications vào bảng order_items
$check_columns = $conn->query("SHOW COLUMNS FROM order_items");
$existing_columns = [];
while ($column = $check_columns->fetch_assoc()) {
    $existing_columns[] = $column['Field'];
}

// Thêm cột specifications nếu chưa tồn tại
if (!in_array('specifications', $existing_columns)) {
    $sql = "ALTER TABLE order_items ADD COLUMN specifications TEXT AFTER price";
    $conn->query($sql);
}

// Tạo bảng cart nếu chưa tồn tại
$create_cart_table = "CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if (!$conn->query($create_cart_table)) {
    error_log("Lỗi tạo bảng cart: " . $conn->error);
}
?> 