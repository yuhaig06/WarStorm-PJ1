<?php
// Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra kết nối database
try {
    require_once '../config/database.php';
    echo "Kết nối database thành công!<br><br>";
} catch (Exception $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Kiểm tra tài khoản admin
$sql = "SELECT id, username, password, email, role, status FROM users WHERE username = 'admin' OR role = 'admin'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Danh sách tài khoản admin:</h3>";
    while($row = $result->fetch_assoc()) {
        echo "<div style='margin-bottom: 20px; padding: 10px; border: 1px solid #ccc;'>";
        echo "ID: " . $row['id'] . "<br>";
        echo "Username: " . $row['username'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "Role: " . $row['role'] . "<br>";
        echo "Status: " . $row['status'] . "<br>";
        echo "Password hash: " . $row['password'] . "<br>";
        echo "</div>";
    }
} else {
    echo "Không tìm thấy tài khoản admin nào trong database!";
}

$conn->close();
?> 