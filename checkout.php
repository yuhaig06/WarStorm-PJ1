<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';
$cart_items = [];
$total = 0;
$user = null;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Lấy thông tin người dùng
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $_SESSION['user_id']);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();

    // Lấy thông tin giỏ hàng
    $sql = "SELECT c.*, p.name, p.price, p.image, p.stock 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total += $row['price'] * $row['quantity'];
    }

    // Xử lý thanh toán
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
        if (empty($cart_items)) {
            throw new Exception("Giỏ hàng của bạn đang trống!");
        }

        // Kiểm tra thông tin giao hàng
        $fullname = trim($_POST['fullname']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $note = trim($_POST['note']);
        $payment_method = $_POST['payment_method'];

        if (empty($fullname)) {
            throw new Exception("Vui lòng nhập họ tên!");
        }
        if (empty($phone)) {
            throw new Exception("Vui lòng nhập số điện thoại!");
        }
        if (empty($address)) {
            throw new Exception("Vui lòng nhập địa chỉ giao hàng!");
        }
        if (empty($payment_method)) {
            throw new Exception("Vui lòng chọn phương thức thanh toán!");
        }

        // Kiểm tra số lượng trong kho
        foreach ($cart_items as $item) {
            if ($item['quantity'] > $item['stock']) {
                throw new Exception("Sản phẩm " . $item['name'] . " chỉ còn " . $item['stock'] . " sản phẩm trong kho!");
            }
        }

        // Bắt đầu transaction
        $conn->begin_transaction();

        try {
            // Tạo đơn hàng mới
            $order_sql = "INSERT INTO orders (user_id, total_amount, status, fullname, phone, address, note, payment_method) 
                         VALUES (?, ?, 'pending', ?, ?, ?, ?, ?)";
            $order_stmt = $conn->prepare($order_sql);
            if (!$order_stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }
            
            $order_stmt->bind_param("idsssss", $_SESSION['user_id'], $total, $fullname, $phone, $address, $note, $payment_method);
            if (!$order_stmt->execute()) {
                throw new Exception("Lỗi tạo đơn hàng!");
            }
            
            $order_id = $conn->insert_id;

            // Thêm chi tiết đơn hàng
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price, specifications) VALUES (?, ?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            if (!$item_stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }

            foreach ($cart_items as $item) {
                $specifications = isset($item['specifications']) ? json_encode($item['specifications']) : null;
                $item_stmt->bind_param("iiids", $order_id, $item['product_id'], $item['quantity'], $item['price'], $specifications);
                if (!$item_stmt->execute()) {
                    throw new Exception("Lỗi thêm chi tiết đơn hàng!");
                }

                // Cập nhật số lượng trong kho
                $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $update_stock_stmt = $conn->prepare($update_stock_sql);
                if (!$update_stock_stmt) {
                    throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
                }
                
                $update_stock_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                if (!$update_stock_stmt->execute()) {
                    throw new Exception("Lỗi cập nhật số lượng trong kho!");
                }
            }

            // Xóa giỏ hàng
            $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
            $clear_cart_stmt = $conn->prepare($clear_cart_sql);
            if (!$clear_cart_stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
            }
            
            $clear_cart_stmt->bind_param("i", $_SESSION['user_id']);
            if (!$clear_cart_stmt->execute()) {
                throw new Exception("Lỗi xóa giỏ hàng!");
            }

            // Commit transaction
            $conn->commit();
            
            // Chuyển hướng đến trang thông báo đặt hàng thành công
            header("Location: order_success.php?id=" . $order_id);
            exit();
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $conn->rollback();
            throw $e;
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Checkout error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Computer Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .checkout-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        .checkout-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .checkout-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .checkout-item:last-child {
            border-bottom: none;
        }
        .checkout-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .checkout-item-title {
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #333;
        }
        .checkout-item-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .checkout-total {
            font-size: 1.5em;
            color: #e74c3c;
            font-weight: bold;
        }
        .payment-method {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #3498db;
            background-color: #f8f9fa;
        }
        .payment-method.selected {
            border-color: #3498db;
            background-color: #f8f9fa;
        }
        .payment-method input[type="radio"] {
            display: none;
        }
        .payment-method label {
            margin-bottom: 0;
            cursor: pointer;
        }
        .payment-method i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .btn-checkout {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2);
        }
        .btn-checkout:hover {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.3);
        }
        .btn-continue {
            background: #2ecc71;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.2);
        }
        .btn-continue:hover {
            background: #27ae60;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.3);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container checkout-container">
        <div class="checkout-card">
            <h2 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán</h2>

            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if(empty($cart_items)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Giỏ hàng của bạn đang trống
                </div>
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                </a>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="checkout-card">
                                <h4 class="mb-4">Thông tin giao hàng</h4>
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" 
                                           value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Địa chỉ giao hàng</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="note" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="checkout-card">
                                <h4 class="mb-4">Phương thức thanh toán</h4>
                                <div class="payment-method">
                                    <input type="radio" id="cod" name="payment_method" value="cod" required>
                                    <label for="cod">
                                        <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="bank" name="payment_method" value="bank">
                                    <label for="bank">
                                        <i class="fas fa-university"></i> Chuyển khoản ngân hàng
                                    </label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="momo" name="payment_method" value="momo">
                                    <label for="momo">
                                        <i class="fas fa-wallet"></i> Ví MoMo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="checkout-card">
                                <h4 class="mb-4">Đơn hàng của bạn</h4>
                                <?php foreach($cart_items as $item): ?>
                                    <div class="checkout-item">
                                        <div class="row align-items-center">
                                            <div class="col-3">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="checkout-item-image">
                                            </div>
                                            <div class="col-9">
                                                <h5 class="checkout-item-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                <div class="checkout-item-price">
                                                    <?php echo number_format($item['price'], 0, ',', '.'); ?> đ x <?php echo $item['quantity']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="mt-4">
                                    <h4>Tổng cộng:</h4>
                                    <div class="checkout-total">
                                        <?php echo number_format($total, 0, ',', '.'); ?> đ
                                    </div>
                                </div>

                                <button type="submit" name="checkout" class="btn btn-checkout w-100 mt-4">
                                    <i class="fas fa-check-circle"></i> Xác nhận đặt hàng
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Xử lý chọn phương thức thanh toán
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Bỏ chọn tất cả
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                    m.querySelector('input[type="radio"]').checked = false;
                });
                // Chọn phương thức được click
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>
</html> 