<?php
// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Tạo lại database</h2>";

// Thông tin kết nối
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'computer_shop';

// Kết nối MySQL
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}
echo "<p style='color: green;'>✅ Kết nối thành công đến MySQL</p>";

// Xóa database cũ nếu tồn tại
$sql = "DROP DATABASE IF EXISTS $dbname";
if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Đã xóa database cũ (nếu có)</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi xóa database: " . $conn->error . "</p>";
}

// Tạo database mới
$sql = "CREATE DATABASE $dbname";
if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Đã tạo database mới</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi tạo database: " . $conn->error . "</p>";
    die();
}

// Chọn database
$conn->select_db($dbname);

// Tạo bảng users
$sql = "CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Đã tạo bảng users</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi tạo bảng users: " . $conn->error . "</p>";
}

// Tạo bảng login_logs
$sql = "CREATE TABLE login_logs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Đã tạo bảng login_logs</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi tạo bảng login_logs: " . $conn->error . "</p>";
}

// Tạo bảng products
$sql = "CREATE TABLE products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    stock INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Đã tạo bảng products</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi tạo bảng products: " . $conn->error . "</p>";
}

// Tạo bảng orders
$sql = "CREATE TABLE orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Đã tạo bảng orders</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi tạo bảng orders: " . $conn->error . "</p>";
}

// Tạo bảng order_items
$sql = "CREATE TABLE order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11),
    product_id INT(11),
    quantity INT(11),
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

if ($conn->query($sql)) {
    echo "<p style='color: green;'>✅ Đã tạo bảng order_items</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi tạo bảng order_items: " . $conn->error . "</p>";
}

// Tạo tài khoản admin mặc định
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$email = 'admin@computershop.com';

$stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
$stmt->bind_param("sss", $username, $password, $email);

if ($stmt->execute()) {
    echo "<p style='color: green;'>✅ Đã tạo tài khoản admin mặc định</p>";
    echo "<p>Username: admin</p>";
    echo "<p>Password: admin123</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi khi tạo tài khoản admin: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();

echo "<h3>Tổng kết:</h3>";
echo "<p>✅ Database đã được tạo lại thành công!</p>";
echo "<p>Bạn có thể:</p>";
echo "<ul>";
echo "<li><a href='add_sample_products.php'>Thêm sản phẩm mẫu</a></li>";
echo "<li><a href='login.php'>Đăng nhập</a></li>";
echo "</ul>";
?> 