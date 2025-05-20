<?php
require_once 'config/database.php';

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Danh sách sản phẩm trong database</h2>";

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

// Lấy danh sách sản phẩm
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f2f2f2;'>";
    echo "<th>ID</th>";
    echo "<th>Tên sản phẩm</th>";
    echo "<th>Mô tả</th>";
    echo "<th>Giá</th>";
    echo "<th>Danh mục</th>";
    echo "<th>Tồn kho</th>";
    echo "<th>Hình ảnh</th>";
    echo "</tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . number_format($row['price'], 0, ',', '.') . " đ</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . $row['stock'] . "</td>";
        echo "<td>";
        if (!empty($row['image'])) {
            echo "<img src='" . htmlspecialchars($row['image']) . "' style='max-width: 100px;'>";
        } else {
            echo "Không có hình";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p>Tổng số sản phẩm: " . $result->num_rows . "</p>";
} else {
    echo "<p style='color: red;'>Không có sản phẩm nào trong database!</p>";
    
    // Kiểm tra bảng products có tồn tại không
    $result = $conn->query("SHOW TABLES LIKE 'products'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>Bảng products chưa được tạo!</p>";
    } else {
        echo "<p>Bảng products đã tồn tại nhưng chưa có dữ liệu.</p>";
    }
}

$conn->close();
?> 