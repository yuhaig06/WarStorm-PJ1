<?php

namespace App\Controllers;

use App\Core\Controller;
use PDO;
use App\Models\UserModel;
use App\Models\GameModel;
use App\Models\NewsModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\WalletModel;
use App\Models\ProductModel;
use Exception;

class AdminController extends Controller
{

    /**
     * Xóa sản phẩm
     * @param int $id ID sản phẩm cần xóa
     */
    public function deleteProduct($id)
    {
        // Kiểm tra xem có phải là yêu cầu AJAX không
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                 strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        // Thiết lập header JSON nếu là AJAX
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
        }
        
        // Kiểm tra quyền admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            if ($isAjax) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này']);
                exit();
            } else {
                $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
                header('Location: /PJ1/public/admin/products');
                exit();
            }
        }

        try {
            // Kiểm tra xem sản phẩm có tồn tại không
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                if ($isAjax) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
                    exit();
                } else {
                    $_SESSION['error'] = 'Không tìm thấy sản phẩm';
                    header('Location: /PJ1/public/admin/products');
                    exit();
                }
            }

            // Thực hiện xóa
            $result = $this->productModel->delete($id);
            
            if ($isAjax) {
                if ($result) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Xóa sản phẩm thành công',
                        'data' => ['id' => $id]
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Có lỗi xảy ra khi xóa sản phẩm'
                    ]);
                }
                exit();
            } else {
                if ($result) {
                    $_SESSION['success'] = 'Xóa sản phẩm thành công';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi xóa sản phẩm';
                }
                header('Location: /PJ1/public/admin/products');
                exit();
            }
        } catch (\Exception $e) {
            error_log("Lỗi khi xóa sản phẩm: " . $e->getMessage());
            if ($isAjax) {
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]);
                exit();
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
                header('Location: /PJ1/public/admin/products');
                exit();
            }
        }
    }

    /**
     * Thêm người dùng mới
     */
    public function addUser()
    {
        // Kiểm tra nếu là POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $fullName = $_POST['full_name'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($username)) {
                $errors[] = 'Tên đăng nhập không được để trống';
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            }
            
            if (empty($password)) {
                $errors[] = 'Vui lòng nhập mật khẩu';
            }
            
            // Kiểm tra username hoặc email đã tồn tại chưa
            $existingUser = $this->userModel->getUserByUsernameOrEmail($username, $email);
            if ($existingUser) {
                if (strtolower($existingUser['username']) === strtolower($username)) {
                    $errors[] = 'Tên đăng nhập đã được sử dụng';
                }
                if (strtolower($existingUser['email']) === strtolower($email)) {
                    $errors[] = 'Email đã được đăng ký';
                }
            }
            
            // Nếu không có lỗi, tiến hành tạo tài khoản
            if (empty($errors)) {
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'full_name' => $fullName,
                    'role' => $role,
                    'is_verified' => 1, // Đánh dấu tài khoản đã xác minh
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $userId = $this->userModel->create($userData);
                
                if ($userId) {
                    $_SESSION['success'] = 'Thêm người dùng thành công';
                    header('Location: /PJ1/public/admin/users');
                    exit();
                } else {
                    $errors[] = 'Có lỗi xảy ra khi thêm người dùng';
                }
            }
            
            // Nếu có lỗi, lưu vào session để hiển thị
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            // Nếu không phải POST request, chuyển hướng về trang danh sách
            header('Location: /PJ1/public/admin/users');
            exit();
        }
    }
    /**
     * @var UserModel
     */
    protected $userModel;
    
    /**
     * @var GameModel
     */
    protected $gameModel;
    
    /**
     * @var NewsModel
     */
    protected $newsModel;
    
    /**
     * @var CategoryModel
     */
    protected $categoryModel;
    
    /**
     * @var OrderModel
     */
    protected $orderModel;
    
    /**
     * @var WalletModel
     */
    protected $walletModel;
    
    /**
     * @var ProductModel
     */
    protected $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->gameModel = new GameModel();
        $this->newsModel = new NewsModel();
        $this->categoryModel = new CategoryModel();
        $this->orderModel = new OrderModel();
        $this->walletModel = new WalletModel();
        $this->productModel = new ProductModel();
        
        // Kiểm tra đăng nhập và quyền admin cho tất cả các phương thức
        $this->checkAdmin();
    }
    
    /**
     * Trang tổng quan admin
     */
    
    /**
     * Lấy tổng doanh thu
     */
    private function getTotalRevenue()
    {
        $sql = "SELECT SUM(final_amount) as revenue FROM orders WHERE payment_status = 'paid'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result && $result['revenue'] !== null ? (float)$result['revenue'] : 0.0;
    }
    
    /**
     * Lấy dữ liệu doanh thu 6 tháng gần nhất
     */
    private function getRevenueData()
    {
        $data = [];
        $months = 6;
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as total 
                FROM orders 
                WHERE status = 'completed' 
                AND DATE_FORMAT(created_at, '%Y-%m') = ?
            ");
            $stmt->execute([$month]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $data[] = $result ? (float)$result->total : 0;
        }
        
        return $data;
    }
    
    /**
     * Lấy nhãn cho biểu đồ doanh thu (6 tháng gần nhất)
     */
    private function getRevenueLabels()
    {
        $labels = [];
        $months = 6;
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $labels[] = date('M Y', strtotime("-$i months"));
        }
        
        return $labels;
    }
    
    /**
     * Lấy danh sách đơn hàng gần đây
     */
    private function getRecentOrders($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, u.username as customer_name, 
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * API endpoint để lấy danh sách đơn hàng gần đây
     */
    public function apiRecentOrders()
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Không có quyền truy cập']);
            exit;
        }
        
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $orders = $this->getRecentOrders($limit);
            
            header('Content-Type: application/json');
            echo json_encode($orders);
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Lỗi khi tải đơn hàng']);
        }
        exit;
    }
    
    /**
     * Lấy danh sách sản phẩm bán chạy
     */
    private function getTopSellingProducts($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.image_url, 
                   SUM(oi.quantity) as total_quantity,
                   SUM(oi.quantity * oi.price) as total_revenue
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'completed'
            GROUP BY p.id, p.name, p.image_url
            ORDER BY total_quantity DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Lấy tổng số người dùng
     */
    private function getTotalUsers()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? (int)$result->total : 0;
    }
    
    /**
     * Lấy tổng số sản phẩm
     */
    private function getTotalProducts()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? (int)$result->total : 0;
    }
    
    /**
     * Lấy tổng số đơn hàng
     */
    private function getTotalOrders()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? (int)$result->total : 0;
    }
    
    /**
     * Kiểm tra đăng nhập và quyền admin
     */
    protected function checkAdmin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /PJ1/public/users/login');
            exit();
        }
        
        // Kiểm tra quyền admin dựa trên trường role trong bảng users
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        if (!$user) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Không tìm thấy thông tin người dùng.';
            exit();
        }
        
        // Kiểm tra nếu role không phải là admin
        if (!isset($user->role) || $user->role !== 'admin') {
            header('HTTP/1.0 403 Forbidden');
            echo 'Bạn không có quyền truy cập trang quản trị. Vui lòng đăng nhập bằng tài khoản admin.';
            exit();
        }
    }
    
    /**
     * Trang cài đặt hệ thống
     */
    public function settings()
    {
        $data = [
            'title' => 'Cài đặt hệ thống',
            'current_page' => 'settings'
        ];
        
        // Xử lý cập nhật cài đặt nếu có dữ liệu POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý cập nhật cài đặt ở đây
            // Ví dụ: lưu cài đặt vào database hoặc file cấu hình
            
            $_SESSION['message'] = 'Cập nhật cài đặt thành công!';
            $_SESSION['message_type'] = 'success';
            
            // Làm mới trang để tránh gửi lại form
            header('Location: /PJ1/public/admin/settings');
            exit();
        }
        
        $this->view('admin/settings', $data);
    }
    
    /**
     * Xử lý thêm sản phẩm mới
     */
    public function store()
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /PJ1/public/users/login');
            exit();
        }
        
        // Khởi tạo mảng dữ liệu và lỗi
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => trim($_POST['price'] ?? '0'),
            'category' => '',
            'stock' => trim($_POST['stock'] ?? '0'),
            'name_err' => '',
            'price_err' => '',
            'stock_err' => '',
            'image_err' => '',
            'message' => '',
            'message_type' => ''
        ];
        
        // Lấy tên danh mục từ category_id
        if (!empty($_POST['category_id'])) {
            $category = $this->categoryModel->getCategoryById($_POST['category_id']);
            if ($category) {
                $data['category'] = $category->name;
            }
        }
        
        // Validate dữ liệu
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên sản phẩm';
        }
        
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
            $data['price_err'] = 'Vui lòng nhập giá hợp lệ';
        }
        
        if (!is_numeric($data['stock']) || $data['stock'] < 0) {
            $data['stock_err'] = 'Vui lòng nhập số lượng hợp lệ';
        }
        
        // Xử lý upload ảnh
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/PJ1/FrontEnd/Store/img/products/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        
        if (empty($_FILES['image']['name'])) {
            $data['image_err'] = 'Vui lòng chọn ảnh sản phẩm';
        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $data['image_err'] = 'Có lỗi xảy ra khi tải lên ảnh';
        } elseif (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $data['image_err'] = 'Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG hoặc GIF';
        } elseif ($_FILES['image']['size'] > $maxFileSize) {
            $data['image_err'] = 'Kích thước ảnh không được vượt quá 2MB';
        }
        
        // Nếu không có lỗi
        if (empty($data['name_err']) && empty($data['price_err']) && 
            empty($data['stock_err']) && empty($data['image_err'])) {
            
            // Tạo tên file mới
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $fileName = uniqid('product_') . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            // Di chuyển file tải lên vào thư mục đích
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                // Tạo URL ảnh
                $imageUrl = '/PJ1/FrontEnd/Store/img/products/' . $fileName;
                
                // Chuẩn bị dữ liệu sản phẩm
                $productData = [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'image' => $fileName,
                    'image_url' => $imageUrl,
                    'category' => $data['category'],
                    'stock' => $data['stock']
                ];
                
                // Thêm sản phẩm vào CSDL
                if ($this->productModel->addProduct($productData)) {
                    $data['message'] = 'Thêm sản phẩm thành công!';
                    $data['message_type'] = 'success';
                    
                    // Reset form
                    $data['name'] = '';
                    $data['description'] = '';
                    $data['price'] = '0';
                    $data['stock'] = '0';
                } else {
                    $data['message'] = 'Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại.';
                    $data['message_type'] = 'danger';
                }
            } else {
                $data['message'] = 'Có lỗi khi lưu ảnh. Vui lòng thử lại.';
                $data['message_type'] = 'danger';
            }
        } else {
            $data['message'] = 'Vui lòng kiểm tra lại thông tin nhập vào.';
            $data['message_type'] = 'danger';
        }
        
        // Lấy danh sách danh mục để hiển thị lại form
        $data['categories'] = $this->categoryModel->getAllCategories();
        
        // Hiển thị lại form với thông báo lỗi
        $this->view('admin/add', $data);
    }
    
    /**
     * Hiển thị trang quản lý người dùng
     */
    public function users()
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /PJ1/public/users/login');
            exit();
        }
        
        try {
            // Lấy danh sách người dùng
            $users = $this->userModel->getAllUsers();
            
            $data = [
                'pageTitle' => 'Quản lý người dùng',
                'users' => $users,
                'message' => $_SESSION['message'] ?? '',
                'message_type' => $_SESSION['message_type'] ?? ''
            ];
            
            // Xóa thông báo sau khi đã hiển thị
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            
            // Load view
            $this->view('admin/users', $data);
            
        } catch (Exception $e) {
            // Ghi log lỗi
            error_log('Lỗi khi lấy danh sách người dùng: ' . $e->getMessage());
            
            $data = [
                'pageTitle' => 'Lỗi',
                'message' => 'Đã xảy ra lỗi khi tải danh sách người dùng. Vui lòng thử lại sau.',
                'message_type' => 'danger'
            ];
            
            $this->view('admin/users', $data);
        }
    }
    
    /**
     * Hiển thị form chỉnh sửa sản phẩm
     * 
     * @param int $id ID của sản phẩm cần chỉnh sửa
     * @return void
     */
    public function edit($id = null)
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vui lòng đăng nhập để tiếp tục';
            $_SESSION['message_type'] = 'warning';
            header('Location: /PJ1/public/users/login');
            exit();
        }

        // Nếu không có ID, hiển thị danh sách sản phẩm
        if (empty($id)) {
            try {
                // Lấy danh sách sản phẩm
                $products = $this->productModel->getAllProducts();
                
                $data = [
                    'pageTitle' => 'Chọn sản phẩm cần chỉnh sửa',
                    'products' => $products,
                    'message' => $_SESSION['message'] ?? '',
                    'message_type' => $_SESSION['message_type'] ?? ''
                ];
                
                // Xóa thông báo sau khi đã hiển thị
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                
                // Load view
                $this->view('admin/edit', $data);
                return;
            } catch (Exception $e) {
                error_log('Lỗi khi lấy danh sách sản phẩm: ' . $e->getMessage());
                $_SESSION['message'] = 'Đã xảy ra lỗi khi tải danh sách sản phẩm';
                $_SESSION['message_type'] = 'danger';
                header('Location: /PJ1/public/admin');
                exit();
            }
        }

        try {
            // Lấy thông tin sản phẩm
            $product = $this->productModel->getProductById($id);
            
            if (!$product) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            // Lấy danh sách danh mục
            $categories = $this->categoryModel->getAllCategories();

            $data = [
                'pageTitle' => 'Chỉnh sửa sản phẩm',
                'product' => $product,
                'categories' => $categories,
                'id' => $id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'category_id' => $product->category_id ?? '',
                'name_err' => '',
                'price_err' => '',
                'stock_err' => '',
                'image_err' => ''
            ];

            $this->view('admin/edit', $data);
        } catch (Exception $e) {
            // Ghi log lỗi
            error_log('Lỗi khi lấy thông tin sản phẩm: ' . $e->getMessage());
            
            $data = [
                'pageTitle' => 'Lỗi',
                'message' => 'Đã xảy ra lỗi khi tải thông tin sản phẩm. Vui lòng thử lại sau.',
                'message_type' => 'danger'
            ];
            
            $this->view('admin/edit', $data);
        }
    }

    /**
     * Xử lý cập nhật sản phẩm
     * 
     * @param int $id ID của sản phẩm cần cập nhật
     */
    public function update($id = null)
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /PJ1/public/users/login');
            exit();
        }

        // Lấy thông tin sản phẩm hiện tại
        $currentProduct = $this->productModel->getProductById($id);
        if (!$currentProduct) {
            $_SESSION['message'] = 'Không tìm thấy sản phẩm.';
            $_SESSION['message_type'] = 'danger';
            header('Location: /PJ1/public/admin/products');
            exit();
        }

        // Khởi tạo mảng dữ liệu và lỗi
        $data = [
            'id' => $id,
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => trim($_POST['price'] ?? '0'),
            'stock' => trim($_POST['stock'] ?? '0'),
            'category_id' => trim($_POST['category_id'] ?? ''),
            'name_err' => '',
            'price_err' => '',
            'stock_err' => '',
            'image_err' => '',
            'message' => '',
            'message_type' => ''
        ];

        // Lấy tên danh mục từ category_id
        $category = null;
        if (!empty($data['category_id'])) {
            $category = $this->categoryModel->getCategoryById($data['category_id']);
            if ($category) {
                $data['category'] = $category->name;
            }
        }

        // Validate dữ liệu
        if (empty($data['name'])) {
            $data['name_err'] = 'Vui lòng nhập tên sản phẩm';
        }
        
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
            $data['price_err'] = 'Vui lòng nhập giá hợp lệ';
        }
        
        if (!is_numeric($data['stock']) || $data['stock'] < 0) {
            $data['stock_err'] = 'Vui lòng nhập số lượng hợp lệ';
        }

        // Xử lý upload ảnh nếu có
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/PJ1/FrontEnd/Store/img/products/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        $imagePath = $currentProduct->image;
        $imageUrl = $currentProduct->image_url;

        if (!empty($_FILES['image']['name'])) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $data['image_err'] = 'Có lỗi xảy ra khi tải lên ảnh';
            } elseif (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $data['image_err'] = 'Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG hoặc GIF';
            } elseif ($_FILES['image']['size'] > $maxFileSize) {
                $data['image_err'] = 'Kích thước ảnh không được vượt quá 2MB';
            } else {
                // Tạo tên file mới
                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $fileName = uniqid('product_') . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;
                
                // Di chuyển file tải lên vào thư mục đích
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    // Xóa ảnh cũ nếu tồn tại và không phải ảnh mặc định
                    if ($currentProduct->image && $currentProduct->image !== 'default.jpg') {
                        $oldImagePath = $uploadDir . $currentProduct->image;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    // Cập nhật đường dẫn ảnh mới
                    $imagePath = $fileName;
                    $imageUrl = '/PJ1/FrontEnd/Store/img/products/' . $fileName;
                } else {
                    $data['image_err'] = 'Có lỗi khi lưu ảnh. Vui lòng thử lại.';
                }
            }
        }

        // Nếu không có lỗi
        if (empty($data['name_err']) && empty($data['price_err']) && 
            empty($data['stock_err']) && empty($data['image_err'])) {
            
            // Chuẩn bị dữ liệu sản phẩm
            $productData = [
                'id' => $id,
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'category_id' => $data['category_id'],
                'category' => $data['category'] ?? '',
                'image' => $imagePath,
                'image_url' => $imageUrl,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Cập nhật sản phẩm
            if ($this->productModel->update($productData['id'], $productData)) {
                $_SESSION['message'] = 'Cập nhật sản phẩm thành công!';
                $_SESSION['message_type'] = 'success';
                header('Location: /PJ1/public/admin/products');
                exit();
            } else {
                $data['message'] = 'Có lỗi xảy ra khi cập nhật sản phẩm. Vui lòng thử lại.';
                $data['message_type'] = 'danger';
            }
        } else {
            $data['message'] = 'Vui lòng kiểm tra lại thông tin nhập vào.';
            $data['message_type'] = 'danger';
        }
        
        // Lấy danh sách danh mục để hiển thị lại form
        $data['categories'] = $this->categoryModel->getAllCategories();
        $data['product'] = $currentProduct;
        
        // Hiển thị lại form với thông báo lỗi
        $this->view('admin/edit', $data);
    }

    public function add()
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /PJ1/public/users/login');
            exit();
        }
        
        // Xử lý khi submit form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $category_id = $_POST['category_id'] ?? '';
            $stock = $_POST['stock'] ?? 0;
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = 'Vui lòng nhập tên sản phẩm';
            }
            
            if (empty($price) || !is_numeric($price) || $price < 0) {
                $errors['price'] = 'Giá sản phẩm không hợp lệ';
            }
            
            if (empty($category_id)) {
                $errors['category_id'] = 'Vui lòng chọn danh mục';
            }
            
            // Xử lý upload ảnh
            $image = 'default.jpg';
            $originalImageName = '';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $originalImageName = basename($_FILES['image']['name']); // Lưu tên gốc
                $fileExt = strtolower(pathinfo($originalImageName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExt, $allowedExts)) {
                    $fileName = uniqid() . '.' . $fileExt; // Tạo tên mới ngẫu nhiên
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $image = $fileName;
                    } else {
                        $errors['image'] = 'Có lỗi khi tải lên ảnh';
                    }
                } else {
                    $errors['image'] = 'Định dạng ảnh không được hỗ trợ';
                }
            }
            
            // Nếu không có lỗi, thêm sản phẩm vào CSDL
            if (empty($errors)) {
                $productData = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category_id' => $category_id,
                    'image' => $image,
                    'original_image_name' => $originalImageName ?: $image,
                    'stock' => $stock
                ];
                
                $productModel = new ProductModel($this->db);
                if ($productModel->addProduct($productData)) {
                    $_SESSION['success_message'] = 'Thêm sản phẩm thành công!';
                    header('Location: /PJ1/public/admin/add');
                    exit();
                } else {
                    $errors['general'] = 'Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại.';
                }
            }
            
            // Nếu có lỗi, quay lại form với dữ liệu đã nhập
            $data = [
                'pageTitle' => 'Thêm sản phẩm mới',
                'categories' => $this->categoryModel->getAllCategories(),
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $category_id,
                'stock' => $stock,
                'name_err' => $errors['name'] ?? '',
                'price_err' => $errors['price'] ?? '',
                'category_id_err' => $errors['category_id'] ?? '',
                'image_err' => $errors['image'] ?? '',
                'general_error' => $errors['general'] ?? ''
            ];
            
            $this->view('admin/add', $data);
            return;
        }
        
        // Nếu không phải là POST request, hiển thị form trống
        $data = [
            'pageTitle' => 'Thêm sản phẩm mới',
            'categories' => $this->categoryModel->getAllCategories(),
            'name' => '',
            'description' => '',
            'price' => '',
            'category_id' => '',
            'stock' => 0,
            'name_err' => '',
            'price_err' => '',
            'category_id_err' => '',
            'image_err' => '',
            'general_error' => ''
        ];
        
        $this->view('admin/add', $data);
    }
    
    /**
     * Lấy tổng doanh thu trong tháng hiện tại
     */
    protected function getMonthlyRevenue()
    {
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $lastDayOfMonth = date('Y-m-t 23:59:59');
        
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(final_amount), 0) as total 
            FROM orders 
            WHERE created_at BETWEEN :start_date AND :end_date
            AND payment_status = 'completed'
        ");
        
        $stmt->execute([
            ':start_date' => $firstDayOfMonth,
            ':end_date' => $lastDayOfMonth
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    /**
     * Lấy số người dùng mới trong tháng
     */
    protected function getNewUsersThisMonth()
    {
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $lastDayOfMonth = date('Y-m-t 23:59:59');
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM users 
            WHERE created_at BETWEEN :start_date AND :end_date
        ");
        
        $stmt->execute([
            ':start_date' => $firstDayOfMonth,
            ':end_date' => $lastDayOfMonth
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Lấy số sản phẩm sắp hết hàng (số lượng < 10)
     */
    protected function getLowStockProducts()
    {
        $threshold = 10; // Ngưỡng số lượng tồn kho thấp
        
        try {
            $sql = "SELECT COUNT(*) as count FROM products WHERE stock > 0 AND stock <= :threshold";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt && $stmt->execute([':threshold' => $threshold])) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['count'] ?? 0;
            }
            return 0;
        } catch (Exception $e) {
            error_log('Lỗi khi đếm sản phẩm sắp hết hàng: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Lấy thống kê trạng thái đơn hàng
     */
    protected function getOrderStatusStats()
    {
        $stats = [
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0
        ];
        
        try {
            $sql = "SELECT status, COUNT(*) as count FROM orders WHERE status IS NOT NULL GROUP BY status";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt && $stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (isset($row['status'])) {
                        $status = strtolower($row['status']);
                        $stats[$status] = isset($row['count']) ? (int)$row['count'] : 0;
                    }
                }
            }
        } catch (Exception $e) {
            // Ghi log lỗi nếu cần
            error_log('Lỗi khi lấy thống kê trạng thái đơn hàng: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    public function index()
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /PJ1/public/users/login');
            exit();
        }
        
        // Lấy dữ liệu thống kê
        $orderStatusStats = $this->getOrderStatusStats();
        
        // Đảm bảo $orderStatusStats là mảng
        if (!is_array($orderStatusStats)) {
            $orderStatusStats = [
                'pending' => 0,
                'processing' => 0,
                'shipped' => 0,
                'delivered' => 0,
                'cancelled' => 0
            ];
        }
        
        $data = [
            'pageTitle' => 'Trang Quản Trị',
            'current_page' => 'dashboard',
            // Thống kê tổng quan
            'userCount' => $this->userModel->countUsers(),
            'postCount' => $this->newsModel->countPosts(),
            'orderCount' => $this->orderModel->countOrders(),
            'orderCountToday' => $this->orderModel->countOrdersToday(),
            'monthlyRevenue' => $this->getMonthlyRevenue(),
            'newUsersThisMonth' => $this->getNewUsersThisMonth(),
            'lowStockProducts' => $this->getLowStockProducts(),
            'orderStatusStats' => $orderStatusStats,
            // Dữ liệu cho biểu đồ doanh thu
            'revenueData' => $this->getRevenueData(),
            'revenueLabels' => $this->getRevenueLabels(),
            // Dữ liệu cho biểu đồ trạng thái đơn hàng
            'orderStatusData' => [
                $orderStatusStats['processing'] ?? 0,
                $orderStatusStats['delivered'] ?? 0,
                $orderStatusStats['shipped'] ?? 0,
                $orderStatusStats['pending'] ?? 0,
                $orderStatusStats['cancelled'] ?? 0
            ],
            // Đơn hàng gần đây
            'recentOrders' => $this->getRecentOrders(5),
            // Sản phẩm bán chạy
            'topProducts' => $this->getTopSellingProducts(5),
            // Người dùng gần đây
            'recentUsers' => $this->userModel->getRecentUsers(5)
        ];
        
        // Load view
        $this->view('admin/admin', $data);
    }

    public function getDashboardStats()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $stats = [
            'users' => [
                'total' => $this->getUserCount(),
            ],
            'games' => [
                'total' => $this->getGameCount(),
            ],
            'news' => [
                'total' => $this->getNewsCount(),
            ],
            'orders' => [
                'total' => $this->getOrderCount(),
            ],
            'revenue' => [
                'today' => $this->getRevenueByPeriod('today'),
                'week' => $this->getRevenueByPeriod('week'),
                'month' => $this->getRevenueByPeriod('month'),
                'year' => $this->getRevenueByPeriod('year')
            ]
        ];

        return $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Quản lý người dùng
     */
    public function getUsers()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,suspended,banned',
            'role' => 'nullable|in:user,moderator,admin',
            'search' => 'nullable|string|min:2'
        ]);

        $limit = $data['success'] ? ($data['data']['limit'] ?? 10) : 10;
        $offset = $data['success'] ? ($data['data']['offset'] ?? 0) : 0;
        $status = $data['success'] ? ($data['data']['status'] ?? null) : null;
        $role = $data['success'] ? ($data['data']['role'] ?? null) : null;
        $search = $data['success'] ? ($data['data']['search'] ?? null) : null;

        $stmt = $this->userModel->getPdo()->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Cập nhật trạng thái người dùng
     */
    public function updateUserStatus($userId)
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'status' => 'required|in:active,suspended,banned',
            'reason' => 'required|string|min:10'
        ]);

        if (!$data['success']) {
            return $this->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $data['errors']
            ], 422);
        }

        $stmt = $this->userModel->getPdo()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        // Không cho phép thay đổi trạng thái admin
        if ($user['role'] === 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không thể thay đổi trạng thái admin'
            ], 400);
        }

        $stmt = $this->userModel->getPdo()->prepare("UPDATE users SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $data['data']['status']);
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        $success = $stmt->execute();

        if (!$success) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái người dùng'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái người dùng thành công'
        ]);
    }

    /**
     * Cập nhật vai trò người dùng
     */
    public function updateUserRole($userId)
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'role' => 'required|in:user,moderator,admin'
        ]);

        if (!$data['success']) {
            return $this->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $data['errors']
            ], 422);
        }

        $stmt = $this->userModel->getPdo()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $stmt = $this->userModel->getPdo()->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->bindParam(':role', $data['data']['role']);
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        $success = $stmt->execute();

        if (!$success) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể cập nhật vai trò người dùng'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'message' => 'Cập nhật vai trò người dùng thành công'
        ]);
    }

    /**
     * Quản lý danh mục
     */
    public function getCategories()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $stmt = $this->categoryModel->getPdo()->prepare("SELECT * FROM categories");
        $stmt->execute();
        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Tạo danh mục mới
     */
    public function createCategory()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'name' => 'required|string|min:2|max:50',
            'slug' => 'required|string|min:2|max:50',
            'description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer',
            'type' => 'required|in:game,news'
        ]);

        if (!$data['success']) {
            return $this->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $data['errors']
            ], 422);
        }

        $stmt = $this->categoryModel->getPdo()->prepare("INSERT INTO categories (name, slug, description, parent_id, type) VALUES (:name, :slug, :description, :parent_id, :type)");
        $stmt->bindParam(':name', $data['data']['name']);
        $stmt->bindParam(':slug', $data['data']['slug']);
        $stmt->bindParam(':description', $data['data']['description']);
        $stmt->bindParam(':parent_id', $data['data']['parent_id']);
        $stmt->bindParam(':type', $data['data']['type']);
        $stmt->execute();
        $categoryId = $this->categoryModel->getPdo()->lastInsertId();

        return $this->json([
            'success' => true,
            'message' => 'Tạo danh mục thành công',
            'data' => ['category_id' => $categoryId]
        ]);
    }

    /**
     * Cập nhật danh mục
     */
    public function updateCategory($id)
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'name' => 'required|string|min:2|max:50',
            'slug' => 'required|string|min:2|max:50',
            'description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer'
        ]);

        if (!$data['success']) {
            return $this->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $data['errors']
            ], 422);
        }

        $stmt = $this->categoryModel->getPdo()->prepare("UPDATE categories SET name = :name, slug = :slug, description = :description, parent_id = :parent_id WHERE id = :id");
        $stmt->bindParam(':name', $data['data']['name']);
        $stmt->bindParam(':slug', $data['data']['slug']);
        $stmt->bindParam(':description', $data['data']['description']);
        $stmt->bindParam(':parent_id', $data['data']['parent_id']);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $success = $stmt->execute();

        if (!$success) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể cập nhật danh mục'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'message' => 'Cập nhật danh mục thành công'
        ]);
    }

    /**
     * Xóa danh mục
     */
    public function deleteCategory($id)
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $stmt = $this->categoryModel->getPdo()->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $success = $stmt->execute();

        if (!$success) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công'
        ]);
    }

    /**
     * Lấy thống kê doanh thu
     */
    public function getRevenueStats()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'period' => 'nullable|in:day,week,month,year'
        ]);

        $period = $data['success'] ? ($data['data']['period'] ?? 'month') : 'month';

        // Lấy tổng doanh thu
        $stmt = $this->orderModel->getPdo()->prepare("SELECT SUM(final_amount) as revenue FROM orders WHERE payment_status = 'paid'");
        $stmt->execute();
        $totalRevenue = $stmt->fetch(\PDO::FETCH_ASSOC)['revenue'];

        $stats = [
            'total' => $totalRevenue,
            'by_period' => $this->getRevenueByPeriod($period),
            'by_payment_method' => $this->getRevenueByPaymentMethod(),
            'by_category' => $this->getRevenueByCategory()
        ];

        return $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Lấy thống kê người dùng
     */
    public function getUserStats()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'period' => 'nullable|in:day,week,month,year'
        ]);

        $period = $data['success'] ? ($data['data']['period'] ?? 'month') : 'month';

        // Đếm tổng số user
        $stmt = $this->userModel->getPdo()->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $totalUser = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        $stats = [
            'total' => $totalUser,
            'by_period' => $this->getUserCountByPeriod($period)
        ];

        return $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Lấy thống kê nội dung
     */
    public function getContentStats()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        // Validate dữ liệu
        $data = $this->validate([
            'period' => 'nullable|in:day,week,month,year'
        ]);

        $period = $data['success'] ? ($data['data']['period'] ?? 'month') : 'month';

        $stats = [
            'games' => [
                'total' => $this->getGameCount(),
                'by_period' => $this->getGameCountByPeriod($period)
            ],
            'news' => [
                'total' => $this->getNewsCount(),
                'by_period' => $this->getNewsCountByPeriod($period)
            ]
        ];

        return $this->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // Đếm tổng số game
    private function getGameCount()
    {
        $stmt = $this->gameModel->getPdo()->prepare("SELECT COUNT(*) as total FROM games");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // Đếm số game theo khoảng thời gian
    private function getGameCountByPeriod($period)
    {
        $dateCondition = $this->getDateCondition($period, 'created_at');
        $sql = "SELECT COUNT(*) as total FROM games WHERE $dateCondition";
        $stmt = $this->gameModel->getPdo()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // Đếm tổng số tin tức
    private function getNewsCount()
    {
        $stmt = $this->newsModel->getPdo()->prepare("SELECT COUNT(*) as total FROM news");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // Đếm số tin tức theo khoảng thời gian
    private function getNewsCountByPeriod($period)
    {
        $dateCondition = $this->getDateCondition($period, 'published_at');
        $sql = "SELECT COUNT(*) as total FROM news WHERE $dateCondition";
        $stmt = $this->newsModel->getPdo()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // Đếm tổng số người dùng
    private function getUserCount()
    {
        $stmt = $this->userModel->getPdo()->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // Đếm số người dùng theo khoảng thời gian
    private function getUserCountByPeriod($period)
    {
        $dateCondition = $this->getDateCondition($period, 'created_at');
        $sql = "SELECT COUNT(*) as total FROM users WHERE $dateCondition";
        $stmt = $this->userModel->getPdo()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // Đếm tổng số đơn hàng
    private function getOrderCount()
    {
        $stmt = $this->orderModel->getPdo()->prepare("SELECT COUNT(*) as total FROM orders");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // Hàm phụ trợ sinh điều kiện thời gian
    private function getDateCondition($period, $column)
    {
        switch ($period) {
            case 'day':
                return "DATE($column) = CURDATE()";
            case 'week':
                return "YEARWEEK($column, 1) = YEARWEEK(CURDATE(), 1)";
            case 'month':
                return "YEAR($column) = YEAR(CURDATE()) AND MONTH($column) = MONTH(CURDATE())";
            case 'year':
                return "YEAR($column) = YEAR(CURDATE())";
            default:
                return '1=1';
        }
    }

    // Lấy doanh thu theo khoảng thời gian
    private function getRevenueByPeriod($period)
    {
        $dateCondition = '';
        switch ($period) {
            case 'today':
                $dateCondition = "DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $dateCondition = "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $dateCondition = "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
                break;
            case 'year':
                $dateCondition = "YEAR(created_at) = YEAR(CURDATE())";
                break;
            default:
                $dateCondition = '1=1';
                break;
        }
        $sql = "SELECT SUM(final_amount) as revenue FROM orders WHERE payment_status = 'paid' AND $dateCondition";
        $stmt = $this->orderModel->getPdo()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result && $result['revenue'] !== null ? (float)$result['revenue'] : 0.0;
    }

    // Lấy doanh thu theo phương thức thanh toán
    private function getRevenueByPaymentMethod()
    {
        $sql = "SELECT payment_method, SUM(final_amount) as revenue FROM orders WHERE payment_status = 'paid' GROUP BY payment_method";
        $stmt = $this->orderModel->getPdo()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy doanh thu theo danh mục
    private function getRevenueByCategory()
    {
        $sql = "SELECT c.name as category, SUM(o.final_amount) as revenue
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN games g ON oi.game_id = g.id
                JOIN categories c ON g.category_id = c.id
                WHERE o.payment_status = 'paid'
                GROUP BY c.id";
        $stmt = $this->orderModel->getPdo()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Xóa người dùng
     * @param int $id ID của người dùng cần xóa
     */
    public function delete($id)
    {
        // Kiểm quyền admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này';
            header('Location: /PJ1/public/admin/users');
            exit();
        }

        try {
            // Kiểm tra xem user có tồn tại không
            $user = $this->userModel->getById($id);
            if (!$user) {
                $_SESSION['error'] = 'Không tìm thấy người dùng';
                header('Location: /PJ1/public/admin/users');
                exit();
            }

            // Không cho phép xóa tài khoản admin
            if ($user['role'] === 'admin') {
                $_SESSION['error'] = 'Không thể xóa tài khoản admin';
                header('Location: /PJ1/public/admin/users');
                exit();
            }

            // Thực hiện xóa
            if ($this->userModel->delete($id)) {
                $_SESSION['success'] = 'Xóa người dùng thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa người dùng';
            }
        } catch (\Exception $e) {
            error_log("Error in delete user: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa người dùng: ' . $e->getMessage();
        }

        header('Location: /PJ1/public/admin/users');
        exit();
    }
    
    /**
     * Kiểm tra nếu request là AJAX
     */
    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}