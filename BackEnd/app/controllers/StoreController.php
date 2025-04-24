<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\CartModel;
use App\Config\Database;

class StoreController {
    private $db;
    private $productModel;
    private $orderModel;
    private $cartModel;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->orderModel = new OrderModel($this->db);
        $this->cartModel = new CartModel($this->db);
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
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to remove from cart'], 500);
        }
    }

    public function createOrder() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            
            $cart = $this->cartModel->getCart($userId);
            
            if (empty($cart['items'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'Cart is empty'], 400);
            }
            
            // Calculate total
            $total = 0;
            foreach ($cart['items'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            // Create order
            $orderData = [
                'user_id' => $userId,
                'total' => $total,
                'status' => 'pending',
                'items' => $cart['items']
            ];
            
            $orderId = $this->orderModel->create($orderData);
            
            if (!$orderId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Failed to create order'], 500);
            }
            
            // Clear cart
            $this->cartModel->removeItem(1); // Thay bằng hàm khác để test lỗi gạch chân
            
            // Update product stock
            foreach ($cart['items'] as $item) {
                $this->productModel->updateStock($item['product_id'], -$item['quantity']);
            }
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => ['order_id' => $orderId]
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to create order'], 500);
        }
    }

    public function getOrderHistory() {
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
                <a href="/PJ1/BackEnd/public/store/manage" class="edit-btn">Về trang quản lý sản phẩm</a>
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
        $categoryModel = new \App\Models\CategoryModel($this->db);
        $categories = $categoryModel->getAll();
        $data = [
            'categories' => $categories
        ];
        require __DIR__ . '/../views/store/category_list.php';
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}