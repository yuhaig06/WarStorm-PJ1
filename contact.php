<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name)) {
        $error = "Vui lòng nhập họ tên!";
    } elseif (empty($email)) {
        $error = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    } elseif (empty($subject)) {
        $error = "Vui lòng nhập tiêu đề!";
    } elseif (empty($message)) {
        $error = "Vui lòng nhập nội dung tin nhắn!";
    } else {
        try {
            // Lưu tin nhắn vào database
            $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }
            
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi gửi tin nhắn: " . $stmt->error);
            }
            
            $success = "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.";
            
            // Reset form
            $name = $email = $subject = $message = '';
        } catch (Exception $e) {
            $error = $e->getMessage();
            error_log("Contact error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .contact-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .contact-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .contact-info {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .contact-info i {
            font-size: 1.5em;
            color: #0d6efd;
            margin-right: 10px;
        }
        .contact-info p {
            margin-bottom: 10px;
        }
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s;
        }
        .social-links a:hover {
            background: #0b5ed7;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-laptop"></i> Computer Shop
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
                        <a class="nav-link active" href="contact.php">Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-shopping-cart"></i> Giỏ hàng
                    </a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="btn btn-outline-success me-2">
                            <i class="fas fa-user"></i> Tài khoản
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                        <a href="register.php" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container contact-container">
        <h2 class="mb-4"><i class="fas fa-envelope"></i> Liên hệ với chúng tôi</h2>

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

        <div class="row">
            <div class="col-md-6">
                <div class="contact-card">
                    <h4 class="mb-4">Gửi tin nhắn cho chúng tôi</h4>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung tin nhắn</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                        </div>
                        <button type="submit" name="send_message" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="contact-card">
                    <h4 class="mb-4">Thông tin liên hệ</h4>
                    <div class="contact-info">
                        <p>
                            <i class="fas fa-map-marker-alt"></i>
                            Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                        </p>
                        <p>
                            <i class="fas fa-phone"></i>
                            Điện thoại: 0123 456 789
                        </p>
                        <p>
                            <i class="fas fa-envelope"></i>
                            Email: info@computershop.com
                        </p>
                        <p>
                            <i class="fas fa-clock"></i>
                            Giờ làm việc: 8:00 - 22:00 (Thứ 2 - Chủ nhật)
                        </p>
                    </div>
                    <h4 class="mb-4">Theo dõi chúng tôi</h4>
                    <div class="social-links">
                        <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 