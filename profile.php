<?php
session_start();
require_once 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Lấy thông tin user
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate input
    if (empty($fullname) || empty($email)) {
        $error = "Vui lòng nhập đầy đủ thông tin bắt buộc!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    } else {
        try {
            // Kiểm tra email đã tồn tại chưa
            $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("si", $email, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "Email đã được sử dụng bởi tài khoản khác!";
            } else {
                // Cập nhật thông tin
                $update_sql = "UPDATE users SET fullname = ?, email = ?, phone = ?, address = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ssssi", $fullname, $email, $phone, $address, $user_id);
                
                if ($update_stmt->execute()) {
                    $success = "Cập nhật thông tin thành công!";
                    // Cập nhật lại thông tin user
                    $user['fullname'] = $fullname;
                    $user['email'] = $email;
                    $user['phone'] = $phone;
                    $user['address'] = $address;
                } else {
                    throw new Exception("Lỗi cập nhật thông tin: " . $update_stmt->error);
                }
            }
        } catch (Exception $e) {
            $error = "Có lỗi xảy ra, vui lòng thử lại sau!";
            error_log("Profile update error: " . $e->getMessage());
        }
    }
}

// Lấy lịch sử đơn hàng
$orders_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$orders_stmt = $conn->prepare($orders_sql);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản - Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-title {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        .profile-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #3498db;
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
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .status-pending {
            background-color: #f1c40f;
            color: #fff;
        }
        .status-processing {
            background-color: #3498db;
            color: #fff;
        }
        .status-completed {
            background-color: #2ecc71;
            color: #fff;
        }
        .status-cancelled {
            background-color: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-laptop"></i> Warstorm
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-shopping-cart"></i> Giỏ hàng
                    </a>
                    <a href="profile.php" class="btn btn-outline-success me-2">
                        <i class="fas fa-user"></i> Tài khoản
                    </a>
                    <a href="logout.php" class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h1 class="profile-title">Thông tin tài khoản</h1>
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

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Tên đăng nhập
                            </label>
                            <input type="text" class="form-control" id="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">
                                <i class="fas fa-user-circle"></i> Họ và tên
                            </label>
                            <input type="text" class="form-control" id="fullname" name="fullname" 
                                   value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i> Số điện thoại
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Địa chỉ
                            </label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật thông tin
                    </button>
                </div>
            </form>
        </div>

        <div class="profile-card">
            <h2 class="profile-title">Lịch sử đơn hàng</h2>
            <?php if(empty($orders)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Bạn chưa có đơn hàng nào.
                </div>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <div class="order-card">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Mã đơn hàng:</strong> #<?php echo $order['id']; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ
                            </div>
                            <div class="col-md-3">
                                <strong>Trạng thái:</strong>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php
                                    switch($order['status']) {
                                        case 'pending':
                                            echo 'Chờ xử lý';
                                            break;
                                        case 'processing':
                                            echo 'Đang xử lý';
                                            break;
                                        case 'completed':
                                            echo 'Hoàn thành';
                                            break;
                                        case 'cancelled':
                                            echo 'Đã hủy';
                                            break;
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về chúng tôi</h5>
                    <p>Warstorm - Cửa hàng máy tính uy tín, chất lượng với nhiều năm kinh nghiệm trong lĩnh vực công nghệ.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh<br>
                        <i class="fas fa-phone"></i> 0123 456 789<br>
                        <i class="fas fa-envelope"></i> info@warstorm.com<br>
                        <i class="fas fa-clock"></i> 8:00 - 22:00 (Thứ 2 - Chủ nhật)
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="mt-3">
                        <h5>Liên kết nhanh</h5>
                        <ul class="list-unstyled">
                            <li><a href="products.php" class="text-light text-decoration-none">Sản phẩm</a></li>
                            <li><a href="contact.php" class="text-light text-decoration-none">Liên hệ</a></li>
                            <li><a href="about.php" class="text-light text-decoration-none">Giới thiệu</a></li>
                            <li><a href="policy.php" class="text-light text-decoration-none">Chính sách</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0">&copy; 2024 Warstorm. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 