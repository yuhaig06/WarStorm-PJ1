<?php
session_start();
require_once '../config/database.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Lấy thống kê
try {
    // Tổng số đơn hàng
    $orders_sql = "SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue FROM orders";
    $orders_result = $conn->query($orders_sql);
    $orders_stats = $orders_result->fetch_assoc();

    // Tổng số người dùng
    $users_sql = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
    $users_result = $conn->query($users_sql);
    $users_stats = $users_result->fetch_assoc();

    // Tổng số sản phẩm
    $products_sql = "SELECT COUNT(*) as total_products, SUM(stock) as total_stock FROM products";
    $products_result = $conn->query($products_sql);
    $products_stats = $products_result->fetch_assoc();

    // Đơn hàng gần đây
    $recent_orders_sql = "SELECT o.*, u.username 
                         FROM orders o 
                         JOIN users u ON o.user_id = u.id 
                         ORDER BY o.created_at DESC 
                         LIMIT 5";
    $recent_orders_result = $conn->query($recent_orders_sql);
    $recent_orders = [];
    while ($row = $recent_orders_result->fetch_assoc()) {
        $recent_orders[] = $row;
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Warstorm</title>
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
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .recent-orders {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
                            <a class="nav-link active" href="dashboard.php">
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
                            <a class="nav-link" href="users.php">
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
                <h2 class="mb-4">Dashboard</h2>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card bg-primary text-white">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3><?php echo number_format($orders_stats['total_orders']); ?></h3>
                            <p class="mb-0">Tổng đơn hàng</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-success text-white">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3><?php echo number_format($orders_stats['total_revenue'], 0, ',', '.'); ?> đ</h3>
                            <p class="mb-0">Tổng doanh thu</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-info text-white">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3><?php echo number_format($users_stats['total_users']); ?></h3>
                            <p class="mb-0">Người dùng</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-warning text-white">
                            <div class="stat-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <h3><?php echo number_format($products_stats['total_products']); ?></h3>
                            <p class="mb-0">Sản phẩm</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="recent-orders mt-4">
                    <h4 class="mb-4">Đơn hàng gần đây</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                                        <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</td>
                                        <td>
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
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="order_detail.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 