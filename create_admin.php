<?php
// Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra kết nối database
try {
    require_once 'config/database.php';
    echo "Kết nối database thành công!<br>";
} catch (Exception $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Thông tin tài khoản admin mới
$username = "admin";
$password = "admin123"; // Mật khẩu mặc định
$email = "admin@warstorm.com";
$phone = "0123456789";
$address = "Hà Nội";
$role = "admin";
$status = "active";

// Mã hóa mật khẩu
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Bắt đầu transaction
    $conn->begin_transaction();

    // Kiểm tra xem tài khoản đã tồn tại chưa
    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Lỗi prepare statement: " . $conn->error);
    }
    
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Xóa các đơn hàng liên quan
        $delete_orders_sql = "DELETE FROM orders WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_orders_sql);
        if (!$delete_stmt) {
            throw new Exception("Lỗi prepare statement: " . $conn->error);
        }
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();

        // Xóa tài khoản cũ
        $delete_user_sql = "DELETE FROM users WHERE id = ?";
        $delete_user_stmt = $conn->prepare($delete_user_sql);
        if (!$delete_user_stmt) {
            throw new Exception("Lỗi prepare statement: " . $conn->error);
        }
        $delete_user_stmt->bind_param("i", $user_id);
        $delete_user_stmt->execute();

        echo "Đã xóa tài khoản admin cũ và các đơn hàng liên quan.<br>";
    }

    // Thêm tài khoản admin mới
    $sql = "INSERT INTO users (username, password, email, phone, address, role, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi prepare statement: " . $conn->error);
    }
    
    $stmt->bind_param("sssssss", $username, $hashed_password, $email, $phone, $address, $role, $status);
    
    if ($stmt->execute()) {
        // Commit transaction nếu mọi thứ OK
        $conn->commit();
        
        echo "<div style='color: green; font-weight: bold;'>";
        echo "Đã tạo tài khoản admin thành công!<br>";
        echo "Username: " . $username . "<br>";
        echo "Password: " . $password . "<br>";
        echo "Email: " . $email . "<br>";
        echo "</div>";
        echo "<div style='color: red; margin-top: 10px;'>";
        echo "Vui lòng đăng nhập và đổi mật khẩu ngay lập tức!";
        echo "</div>";
    } else {
        throw new Exception("Lỗi khi thêm tài khoản: " . $stmt->error);
    }
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    
    echo "<div style='color: red; font-weight: bold;'>";
    echo "Có lỗi xảy ra: " . $e->getMessage();
    echo "</div>";
}

$conn->close();
?> 