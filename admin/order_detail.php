<?php
session_start();
require_once '../config/database.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Kiểm tra order_id
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

try {
    // Lấy thông tin đơn hàng
    $order_sql = "SELECT o.*, u.username, u.email, u.phone, u.address 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  WHERE o.id = ?";
    $order_stmt = $conn->prepare($order_sql);
    if (!$order_stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn đơn hàng: " . $conn->error);
    }
    
    $order_stmt->bind_param("i", $order_id);
    if (!$order_stmt->execute()) {
        throw new Exception("Lỗi thực thi truy vấn đơn hàng: " . $order_stmt->error);
    }
    
    $order = $order_stmt->get_result()->fetch_assoc();
    if (!$order) {
        header("Location: orders.php");
        exit();
    }

    // Lấy chi tiết sản phẩm trong đơn hàng
    $items_sql = "SELECT oi.id, oi.order_id, oi.product_id, oi.quantity, oi.price, oi.specifications, p.name, p.image 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_sql);
    if (!$items_stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn sản phẩm: " . $conn->error);
    }
    
    $items_stmt->bind_param("i", $order_id);
    if (!$items_stmt->execute()) {
        throw new Exception("Lỗi thực thi truy vấn sản phẩm: " . $items_stmt->error);
    }
    
    $items = $items_stmt->get_result();

    // Xử lý cập nhật trạng thái đơn hàng
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $new_status = $_POST['status'];
        $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn cập nhật: " . $conn->error);
        }
        
        $update_stmt->bind_param("si", $new_status, $order_id);
        if (!$update_stmt->execute()) {
            throw new Exception("Lỗi cập nhật trạng thái: " . $update_stmt->error);
        }
        
        header("Location: order_detail.php?id=" . $order_id);
        exit();
    }
} catch (Exception $e) {
    die("Có lỗi xảy ra: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?php echo $order_id; ?> - Warstorm</title>
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
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
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
                            <a class="nav-link active" href="orders.php">
                                <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Chi tiết đơn hàng #<?php echo $order_id; ?></h2>
                    <a href="orders.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

                <!-- Thông tin đơn hàng -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Thông tin đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Mã đơn hàng:</strong> #<?php echo $order_id; ?></p>
                                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                <p><strong>Trạng thái:</strong> 
                                    <?php
                                    $status_class = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $status_text = [
                                        'pending' => 'Chờ xử lý',
                                        'processing' => 'Đang xử lý',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $status_class[$order['status']]; ?>">
                                        <?php echo $status_text[$order['status']]; ?>
                                    </span>
                                </p>
                                <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount']); ?> VNĐ</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Thông tin khách hàng</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Tên khách hàng:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cập nhật trạng thái -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Cập nhật trạng thái</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Trạng thái mới</label>
                                <select name="status" class="form-select">
                                    <?php foreach($status_text as $value => $text): ?>
                                        <option value="<?php echo $value; ?>" 
                                                <?php echo $order['status'] === $value ? 'selected' : ''; ?>>
                                            <?php echo $text; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" name="update_status" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cập nhật
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Chi tiết sản phẩm -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Chi tiết sản phẩm</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Thông số kỹ thuật</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($item = $items->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     class="product-image me-3" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            if (!empty($item['specifications'])) {
                                                $specs = json_decode($item['specifications'], true);
                                                if (is_array($specs)) {
                                                    echo implode(', ', $specs);
                                                } else {
                                                    echo htmlspecialchars($item['specifications']);
                                                }
                                            } else {
                                                echo "Không có thông số kỹ thuật";
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo number_format($item['price']); ?> VNĐ</td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['price'] * $item['quantity']); ?> VNĐ</td>
                                    </tr>
                                    <?php endwhile; ?>
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