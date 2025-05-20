<?php
session_start();
require_once 'config/database.php';

$error = '';
$order = null;

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

try {
    // Lấy thông tin đơn hàng
    $sql = "SELECT o.*, u.username, u.email 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = ? AND o.user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        throw new Exception("Không tìm thấy đơn hàng!");
    }
    
    $order = $result->fetch_assoc();
    
    // Lấy chi tiết đơn hàng
    $items_sql = "SELECT oi.*, p.name, p.image 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_sql);
    if (!$items_stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    
    $items_stmt->bind_param("i", $order['id']);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $order_items = [];
    while ($row = $items_result->fetch_assoc()) {
        $order_items[] = $row;
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Order success error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .success-icon {
            font-size: 4em;
            color: #28a745;
            margin-bottom: 20px;
        }
        .order-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .order-item-title {
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .order-item-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .order-total {
            font-size: 1.5em;
            color: #e74c3c;
            font-weight: bold;
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

    <div class="container success-container">
        <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="success-card text-center">
                <i class="fas fa-check-circle success-icon"></i>
                <h2 class="mb-4">Đặt hàng thành công!</h2>
                <p class="lead">Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là: <strong>#<?php echo $order['id']; ?></strong></p>
                <p>Chúng tôi sẽ gửi email xác nhận đến địa chỉ: <?php echo htmlspecialchars($order['email']); ?></p>
            </div>

            <div class="success-card">
                <h4 class="mb-4">Chi tiết đơn hàng</h4>
                <?php foreach($order_items as $item): ?>
                    <div class="order-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="order-item-image">
                            </div>
                            <div class="col-md-6">
                                <h5 class="order-item-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <div class="order-item-price">
                                    <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
                                </div>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted">Số lượng: <?php echo $item['quantity']; ?></span>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="order-item-price">
                                    <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Thông tin đơn hàng</h5>
                        <p>
                            <strong>Mã đơn hàng:</strong> #<?php echo $order['id']; ?><br>
                            <strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?><br>
                            <strong>Trạng thái:</strong> 
                            <?php
                            switch($order['status']) {
                                case 'pending':
                                    echo '<span class="badge bg-warning">Chờ xử lý</span>';
                                    break;
                                case 'processing':
                                    echo '<span class="badge bg-info">Đang xử lý</span>';
                                    break;
                                case 'completed':
                                    echo '<span class="badge bg-success">Hoàn thành</span>';
                                    break;
                                case 'cancelled':
                                    echo '<span class="badge bg-danger">Đã hủy</span>';
                                    break;
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5>Tổng cộng</h5>
                        <div class="order-total">
                            <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 