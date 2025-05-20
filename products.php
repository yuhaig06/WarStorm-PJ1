<?php
session_start();
require_once 'config/database.php';

$error = '';
$products = [];
$categories = [];
$selected_category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

try {
    // Lấy danh sách danh mục
    $cat_sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category";
    $cat_result = $conn->query($cat_sql);
    if ($cat_result) {
        while ($row = $cat_result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
    }

    // Xây dựng câu truy vấn
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];
    $types = "";

    // Lọc theo danh mục
    if (!empty($selected_category)) {
        $sql .= " AND category = ?";
        $params[] = $selected_category;
        $types .= "s";
    }

    // Tìm kiếm theo tên
    if (!empty($search)) {
        $sql .= " AND name LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
    }

    // Lọc theo giá
    if (!empty($min_price)) {
        $sql .= " AND price >= ?";
        $params[] = $min_price;
        $types .= "d";
    }
    if (!empty($max_price)) {
        $sql .= " AND price <= ?";
        $params[] = $max_price;
        $types .= "d";
    }

    // Sắp xếp
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY price DESC";
            break;
        case 'name_asc':
            $sql .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY name DESC";
            break;
        default:
            $sql .= " ORDER BY created_at DESC";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Products error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .products-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .filter-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 20px;
            transition: transform 0.3s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .product-title {
            font-size: 1.1em;
            margin-bottom: 10px;
            height: 2.4em;
            overflow: hidden;
        }
        .product-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .product-category {
            font-size: 0.9em;
            color: #666;
        }
        .product-stock {
            font-size: 0.9em;
        }
        .stock-in {
            color: #28a745;
        }
        .stock-out {
            color: #dc3545;
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
                        <a class="nav-link active" href="products.php">Sản phẩm</a>
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

    <div class="container products-container">
        <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Bộ lọc -->
            <div class="col-md-3">
                <div class="filter-card">
                    <h4 class="mb-3">Bộ lọc</h4>
                    <form method="GET" action="">
                        <!-- Tìm kiếm -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Nhập tên sản phẩm...">
                        </div>

                        <!-- Danh mục -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Danh mục</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Tất cả</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>" 
                                            <?php echo $selected_category === $category ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Khoảng giá -->
                        <div class="mb-3">
                            <label class="form-label">Khoảng giá</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="min_price" 
                                           value="<?php echo htmlspecialchars($min_price); ?>" 
                                           placeholder="Từ">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="max_price" 
                                           value="<?php echo htmlspecialchars($max_price); ?>" 
                                           placeholder="Đến">
                                </div>
                            </div>
                        </div>

                        <!-- Sắp xếp -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sắp xếp</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                                <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                                <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Tên Z-A</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="col-md-9">
                <?php if(empty($products)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Không tìm thấy sản phẩm nào
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($products as $product): ?>
                            <div class="col-md-4 mb-4">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                    <div class="product-card">
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="product-image">
                                        <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <div class="product-price">
                                            <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                                        </div>
                                        <div class="product-category">
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category']); ?>
                                        </div>
                                        <div class="product-stock">
                                            <?php if($product['stock'] > 0): ?>
                                                <span class="stock-in">
                                                    <i class="fas fa-check-circle"></i> Còn hàng
                                                </span>
                                            <?php else: ?>
                                                <span class="stock-out">
                                                    <i class="fas fa-times-circle"></i> Hết hàng
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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