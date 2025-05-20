<?php
session_start();
require_once '../config/database.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Lấy thông tin admin
$admin_sql = "SELECT * FROM users WHERE id = ?";
$admin_stmt = $conn->prepare($admin_sql);
$admin_stmt->bind_param("i", $_SESSION['user_id']);
$admin_stmt->execute();
$admin = $admin_stmt->get_result()->fetch_assoc();

// Xử lý lọc theo thời gian
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Mặc định là ngày đầu tháng
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Mặc định là ngày hiện tại

// Lấy doanh thu theo ngày
$daily_revenue_sql = "SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_orders,
    COALESCE(SUM(total_amount), 0) as total_revenue
    FROM orders 
    WHERE status = 'completed' 
    AND created_at BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date DESC";

$daily_stmt = $conn->prepare($daily_revenue_sql);
$daily_stmt->bind_param("ss", $start_date, $end_date);
$daily_stmt->execute();
$daily_revenue = $daily_stmt->get_result();

// Tính tổng doanh thu và số đơn hàng
$total_revenue = 0;
$total_orders = 0;

// Reset result pointer
$daily_revenue->data_seek(0);

while ($row = $daily_revenue->fetch_assoc()) {
    $total_revenue += floatval($row['total_revenue']);
    $total_orders += intval($row['total_orders']);
}

// Lấy doanh thu theo tháng
$monthly_revenue_sql = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total_orders,
    COALESCE(SUM(total_amount), 0) as total_revenue
    FROM orders 
    WHERE status = 'completed' 
    AND created_at BETWEEN ? AND ?
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC";

$monthly_stmt = $conn->prepare($monthly_revenue_sql);
$monthly_stmt->bind_param("ss", $start_date, $end_date);
$monthly_stmt->execute();
$monthly_revenue = $monthly_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý doanh thu - Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 1rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,.2);
        }
        .main-content {
            padding: 2rem;
        }
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
            transition: transform .2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-laptop"></i> Warstorm
                    </h4>
                    <div class="text-center mb-4">
                        <img src="https://via.placeholder.com/100" class="rounded-circle mb-2" alt="Admin">
                        <h6><?php echo htmlspecialchars($admin['username']); ?></h6>
                        <small class="text-muted">Administrator</small>
                    </div>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users"></i> Quản lý người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="fas fa-box"></i> Quản lý sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="revenue.php">
                                <i class="fas fa-money-bill-wave"></i> Quản lý doanh thu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4">Quản lý doanh thu</h2>

                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-shopping-cart"></i> Tổng số đơn hàng
                                </h5>
                                <h3><?php echo number_format($total_orders); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-money-bill-wave"></i> Tổng doanh thu
                                </h5>
                                <h3><?php echo number_format($total_revenue, 0, ',', '.'); ?> VNĐ</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line"></i> Chi tiết doanh thu theo ngày
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Số đơn hàng</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $daily_revenue->data_seek(0);
                                    if ($daily_revenue->num_rows > 0):
                                        while($row = $daily_revenue->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                                        <td><?php echo number_format($row['total_orders']); ?></td>
                                        <td><?php echo number_format($row['total_revenue'], 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu doanh thu trong khoảng thời gian này</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue Table -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt"></i> Doanh thu theo tháng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th>Số đơn hàng</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($monthly_revenue->num_rows > 0):
                                        while($row = $monthly_revenue->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><?php echo date('m/Y', strtotime($row['month'] . '-01')); ?></td>
                                        <td><?php echo number_format($row['total_orders']); ?></td>
                                        <td><?php echo number_format($row['total_revenue'], 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu doanh thu theo tháng</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 