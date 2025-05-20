<?php
require_once 'config/database.php';

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Thêm sản phẩm mẫu vào database</h2>";

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

// Kiểm tra bảng products có tồn tại không
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows == 0) {
    die("Bảng products chưa được tạo. Vui lòng chạy lại file config/database.php trước.");
}

// Sample products data
$products = [
    [
        'name' => 'Laptop Dell XPS 13 9315',
        'description' => 'Laptop cao cấp với màn hình InfinityEdge 13.4 inch, Intel Core i7-1250U, 16GB RAM, 512GB SSD, Windows 11 Pro',
        'price' => 29990000,
        'category' => 'Laptop',
        'stock' => 10,
        'image' => 'uploads/dell-xps-13.jpg'
    ],
    [
        'name' => 'Laptop Asus ROG Strix G15 G513',
        'description' => 'Laptop gaming với AMD Ryzen 7 6800H, RTX 3060 6GB, 16GB RAM, 512GB SSD, 15.6 inch 144Hz',
        'price' => 25990000,
        'category' => 'Laptop',
        'stock' => 15,
        'image' => 'uploads/asus-rog-g15.jpg'
    ],
    [
        'name' => 'PC Gaming MSI MPG Trident 3',
        'description' => 'PC Gaming nhỏ gọn với Intel Core i5-12400F, RTX 3060 12GB, 16GB RAM, 512GB SSD, Windows 11',
        'price' => 18990000,
        'category' => 'Desktop',
        'stock' => 8,
        'image' => 'uploads/msi-triden-3.jpg'
    ],
    [
        'name' => 'CPU Intel Core i9-13900K',
        'description' => 'CPU Intel Core i9 thế hệ 13, 24 lõi (8P + 16E), 32 luồng, 5.8GHz, 36MB Cache',
        'price' => 12990000,
        'category' => 'Components',
        'stock' => 20,
        'image' => 'uploads/intel-i9.jpg'
    ],
    [
        'name' => 'VGA MSI RTX 4080 Gaming X Trio',
        'description' => 'Card đồ họa MSI RTX 4080 Gaming X Trio, 16GB GDDR6X, 3 quạt tản nhiệt, RGB Mystic Light',
        'price' => 32990000,
        'category' => 'Components',
        'stock' => 5,
        'image' => 'uploads/rtx-4080.jpg'
    ],
    [
        'name' => 'Bàn phím cơ Logitech G Pro X',
        'description' => 'Bàn phím cơ gaming với switch có thể thay đổi, RGB LIGHTSYNC, dây rút, thiết kế tối giản',
        'price' => 3990000,
        'category' => 'Accessories',
        'stock' => 30,
        'image' => 'uploads/logitech-gpro.jpg'
    ],
    [
        'name' => 'Chuột gaming Razer DeathAdder V2',
        'description' => 'Chuột gaming với cảm biến 20K DPI, 8 nút lập trình, RGB Chroma, thiết kế ergonomic',
        'price' => 1990000,
        'category' => 'Accessories',
        'stock' => 25,
        'image' => 'uploads/razer-deathadder.jpg'
    ],
    [
        'name' => 'Màn hình Dell S2721DGF',
        'description' => 'Màn hình gaming 27 inch, 165Hz, 1ms, QHD (2560x1440), HDR, FreeSync Premium Pro',
        'price' => 8990000,
        'category' => 'Accessories',
        'stock' => 12,
        'image' => 'uploads/dell-s2721dgf.jpg'
    ],
    [
        'name' => 'Laptop MacBook Pro M2',
        'description' => 'Laptop Apple MacBook Pro 13 inch với chip M2, 8GB RAM, 256GB SSD, Retina Display',
        'price' => 27990000,
        'category' => 'Laptop',
        'stock' => 7,
        'image' => 'uploads/macbook-pro.jpg'
    ],
    [
        'name' => 'PC Gaming ASUS ROG Strix G10',
        'description' => 'PC Gaming với Intel Core i7-12700F, RTX 3070 8GB, 16GB RAM, 1TB SSD, Windows 11',
        'price' => 24990000,
        'category' => 'Desktop',
        'stock' => 6,
        'image' => 'uploads/asus-rog-pc.jpg'
    ],
    [
        'name' => 'RAM Corsair Vengeance RGB Pro',
        'description' => 'RAM DDR4 32GB (2x16GB) 3600MHz, RGB LED, thiết kế đẹp mắt',
        'price' => 2990000,
        'category' => 'Components',
        'stock' => 40,
        'image' => 'uploads/corsair-ram.jpg'
    ],
    [
        'name' => 'Tai nghe Logitech G Pro X',
        'description' => 'Tai nghe gaming với mic có thể tháo rời, Blue VO!CE, âm thanh 7.1 surround',
        'price' => 3490000,
        'category' => 'Accessories',
        'stock' => 20,
        'image' => 'uploads/logitech-headset.jpg'
    ]
];

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    if (!mkdir('uploads', 0777, true)) {
        die("Không thể tạo thư mục uploads. Vui lòng kiểm tra quyền truy cập.");
    }
}

// Xóa dữ liệu cũ trong bảng products
$conn->query("TRUNCATE TABLE products");

// Insert products
$stmt = $conn->prepare("INSERT INTO products (name, description, price, category, stock, image) VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Lỗi prepare statement: " . $conn->error);
}

$success_count = 0;
$error_count = 0;

foreach ($products as $product) {
    try {
        $stmt->bind_param("ssdsss", 
            $product['name'],
            $product['description'],
            $product['price'],
            $product['category'],
            $product['stock'],
            $product['image']
        );
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Đã thêm sản phẩm: " . $product['name'] . "</p>";
            $success_count++;
        } else {
            echo "<p style='color: red;'>❌ Lỗi khi thêm sản phẩm: " . $product['name'] . " - " . $stmt->error . "</p>";
            $error_count++;
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Lỗi exception khi thêm sản phẩm: " . $product['name'] . " - " . $e->getMessage() . "</p>";
        $error_count++;
    }
}

echo "<h3>Tổng kết:</h3>";
echo "<p>- Số sản phẩm thêm thành công: " . $success_count . "</p>";
echo "<p>- Số sản phẩm thêm thất bại: " . $error_count . "</p>";

// Kiểm tra số sản phẩm trong database
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$row = $result->fetch_assoc();
echo "<p>- Tổng số sản phẩm trong database: " . $row['total'] . "</p>";

$stmt->close();
$conn->close();

echo "<p><a href='view_products.php'>Xem danh sách sản phẩm</a></p>";
?> 