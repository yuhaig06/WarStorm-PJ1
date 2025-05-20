<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';
$cart_count = 0;

// Lấy số lượng sản phẩm trong giỏ hàng
if (isset($_SESSION['user_id'])) {
    $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $_SESSION['user_id']);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $cart_count = $count_result->fetch_assoc()['total'] ?? 0;
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $product_id = (int)$_POST['product_id'];
    $quantity = 1; // Mặc định thêm 1 sản phẩm

    try {
        // Kiểm tra sản phẩm tồn tại và còn hàng
        $check_sql = "SELECT * FROM products WHERE id = ? AND stock > 0";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Sản phẩm không tồn tại hoặc đã hết hàng!");
        }

        $product = $result->fetch_assoc();

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $cart_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $cart_stmt = $conn->prepare($cart_sql);
        $cart_stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();

        if ($cart_result->num_rows > 0) {
            // Cập nhật số lượng
            $cart_item = $cart_result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock']) {
                throw new Exception("Số lượng vượt quá số lượng trong kho!");
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
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch products from database
$sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 8";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get categories for menu
$categories = [];
$cat_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
if ($cat_result->num_rows > 0) {
    while($row = $cat_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s;
            height: 100%;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .navbar-brand {
            font-weight: bold;
            color: #2c3e50;
        }
        .hero-section {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        .category-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(52, 152, 219, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .price-tag {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.2em;
            margin: 10px 0;
        }
        .stock-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(46, 204, 113, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .featured-section {
            background: #f8f9fa;
            padding: 3rem 0;
        }
        .section-title {
            position: relative;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #3498db;
        }
        .btn-add-to-cart {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.9em;
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
            font-size: 1.1em;
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
                        <a class="nav-link active" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Danh mục
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach($categories as $category): ?>
                                <li>
                                    <a class="dropdown-item" href="products.php?category=<?php echo urlencode($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="cart.php" class="btn btn-outline-primary me-2 position-relative">
                            <i class="fas fa-shopping-cart"></i> Giỏ hàng
                            <?php if($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Chào mừng đến với Warstorm </h1>
            <p class="lead mb-4">Chuyên cung cấp các sản phẩm máy tính, laptop chính hãng với giá tốt nhất</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="products.php" class="btn btn-light btn-lg">Xem sản phẩm</a>
                <a href="#featured" class="btn btn-outline-light btn-lg">Sản phẩm nổi bật</a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="featured-section">
        <div class="container">
            <h2 class="text-center section-title">Sản phẩm nổi bật</h2>
            <div class="row g-4">
                <?php foreach($products as $product): ?>
                <div class="col-md-3">
                    <div class="card product-card">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <span class="category-badge"><?php echo htmlspecialchars($product['category']); ?></span>
                            <?php if($product['stock'] > 0): ?>
                                <span class="stock-badge">Còn hàng</span>
                            <?php else: ?>
                                <span class="stock-badge bg-danger">Hết hàng</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-truncate"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="price-tag"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</p>
                            <div class="d-flex justify-content-between">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-primary">Chi tiết</a>
                                <?php if($product['stock'] > 0): ?>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="add_to_cart" class="btn btn-add-to-cart">
                                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
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
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="products.php" class="btn btn-primary btn-lg">Xem tất cả sản phẩm</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                        <h4>Miễn phí vận chuyển</h4>
                        <p>Cho đơn hàng từ 2 triệu đồng</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h4>Bảo hành chính hãng</h4>
                        <p>Bảo hành từ 12-24 tháng</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h4>Hỗ trợ 24/7</h4>
                        <p>Hotline: 0123 456 789</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về chúng tôi</h5>
                    <p>Warstorm Cửa hàng máy tính uy tín, chất lượng với nhiều năm kinh nghiệm trong lĩnh vực công nghệ.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh<br>
                        <i class="fas fa-phone"></i> 0123 456 789<br>
                        <i class="fas fa-envelope"></i> info@computershop.com<br>
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
                <p class="mb-0">@2024 Warstorm company</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 