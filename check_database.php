<?php
// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Kiểm tra kết nối và cấu trúc database</h2>";

// Thông tin kết nối
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'computer_shop';

echo "<h3>Thông tin kết nối:</h3>";
echo "Host: $host<br>";
echo "User: $user<br>";
echo "Database: $dbname<br>";

// Kiểm tra kết nối
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}
echo "<p style='color: green;'>✅ Kết nối thành công đến MySQL</p>";

// Kiểm tra database có tồn tại không
$result = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>❌ Database '$dbname' chưa tồn tại</p>";
    
    // Tạo database
    if ($conn->query("CREATE DATABASE $dbname")) {
        echo "<p style='color: green;'>✅ Đã tạo database '$dbname'</p>";
    } else {
        echo "<p style='color: red;'>❌ Không thể tạo database: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Database '$dbname' đã tồn tại</p>";
}

// Chọn database
$conn->select_db($dbname);

// Kiểm tra các bảng
$tables = ['products', 'users', 'orders', 'order_items'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "<p style='color: red;'>❌ Bảng '$table' chưa tồn tại</p>";
    } else {
        echo "<p style='color: green;'>✅ Bảng '$table' đã tồn tại</p>";
        
        // Hiển thị cấu trúc bảng
        echo "<h4>Cấu trúc bảng $table:</h4>";
        $result = $conn->query("DESCRIBE $table");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Đếm số bản ghi
        $result = $conn->query("SELECT COUNT(*) as total FROM $table");
        $row = $result->fetch_assoc();
        echo "<p>Số bản ghi trong bảng: " . $row['total'] . "</p>";
    }
}

$conn->close();
?> 