<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\CartModel;
use App\Core\Database;

class StoreController {
    private $db;
    private $productModel;
    private $orderModel;
    private $cartModel;
    private $categoryModel;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->orderModel = new OrderModel($this->db);
        $this->cartModel = new CartModel($this->db);
        $this->categoryModel = new \App\Models\CategoryModel();
    }
    
    /**
     * Xử lý thêm sản phẩm mới
     */
    public function store() {
        // Kiểm tra xem có phải là POST request không
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /PJ1/public/admin/add');
            exit();
        }
        
        // Lấy dữ liệu từ form
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category_id = $_POST['category_id'] ?? '';
        $stock = $_POST['stock'] ?? 0;
        
        // Xử lý upload ảnh
        $image = 'default.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image = $fileName;
            }
        }
        
        // Lấy thông tin danh mục
        $category = $this->categoryModel->getCategoryById($category_id);
        $categoryName = $category ? $category->name : 'Chưa phân loại';
        
        // Chuẩn bị dữ liệu sản phẩm
        $productData = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'image_url' => '/PJ1/public/uploads/products/' . $image,
            'category' => $categoryName,
            'stock' => $stock
        ];
        
        // Thêm sản phẩm vào CSDL
        $result = $this->productModel->addProduct($productData);
        
        if ($result) {
            // Thành công, chuyển hướng về trang danh sách sản phẩm
            $_SESSION['success_message'] = 'Thêm sản phẩm thành công!';
            header('Location: /PJ1/public/admin');
        } else {
            // Thất bại, quay lại trang thêm sản phẩm với thông báo lỗi
            $_SESSION['error_message'] = 'Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại.';
            header('Location: /PJ1/public/admin/add');
        }
        exit();
    }

    public function getProducts() {
        try {
            $filters = $_GET;
            $products = $this->productModel->getAll($filters);
            return $this->jsonResponse(['success' => true, 'data' => $products]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch products'], 500);
        }
    }

    public function getProductById($id) {
        try {
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                return $this->jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            }
            
            return $this->jsonResponse(['success' => true, 'data' => $product]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch product'], 500);
        }
    }

    public function addToCart() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            
            if (!isset($data['productId']) || !isset($data['quantity'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'Product ID and quantity are required'], 400);
            }
            
            $product = $this->productModel->getById($data['productId']);
            
            if (!$product) {
                return $this->jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            }
            
            if ($product['stock'] < $data['quantity']) {
                return $this->jsonResponse(['success' => false, 'message' => 'Not enough stock'], 400);
            }
            
            $cartItem = $this->cartModel->addItem($userId, $data['productId'], $data['quantity']);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Product added to cart',
                'data' => $cartItem
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to add to cart'], 500);
        }
    }

    public function getCart() {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            
            $cart = $this->cartModel->getCart($userId);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch cart'], 500);
        }
    }

    public function updateCartItem() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            
            if (!isset($data['itemId']) || !isset($data['quantity'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'Item ID and quantity are required'], 400);
            }
            
            $cartItem = $this->cartModel->getItem($data['itemId']);
            
            if (!$cartItem || $cartItem['user_id'] !== $userId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Cart item not found'], 404);
            }
            
            $product = $this->productModel->getById($cartItem['product_id']);
            
            if ($product['stock'] < $data['quantity']) {
                return $this->jsonResponse(['success' => false, 'message' => 'Not enough stock'], 400);
            }
            
            $updatedItem = $this->cartModel->updateItem($data['itemId'], $data['quantity']);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Cart item updated',
                'data' => $updatedItem
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to update cart item'], 500);
        }
    }

    public function removeFromCart($itemId) {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            
            $cartItem = $this->cartModel->getItem($itemId);
            
            if (!$cartItem || $cartItem['user_id'] !== $userId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Cart item not found'], 404);
            }
            
            $this->cartModel->removeItem($itemId);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to remove item from cart'], 500);
        }
    }
    
    public function getUserOrders() {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                    return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
                }
                
                $orders = $this->orderModel->getUserOrders($userId);
                
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $orders
                ]);
            } catch (\Exception $e) {
                return $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch order history'], 500);
            }
    }

    // Trang chính Store (tích hợp tìm kiếm)
    public function index() {
        $keyword = $_GET['keyword'] ?? '';
        if ($keyword !== '') {
            $products = $this->productModel->searchByKeyword($keyword);
        } else {
            $products = $this->productModel->getAll([]);
        }
        require __DIR__ . '/../views/store/store.php';
    }

    // Trang thêm sản phẩm
    public function add() {
        // Lấy dữ liệu danh mục nếu cần
        $categoryModel = new \App\Models\CategoryModel($this->db);
        $categories = $categoryModel->getAll();
        // Chuẩn bị data cho view
        $data = [
            'name' => '',
            'name_err' => '',
            'category_id' => '',
            'category_id_err' => '',
            'categories' => $categories
        ];
        require __DIR__ . '/../views/store/add.php';
    }

    // Trang quản lý sản phẩm cho admin
    public function manage() {
        $products = $this->productModel->getAll([]);
        require __DIR__ . '/../views/store/manage.php';
    }

    // Trang chỉnh sửa sản phẩm
    public function edit($id = null) {
        $title = 'Chỉnh sửa sản phẩm | Store';
        $editViewPath = __DIR__ . '/../views/store/edit.php';
        // Nếu không có ID hoặc ID không hợp lệ, trả về HTML lỗi tối giản, có nhúng store.css
        if (!$id || !is_numeric($id)) {
            echo '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa sản phẩm | Store</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Store/css/store.css">
    <style>body { background: #111; text-align: center; margin-top: 80px; font-family: Montserrat, Arial, sans-serif; }</style>
</head>
<body>
    <div class="edit-error-container">
        <div class="edit-error-card">
            <div class="edit-error-icon">⚠️</div>
            <div class="edit-error-title" style="color:#f33;font-size:1.3rem">Thiếu hoặc sai ID sản phẩm!</div>
            <div class="edit-error-actions">
                <a href="/PJ1/public/store/manage" class="edit-btn">Về trang quản lý sản phẩm</a>
                <a href="javascript:history.back()" class="edit-btn edit-btn-outline">Quay lại</a>
            </div>
        </div>
    </div>
</body>
</html>';
            return;
        }
        $categoryModel = new \App\Models\CategoryModel($this->db);
        $categories = $categoryModel->getAll();
        $product = $this->productModel->getById($id);
        if (!$product) {
            $data = [
                'product' => false,
                'categories' => $categories,
                'title' => $title
            ];
            if (file_exists($editViewPath)) require $editViewPath;
            return;
        }
        $data = [
            'id' => $id,
            'name' => $product['name'],
            'name_err' => '',
            'category_id' => $product['category'],
            'category_id_err' => '',
            'categories' => $categories,
            'price' => $product['price'] ?? '',
            'price_err' => '',
            'stock' => $product['stock'] ?? '',
            'stock_err' => '',
            'description' => $product['description'] ?? '',
            'description_err' => '',
            'image' => $product['image'] ?? '',
            'image_err' => '',
            'product' => $product,
            'title' => $title
        ];
        if (file_exists($editViewPath)) require $editViewPath;
    }

    // Trang tìm kiếm sản phẩm
    public function search() {
        $keyword = $_GET['keyword'] ?? '';
        $categoryModel = new \App\Models\CategoryModel($this->db);
        $categories = $categoryModel->getAll();
        $products = [];
        if ($keyword !== '') {
            // Nếu ProductModel chưa có hàm searchByKeyword thì sẽ cần bổ sung sau
            $products = $this->productModel->searchByKeyword($keyword);
        }
        $data = [
            'keyword' => $keyword,
            'categories' => $categories,
            'products' => $products
        ];
        require __DIR__ . '/../views/store/search.php';
    }

    // Hiển thị sản phẩm theo danh mục
    public function category($categoryId) {
        $categoryModel = new \App\Models\CategoryModel($this->db);
        $productModel = $this->productModel;
        $categories = $categoryModel->getAll();
        $category = $categoryModel->findById($categoryId);
        if (!$category) {
            $data = [
                'error' => 'Danh mục không tồn tại!',
                'categories' => $categories
            ];
            require __DIR__ . '/../views/store/category.php';
            return;
        }
        $products = $productModel->getProductsByCategory($categoryId);
        $data = [
            'categories' => $categories,
            'category' => $category,
            'products' => $products
        ];
        require __DIR__ . '/../views/store/category.php';
    }

    // Hiển thị danh sách tất cả danh mục sản phẩm
    public function categoryList() {
        // Lấy dữ liệu danh mục
        $categories = [];
        if (class_exists('App\\Models\\CategoryModel')) {
            $categoryModel = new \App\Models\CategoryModel(); 
            $categories = $categoryModel->getAll();
        }
        // Load view category_list.php
        $data = ['categories' => $categories];
        require APPROOT . '/app/views/store/category_list.php';
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Xử lý thanh toán đơn hàng (phiên bản đơn giản)
     */
    public function checkout() {
        try {
            // Kiểm tra đăng nhập
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                return $this->jsonResponse([
                    'success' => false, 
                    'message' => 'Vui lòng đăng nhập để thanh toán'
                ], 401);
            }
            
            // Lấy thông tin giỏ hàng
            $cartItems = []; // TODO: Lấy giỏ hàng từ session hoặc database
            $totalAmount = 0;
            $discountAmount = 0;
            
            // Tính tổng tiền
            foreach ($cartItems as $item) {
                $totalAmount += ($item['price'] * $item['quantity']);
                if (isset($item['discount_price'])) {
                    $discountAmount += (($item['price'] - $item['discount_price']) * $item['quantity']);
                }
            }
            
            $finalAmount = $totalAmount - $discountAmount;
            
            // Tạo mã đơn hàng
            $orderNumber = 'ORD' . date('Ymd') . strtoupper(uniqid(''));
            
            // Lấy phương thức thanh toán từ form
            $paymentMethod = $_POST['payment'] ?? 'bank_transfer';
            
            // Tạo đơn hàng mới với đầy đủ thông tin
            $orderData = [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'notes' => $_POST['note'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Thực hiện insert vào database
            $sql = "INSERT INTO orders (
                        user_id, order_number, total_amount, discount_amount, final_amount,
                        payment_method, payment_status, order_status, notes, created_at, updated_at
                    ) VALUES (
                        :user_id, :order_number, :total_amount, :discount_amount, :final_amount,
                        :payment_method, :payment_status, :order_status, :notes, :created_at, :updated_at
                    )";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $orderData['user_id'],
                ':order_number' => $orderData['order_number'],
                ':total_amount' => $orderData['total_amount'],
                ':discount_amount' => $orderData['discount_amount'],
                ':final_amount' => $orderData['final_amount'],
                ':payment_method' => $orderData['payment_method'],
                ':payment_status' => $orderData['payment_status'],
                ':order_status' => $orderData['order_status'],
                ':notes' => $orderData['notes'],
                ':created_at' => $orderData['created_at'],
                ':updated_at' => $orderData['updated_at']
            ]);
            
            if ($result) {
                $orderId = $this->db->lastInsertId();
                
                // Thêm các mặt hàng vào bảng order_items
                if (!empty($cartItems)) {
                    $itemSql = "INSERT INTO order_items 
                                (order_id, product_id, quantity, price, discount_price, final_price, created_at) 
                                VALUES 
                                (:order_id, :product_id, :quantity, :price, :discount_price, :final_price, :created_at)";
                    $itemStmt = $this->db->prepare($itemSql);
                    
                    foreach ($cartItems as $item) {
                        $itemStmt->execute([
                            ':order_id' => $orderId,
                            ':product_id' => $item['id'],
                            ':quantity' => $item['quantity'],
                            ':price' => $item['price'],
                            ':discount_price' => $item['discount_price'] ?? null,
                            ':final_price' => $item['discount_price'] ?? $item['price'],
                            ':created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                
                // Xóa giỏ hàng sau khi đặt hàng thành công
                // TODO: Xóa giỏ hàng ở đây
                
                // Trả về thông báo thành công
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Đặt hàng thành công',
                    'order_id' => $orderId,
                    'order_number' => $orderNumber
                ]);
            } else {
                throw new \Exception('Không thể tạo đơn hàng: ' . implode(', ', $stmt->errorInfo()));
            }
            
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Lỗi khi xử lý đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }
}