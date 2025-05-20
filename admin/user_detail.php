<?php
session_start();
require_once '../config/database.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Kiểm tra ID người dùng
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = $_GET['id'];

// Lấy thông tin người dùng
$user_sql = "SELECT u.*, 
             COUNT(DISTINCT o.id) as total_orders,
             SUM(o.total_amount) as total_spent
             FROM users u
             LEFT JOIN orders o ON u.id = o.user_id
             WHERE u.id = ?
             GROUP BY u.id";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit();
}

// Lấy lịch sử đơn hàng
$orders_sql = "SELECT o.*, 
               COUNT(oi.id) as total_items
               FROM orders o
               LEFT JOIN order_items oi ON o.id = oi.order_id
               WHERE o.user_id = ?
               GROUP BY o.id
               ORDER BY o.created_at DESC";
$stmt = $conn->prepare($orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết người dùng - Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 1rem;
            margin: 0.2rem 0;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            background: #3498db;
            color: white;
        }
        .user-profile {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #6c757d;
            margin: 0 auto 1rem;
        }
        .order-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="fas fa-laptop me-2"></i> Sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="users.php">
                                <i class="fas fa-users me-2"></i> Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="revenue.php">
                                <i class="fas fa-chart-line me-2"></i> Doanh thu
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Chi tiết người dùng</h2>
                    <a href="users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại
                    </a>
                </div>

                <!-- User Profile -->
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <div class="text-center mb-4">
                        <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                            <?php echo $user['role'] === 'admin' ? 'Admin' : 'Người dùng'; ?>
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
                            <p><strong>Trạng thái:</strong> 
                                <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo $user['status'] === 'active' ? 'Hoạt động' : 'Khóa'; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3><?php echo $user['total_orders']; ?></h3>
                            <p class="text-muted mb-0">Tổng số đơn hàng</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3><?php echo number_format($user['total_spent'] ?? 0, 0, ',', '.'); ?> đ</h3>
                            <p class="text-muted mb-0">Tổng chi tiêu</p>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <h3 class="mb-4">Lịch sử đơn hàng</h3>
                <?php if(empty($orders)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Người dùng này chưa có đơn hàng nào
                    </div>
                <?php else: ?>
                    <?php foreach($orders as $order): ?>
                        <div class="order-card">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h5 class="mb-1">Đơn hàng #<?php echo $order['id']; ?></h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-<?php 
                                        echo $order['status'] === 'pending' ? 'warning' : 
                                            ($order['status'] === 'completed' ? 'success' : 
                                            ($order['status'] === 'cancelled' ? 'danger' : 'info')); 
                                    ?>">
                                        <?php 
                                            echo $order['status'] === 'pending' ? 'Chờ xử lý' : 
                                                ($order['status'] === 'completed' ? 'Hoàn thành' : 
                                                ($order['status'] === 'cancelled' ? 'Đã hủy' : 'Đang giao')); 
                                        ?>
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <i class="fas fa-box"></i> <?php echo $order['total_items']; ?> sản phẩm
                                </div>
                                <div class="col-md-2">
                                    <strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</strong>
                                </div>
                                <div class="col-md-1 text-end">
                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 