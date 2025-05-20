<?php
session_start();
require_once 'config/database.php';

$error = '';
$orders = [];

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Lấy danh sách đơn hàng của người dùng
    $sql = "SELECT o.*, 
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items
            FROM orders o 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Order history error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đơn hàng - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .history-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .order-card:hover {
            transform: translateY(-5px);
        }
        .order-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .order-id {
            font-size: 1.2em;
            font-weight: bold;
            color: #2c3e50;
        }
        .order-date {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .order-status {
            font-weight: bold;
        }
        .order-total {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.2em;
        }
        .order-items {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
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

    <div class="container history-container">
        <h2 class="mb-4"><i class="fas fa-history"></i> Lịch sử đơn hàng</h2>

        <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($orders)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bạn chưa có đơn hàng nào
            </div>
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Mua sắm ngay
            </a>
        <?php else: ?>
            <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="order-id">Đơn hàng #<?php echo $order['id']; ?></div>
                                <div class="order-date">
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="order-status">
                                    Trạng thái: 
                                    <?php
                                    switch($order['status']) {
                                        case 'pending':
                                            echo '<span class="status-badge status-pending">Chờ xử lý</span>';
                                            break;
                                        case 'processing':
                                            echo '<span class="status-badge status-processing">Đang xử lý</span>';
                                            break;
                                        case 'completed':
                                            echo '<span class="status-badge status-completed">Hoàn thành</span>';
                                            break;
                                        case 'cancelled':
                                            echo '<span class="status-badge status-cancelled">Đã hủy</span>';
                                            break;
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="order-total">
                                    <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ
                                </div>
                                <div class="order-items">
                                    <i class="fas fa-box"></i> <?php echo $order['total_items']; ?> sản phẩm
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 