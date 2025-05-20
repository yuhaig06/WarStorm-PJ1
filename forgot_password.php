<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

// Kiểm tra kết nối database
if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Vui lòng nhập email của bạn!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    } else {
        try {
            // Kiểm tra email có tồn tại trong database
            $sql = "SELECT id, username FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Tạo token reset password
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Lưu token vào database
                $update_sql = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ssi", $token, $expires, $user['id']);
                
                if ($update_stmt->execute()) {
                    // Gửi email reset password
                    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
                    $to = $email;
                    $subject = "Yêu cầu đặt lại mật khẩu - Computer Shop";
                    $message = "Xin chào " . $user['username'] . ",\n\n";
                    $message .= "Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng click vào link sau để đặt lại mật khẩu:\n\n";
                    $message .= $reset_link . "\n\n";
                    $message .= "Link này sẽ hết hạn sau 1 giờ.\n\n";
                    $message .= "Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.\n\n";
                    $message .= "Trân trọng,\nComputer Shop Team";
                    
                    $headers = "From: noreply@computershop.com\r\n";
                    $headers .= "Reply-To: noreply@computershop.com\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
                    
                    if (mail($to, $subject, $message, $headers)) {
                        $success = "Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn!";
                    } else {
                        throw new Exception("Không thể gửi email!");
                    }
                } else {
                    throw new Exception("Lỗi cập nhật token!");
                }
            } else {
                $error = "Email không tồn tại trong hệ thống!";
            }
        } catch (Exception $e) {
            $error = "Có lỗi xảy ra, vui lòng thử lại sau!";
            error_log("Forgot password error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .forgot-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .forgot-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .forgot-logo i {
            font-size: 48px;
            color: #3498db;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-container">
            <div class="forgot-logo">
                <i class="fas fa-key"></i>
                <h3>Quên mật khẩu</h3>
            </div>
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="" autocomplete="off">
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required>
                    <div class="form-text">
                        Nhập email đã đăng ký để nhận hướng dẫn đặt lại mật khẩu
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-paper-plane"></i> Gửi yêu cầu
                </button>
                <hr>
                <div class="text-center">
                    <p>Đã nhớ mật khẩu? <a href="login.php" class="text-decoration-none">Đăng nhập ngay</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 