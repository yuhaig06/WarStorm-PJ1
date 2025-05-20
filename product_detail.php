<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';
$product = null;
$related_products = [];

// Kiểm tra product_id
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];

try {
    // Lấy thông tin sản phẩm
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        throw new Exception("Sản phẩm không tồn tại!");
    }
    
    $product = $result->fetch_assoc();
    
    // Lấy sản phẩm liên quan (cùng danh mục)
    $related_sql = "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4";
    $related_stmt = $conn->prepare($related_sql);
    if (!$related_stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    
    $related_stmt->bind_param("si", $product['category'], $product_id);
    $related_stmt->execute();
    $related_result = $related_stmt->get_result();
    
    while ($row = $related_result->fetch_assoc()) {
        $related_products[] = $row;
    }

    // Xử lý thêm vào giỏ hàng
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        $quantity = (int)$_POST['quantity'];
        
        if ($quantity <= 0) {
            throw new Exception("Số lượng phải lớn hơn 0!");
        }
        
        if ($quantity > $product['stock']) {
            throw new Exception("Số lượng vượt quá số lượng trong kho!");
        }

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Cập nhật số lượng
            $cart_item = $check_result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock']) {
                throw new Exception("Tổng số lượng vượt quá số lượng trong kho!");
            }

            $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("iii", $new_quantity, $_SESSION['user_id'], $product_id);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Lỗi cập nhật giỏ hàng!");
            }
        } else {
            // Thêm mới vào giỏ hàng
            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iii", $_SESSION['user_id'], $product_id, $quantity);
            
            if (!$insert_stmt->execute()) {
                throw new Exception("Lỗi thêm vào giỏ hàng!");
            }
        }

        $success = "Đã thêm sản phẩm vào giỏ hàng!";
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Product detail error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .product-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border-radius: 10px;
        }
        .product-title {
            font-size: 2em;
            margin-bottom: 20px;
        }
        .product-price {
            font-size: 1.5em;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        .product-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .product-meta {
            margin-bottom: 20px;
        }
        .product-meta span {
            margin-right: 20px;
        }
        .related-products {
            margin-top: 50px;
        }
        .related-product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .related-product-card:hover {
            transform: translateY(-5px);
        }
        .related-product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .related-product-title {
            font-size: 1.1em;
            margin-bottom: 10px;
            height: 2.4em;
            overflow: hidden;
        }
        .related-product-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .quantity-input {
            width: 100px;
            text-align: center;
        }
        .btn-add-to-cart {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add-to-cart:hover {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }
        .btn-add-to-cart i {
            font-size: 1.2em;
        }
        .btn-add-to-cart:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Liên hệ</a>
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

    <div class="container product-container">
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

        <?php if($product): ?>
            <div class="row">
                <div class="col-md-6">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                </div>
                <div class="col-md-6">
                    <div class="product-card">
                        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <div class="product-price">
                            <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                        </div>
                        <div class="product-meta">
                            <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category']); ?></span>
                            <span><i class="fas fa-box"></i> Còn lại: <?php echo $product['stock']; ?> sản phẩm</span>
                        </div>
                        <div class="product-description">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </div>
                        <?php if($product['stock'] > 0): ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Số lượng</label>
                                    <input type="number" class="form-control quantity-input" 
                                           id="quantity" name="quantity" value="1" min="1" 
                                           max="<?php echo $product['stock']; ?>" required>
                                </div>
                                <button type="submit" name="add_to_cart" class="btn btn-add-to-cart">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-add-to-cart" disabled>
                                <i class="fas fa-times-circle"></i> Hết hàng
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if(!empty($related_products)): ?>
                <div class="related-products">
                    <h3 class="mb-4">Sản phẩm liên quan</h3>
                    <div class="row">
                        <?php foreach($related_products as $related): ?>
                            <div class="col-md-3">
                                <a href="product_detail.php?id=<?php echo $related['id']; ?>" 
                                   class="text-decoration-none">
                                    <div class="related-product-card">
                                        <img src="<?php echo htmlspecialchars($related['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($related['name']); ?>" 
                                             class="related-product-image">
                                        <h5 class="related-product-title">
                                            <?php echo htmlspecialchars($related['name']); ?>
                                        </h5>
                                        <div class="related-product-price">
                                            <?php echo number_format($related['price'], 0, ',', '.'); ?> đ
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Không tìm thấy sản phẩm
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về chúng tôi</h5>
                    <p>Warstorm - Cửa hàng máy tính uy tín, chất lượng với nhiều năm kinh nghiệm trong lĩnh vực công nghệ.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh<br>
                        <i class="fas fa-phone"></i> 0123 456 789<br>
                        <i class="fas fa-envelope"></i> info@warstorm.com<br>
                        <i class="fas fa-clock"></i> 8:00 - 22:00 (Thứ 2 - Chủ nhật)
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="mt-3">
                        <h5>Liên kết nhanh</h5>
                        <ul class="list-unstyled">
                            <li><a href="products.php" class="text-light text-decoration-none">Sản phẩm</a></li>
                            <li><a href="contact.php" class="text-light text-decoration-none">Liên hệ</a></li>
                            <li><a href="about.php" class="text-light text-decoration-none">Giới thiệu</a></li>
                            <li><a href="policy.php" class="text-light text-decoration-none">Chính sách</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0">&copy; 2024 Warstorm. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 