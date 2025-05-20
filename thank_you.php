<?php
session_start();
require_once 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra order_id
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT o.*, u.username, u.email FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: index.php");
    exit();
}

// Lấy chi tiết đơn hàng
$stmt = $conn->prepare("SELECT oi.*, p.name, p.image 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảm ơn bạn đã đặt hàng - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .thank-you-section {
            background: #f8f9fa;
            padding: 4rem 0;
            text-align: center;
        }
        .order-details {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .order-item {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
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
                </ul>
                <div class="d-flex">
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

    <!-- Thank You Section -->
    <section class="thank-you-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    <h1 class="mt-4">Cảm ơn bạn đã đặt hàng!</h1>
                    <p class="lead">Đơn hàng của bạn đã được đặt thành công.</p>
                    <p>Mã đơn hàng: #<?php echo $order_id; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Details -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="order-details">
                    <h3 class="mb-4">Chi tiết đơn hàng</h3>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <p>Mã đơn hàng: #<?php echo $order_id; ?></p>
                            <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                            <p>Trạng thái: 
                                <span class="badge bg-<?php 
                                    echo $order['status'] == 'pending' ? 'warning' : 
                                        ($order['status'] == 'processing' ? 'info' : 
                                        ($order['status'] == 'completed' ? 'success' : 'danger')); 
                                ?>">
                                    <?php 
                                        echo $order['status'] == 'pending' ? 'Chờ xử lý' : 
                                            ($order['status'] == 'processing' ? 'Đang xử lý' : 
                                            ($order['status'] == 'completed' ? 'Hoàn thành' : 'Đã hủy')); 
                                    ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin người đặt</h5>
                            <p>Tên: <?php echo htmlspecialchars($order['username']); ?></p>
                            <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
                        </div>
                    </div>

                    <h5 class="mb-3">Sản phẩm đã đặt</h5>
                    <?php foreach($order_items as $item): ?>
                        <div class="order-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="product-image">
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Số lượng: <?php echo $item['quantity']; ?></small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <p class="mb-0"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Phương thức thanh toán</h5>
                            <p>Thanh toán khi nhận hàng (COD)</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>Tổng cộng</h5>
                            <p class="text-danger fw-bold"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</p>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Về trang chủ
                        </a>
                        <a href="profile.php" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-user"></i> Xem đơn hàng của tôi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về chúng tôi</h5>
                    <p>Computer Shop - Cửa hàng máy tính uy tín, chất lượng với nhiều năm kinh nghiệm trong lĩnh vực công nghệ.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ<br>
                        <i class="fas fa-phone"></i> 0123 456 789<br>
                        <i class="fas fa-envelope"></i> info@computershop.com
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0">&copy; 2024 Computer Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 