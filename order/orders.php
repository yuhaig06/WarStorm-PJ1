<?php
session_start();
require_once 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$orders = [];

try {
    // Lấy danh sách đơn hàng của user
    $sql = "SELECT o.*, 
            COUNT(oi.id) as total_items,
            GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE o.user_id = ?
            GROUP BY o.id
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
    error_log("Orders error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .orders-container {
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
        }
        .order-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .order-status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .status-processing {
            background-color: #b8daff;
            color: #004085;
        }
        .status-completed {
            background-color: #c3e6cb;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f5c6cb;
            color: #721c24;
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

    <div class="container orders-container">
        <h2 class="mb-4"><i class="fas fa-shopping-bag"></i> Đơn hàng của tôi</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($orders)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bạn chưa có đơn hàng nào
            </div>
        <?php else: ?>
            <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Đơn hàng #<?php echo $order['id']; ?></h5>
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i:s', strtotime($order['created_at'])); ?>
                            </small>
                        </div>
                        <div>
                            <?php
                            $status_class = '';
                            $status_text = '';
                            switch($order['status']) {
                                case 'pending':
                                    $status_class = 'status-pending';
                                    $status_text = 'Chờ xử lý';
                                    break;
                                case 'processing':
                                    $status_class = 'status-processing';
                                    $status_text = 'Đang xử lý';
                                    break;
                                case 'completed':
                                    $status_class = 'status-completed';
                                    $status_text = 'Hoàn thành';
                                    break;
                                case 'cancelled':
                                    $status_class = 'status-cancelled';
                                    $status_text = 'Đã hủy';
                                    break;
                            }
                            ?>
                            <span class="order-status <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>Sản phẩm:</strong> <?php echo htmlspecialchars($order['product_names']); ?></p>
                            <p><strong>Số lượng sản phẩm:</strong> <?php echo $order['total_items']; ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h4 class="text-primary">
                                <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ
                            </h4>
                            <?php if($order['status'] == 'pending'): ?>
                                <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-times"></i> Hủy đơn hàng
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelOrder(orderId) {
            if(confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                window.location.href = 'cancel_order.php?id=' + orderId;
            }
        }
    </script>
</body>
</html> 