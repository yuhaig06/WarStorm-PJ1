<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';
$cart_items = [];
$total = 0;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Lấy thông tin giỏ hàng
    $sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image, p.stock 
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

    // Xử lý cập nhật số lượng
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
        $cart_id = (int)$_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity <= 0) {
            throw new Exception("Số lượng phải lớn hơn 0!");
        }
        
        // Kiểm tra số lượng trong kho
        $check_sql = "SELECT p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        
        $check_stmt->bind_param("i", $cart_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $stock = $check_result->fetch_assoc()['stock'];
        
        if ($quantity > $stock) {
            throw new Exception("Số lượng vượt quá số lượng trong kho!");
        }
        
        $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        
        $update_stmt->bind_param("iii", $quantity, $cart_id, $_SESSION['user_id']);
        if (!$update_stmt->execute()) {
            throw new Exception("Lỗi cập nhật số lượng!");
        }
        
        $success = "Đã cập nhật số lượng!";
        header("Location: cart.php");
        exit();
    }

    // Xử lý xóa sản phẩm
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
        $cart_id = (int)$_POST['cart_id'];
        
        $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        if (!$delete_stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }
        
        $delete_stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
        if (!$delete_stmt->execute()) {
            throw new Exception("Lỗi xóa sản phẩm!");
        }
        
        $success = "Đã xóa sản phẩm khỏi giỏ hàng!";
        header("Location: cart.php");
        exit();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Cart error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .cart-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        .cart-header {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-header h2 {
            margin: 0;
            font-size: 1.5em;
            color: #333;
        }
        .cart-item {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .cart-item-title {
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #333;
        }
        .cart-item-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .cart-item-quantity {
            width: 60px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        .btn-update {
            background: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .btn-remove {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .cart-summary {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-total {
            font-size: 1.5em;
            color: #e74c3c;
            font-weight: bold;
        }
        .btn-continue {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
        }
        .btn-continue:hover {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }
        .btn-checkout {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9em;
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
        .alert {
            border-radius: 4px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }
        .empty-cart {
            text-align: center;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .empty-cart i {
            font-size: 3em;
            color: #bdc3c7;
            margin-bottom: 15px;
        }
        .empty-cart h3 {
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container cart-container">
        <div class="cart-header">
            <h2><i class="fas fa-shopping-cart"></i> Giỏ hàng</h2>
        </div>

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

        <?php if(empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Giỏ hàng trống</h3>
                <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng</p>
                <a href="products.php" class="btn btn-continue">
                    <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                </a>
            </div>
        <?php else: ?>
            <?php foreach($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                        </div>
                        <div class="col-md-4">
                            <h5 class="cart-item-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <div class="cart-item-price">
                                <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
                            </div>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="" class="d-flex align-items-center">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <input type="number" class="form-control cart-item-quantity me-2" name="quantity" 
                                       value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                <button type="submit" name="update_quantity" class="btn btn-update">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-2">
                            <div class="cart-item-price">
                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ
                            </div>
                        </div>
                        <div class="col-md-1">
                            <form method="POST" action="">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-remove" 
                                        onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Tổng cộng:</h4>
                        <div class="cart-total">
                            <?php echo number_format($total, 0, ',', '.'); ?> đ
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="products.php" class="btn btn-continue me-2">
                            <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                        </a>
                        <a href="checkout.php" class="btn btn-checkout">
                            <i class="fas fa-credit-card"></i> Thanh toán
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 