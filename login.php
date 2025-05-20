<?php
session_start();
require_once 'config/database.php';

$error = '';

// Kiểm tra kết nối database
if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra và làm sạch input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        try {
            // Kiểm tra bảng users có tồn tại không
            $check_table = $conn->query("SHOW TABLES LIKE 'users'");
            if ($check_table->num_rows == 0) {
                throw new Exception("Bảng users chưa được tạo!");
            }

            // Kiểm tra cấu trúc bảng users
            $check_columns = $conn->query("SHOW COLUMNS FROM users");
            $required_columns = ['id', 'username', 'password', 'email', 'role'];
            $existing_columns = [];
            while ($column = $check_columns->fetch_assoc()) {
                $existing_columns[] = $column['Field'];
            }
            $missing_columns = array_diff($required_columns, $existing_columns);
            if (!empty($missing_columns)) {
                throw new Exception("Bảng users thiếu các cột: " . implode(', ', $missing_columns));
            }

            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }
            
            $stmt->bind_param("s", $username);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi thực thi truy vấn: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    // Đăng nhập thành công
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Ghi log đăng nhập
                    $log_sql = "INSERT INTO login_logs (user_id, login_time, ip_address) VALUES (?, NOW(), ?)";
                    $log_stmt = $conn->prepare($log_sql);
                    if ($log_stmt) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $log_stmt->bind_param("is", $user['id'], $ip);
                        $log_stmt->execute();
                    }
                    
                    // Chuyển hướng dựa vào role
                    if ($user['role'] == 'admin') {
                        header("Location: admin/index.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $error = "Mật khẩu không chính xác!";
                }
            } else {
                $error = "Tài khoản không tồn tại!";
            }
        } catch (Exception $e) {
            $error = "Có lỗi xảy ra, vui lòng thử lại sau!";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
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
        <div class="login-container">
            <div class="login-logo">
                <i class="fas fa-laptop"></i>
                <h3>Warstorm<h3>
            </div>
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Tên đăng nhập
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
                <div class="text-center mt-3">
                    <a href="forgot_password.php" class="text-decoration-none">
                        <i class="fas fa-key"></i> Quên mật khẩu?
                    </a>
                </div>
                <hr>
                <div class="text-center">
                    <p>Chưa có tài khoản? <a href="register.php" class="text-decoration-none">Đăng ký ngay</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>