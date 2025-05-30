<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\WalletModel;
use PDO;

class OrderController extends Controller
{
    protected $orderModel;
    protected $productModel;
    protected $userModel;
    protected $db;
    protected $walletModel;
    
    // Các thuộc tính cần thiết
    protected $orderItemModel;
    protected $cartModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->walletModel = new WalletModel();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * API tạo đơn hàng mới
     */
    public function create()
    {
        // Kiểm tra phương thức
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json([
                'success' => false,
                'message' => 'Phương thức không được hỗ trợ'
            ], 405);
        }

        // Lấy dữ liệu từ request
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate dữ liệu
        if (empty($input['items']) || !is_array($input['items'])) {
            return $this->json([
                'success' => false,
                'message' => 'Giỏ hàng không được để trống'
            ], 400);
        }

        // Lấy thông tin khách hàng
        $customerInfo = $input['customerInfo'] ?? [];
        $requiredFields = ['fullname', 'phone', 'address'];
        
        foreach ($requiredFields as $field) {
            if (empty($customerInfo[$field])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Vui lòng điền đầy đủ thông tin giao hàng',
                    'missing_field' => $field
                ], 400);
            }
        }

        // Validate số điện thoại
        if (!preg_match('/^(0|\+84)[1-9][0-9]{8}$/', $customerInfo['phone'])) {
            return $this->json([
                'success' => false,
                'message' => 'Số điện thoại không hợp lệ'
            ], 400);
        }

        // Tính toán tổng tiền và kiểm tra sản phẩm
        $totalAmount = 0;
        $orderItems = [];
        
        try {
            $this->db->beginTransaction();
            
            // Lấy thông tin chi tiết từng sản phẩm
            foreach ($input['items'] as $item) {
                $product = $this->productModel->getById($item['id']);
                
                if (!$product) {
                    throw new \Exception("Sản phẩm không tồn tại: " . $item['id']);
                }
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Số lượng sản phẩm '{$product->name}' không đủ");
                }
                
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;
                
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
                
                // Trừ số lượng tồn kho (số âm để giảm số lượng)
                $quantityChange = -$item['quantity'];
                $this->productModel->updateStock($product->id, $quantityChange);
            }
            
            // Tạo đơn hàng
            $orderData = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'customer_name' => $customerInfo['fullname'],
                'customer_phone' => $customerInfo['phone'],
                'shipping_address' => $customerInfo['address'],
                'notes' => $customerInfo['note'] ?? '',
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount, // Có thể áp dụng giảm giá ở đây
                'payment_method' => $input['paymentMethod'] ?? 'cash',
                'payment_status' => $input['paymentMethod'] === 'cash' ? 'pending' : 'unpaid',
                'status' => 'pending'
            ];
            
            // Lưu đơn hàng
            $orderId = $this->orderModel->createOrder($orderData, $orderItems);
            
            $this->db->commit();
            
            return $this->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'order_id' => $orderId
            ]);
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            
            return $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo đơn hàng mới
     */
    public function createOrder($data)
    {
        // Kiểm tra dữ liệu đầu vào
        if (empty($data['items']) || !is_array($data['items'])) {
            return [
                'success' => false,
                'message' => 'Giỏ hàng không được để trống'
            ];
        }

        // Lấy thông tin người dùng từ session
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đặt hàng'
            ];
        }

        // Tạo đơn hàng
        $orderData = [
            'user_id' => $userId,
            'order_number' => $this->orderModel->generateOrderNumber(),
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'final_amount' => $data['final_amount'] ?? $data['total_amount'] ?? 0,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'items' => $data['items']
        ];

        // Lưu đơn hàng vào CSDL
        $orderId = $this->orderModel->create($orderData);

        if (!$orderId) {
            return [
                'success' => false,
                'message' => 'Không thể tạo đơn hàng'
            ];
        }

        return [
            'success' => true,
            'message' => 'Tạo đơn hàng thành công',
            'order_id' => $orderId
        ];

        return $this->json([
            'success' => true,
            'message' => 'Tạo đơn hàng thành công',
            'data' => [
                'order_id' => $orderId
            ]
        ]);
    }

    /**
     * Kiểm tra người dùng đã mua game chưa
     */
    private function hasUserPurchasedGame($userId, $gameId)
    {
        $sql = "SELECT COUNT(*) as count FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.user_id = :user_id AND oi.game_id = :game_id AND o.payment_status = 'paid'";
        $stmt = $this->orderModel->getPdo()->prepare($sql);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindParam(':game_id', $gameId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result && $result['count'] > 0;
    }

    /**
     * Thanh toán đơn hàng
     */
    public function processPayment($orderId)
    {
        $userId = $_SESSION['user_id'];
        $order = $this->orderModel->getOrderDetail($orderId);

        if (!$order) {
            return $this->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        // Kiểm tra quyền thanh toán
        if ($order['user_id'] != $userId) {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền thanh toán đơn hàng này'
            ], 403);
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order['payment_status'] !== 'pending') {
            return $this->json([
                'success' => false,
                'message' => 'Đơn hàng đã được thanh toán hoặc đã hủy'
            ], 400);
        }

        // Xử lý thanh toán theo phương thức
        switch ($order['payment_method']) {
            case 'wallet':
                // Kiểm tra nếu WalletModel tồn tại và có phương thức checkBalance
                if (method_exists($this->walletModel, 'checkBalance')) {
                    if (!$this->walletModel->checkBalance($userId, $order['final_amount'])) {
                        return $this->json([
                            'success' => false,
                            'message' => 'Số dư ví không đủ'
                        ], 400);
                    }
                    // Trừ tiền ví
                } else {
                    return $this->json([
                        'success' => false,
                        'message' => 'Phương thức thanh toán qua ví không khả dụng'
                    ], 400);
                }
                $this->walletModel->withdraw($userId, $order['final_amount'], 'Thanh toán đơn hàng #' . $order['order_number']);
                break;

            case 'credit_card':
            case 'bank_transfer':
                // TODO: Tích hợp cổng thanh toán
                break;

            default:
                return $this->json([
                    'success' => false,
                    'message' => 'Phương thức thanh toán không hợp lệ'
                ], 400);
        }

        // Cập nhật trạng thái đơn hàng
        if (!$this->orderModel->updatePaymentStatus($orderId, 'completed')) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái thanh toán'
            ], 500);
        }

        // Cập nhật trạng thái đơn hàng
        if (!$this->orderModel->updateOrderStatus($orderId, 'processing')) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái đơn hàng'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'message' => 'Thanh toán thành công'
        ]);
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function getOrderDetail($orderId)
    {
        $userId = $_SESSION['user_id'];
        $order = $this->orderModel->getOrderDetail($orderId);

        if (!$order) {
            return $this->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        // Kiểm tra quyền xem
        if ($order['user_id'] != $userId) {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền xem đơn hàng này'
            ], 403);
        }

        // Lấy chi tiết items
        $order['items'] = $this->orderModel->getOrderItems($orderId);

        // Lấy lịch sử giao dịch
        $order['transactions'] = $this->orderModel->getOrderTransactions($orderId);

        return $this->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Lấy danh sách đơn hàng của người dùng
     */
    public function getUserOrders()
    {
        // Validate dữ liệu
        $data = $this->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0',
            'status' => 'nullable|in:pending,processing,completed,cancelled'
        ]);

        $userId = $_SESSION['user_id'];
        $limit = $data['success'] ? ($data['data']['limit'] ?? 10) : 10;
        $offset = $data['success'] ? ($data['data']['offset'] ?? 0) : 0;
        $status = $data['success'] ? ($data['data']['status'] ?? null) : null;

        $orders = $this->orderModel->getUserOrders($userId, $limit, $offset, $status);

        return $this->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Hủy đơn hàng
     */
    public function cancelOrder($orderId)
    {
        $userId = $_SESSION['user_id'];
        $order = $this->orderModel->getOrderDetail($orderId);

        if (!$order) {
            return $this->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        // Kiểm tra quyền hủy
        if ($order['user_id'] != $userId) {
            return $this->json([
                'success' => false,
                'message' => 'Không có quyền hủy đơn hàng này'
            ], 403);
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order['order_status'] !== 'pending') {
            return $this->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng này'
            ], 400);
        }

        // Cập nhật trạng thái đơn hàng
        if (!$this->orderModel->updateOrderStatus($orderId, 'cancelled')) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng'
            ], 500);
        }

        // Hoàn tiền nếu đã thanh toán qua ví
        if ($order['payment_status'] === 'completed' && $order['payment_method'] === 'wallet' && method_exists($this->walletModel, 'deposit')) {
            $this->walletModel->deposit($userId, $order['final_amount'], 'Hoàn tiền đơn hàng #' . $order['order_number']);
        }

        return $this->json([
            'success' => true,
            'message' => 'Hủy đơn hàng thành công'
        ]);
    }

    /**
     * Lấy danh sách đơn hàng (cho admin)
     */
    public function getOrders()
    {
        // Validate dữ liệu
        $data = $this->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0',
            'status' => 'nullable|in:pending,processing,completed,cancelled',
            'payment_method' => 'nullable|in:wallet,credit_card,bank_transfer',
            'keyword' => 'nullable|min:2'
        ]);

        $limit = $data['success'] ? ($data['data']['limit'] ?? 10) : 10;
        $offset = $data['success'] ? ($data['data']['offset'] ?? 0) : 0;
        $status = $data['success'] ? ($data['data']['status'] ?? null) : null;
        $paymentMethod = $data['success'] ? ($data['data']['payment_method'] ?? null) : null;
        $keyword = $data['success'] ? ($data['data']['keyword'] ?? null) : null;

        $orders = [];
        if ($status) {
            $orders = $this->orderModel->getOrdersByStatus($status, $limit, $offset);
        } elseif ($paymentMethod) {
            $orders = $this->orderModel->getOrdersByPaymentMethod($paymentMethod, $limit, $offset);
        } elseif ($keyword) {
            $orders = $this->orderModel->searchOrders($keyword, $limit, $offset);
        } else {
            $orders = $this->orderModel->getUserOrders(null, $limit, $offset);
        }

        return $this->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng (cho admin)
     */
    public function updateOrderStatus($orderId)
    {
        // Validate dữ liệu
        $data = $this->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        if (!$data['success']) {
            return $this->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $data['errors']
            ], 422);
        }

        $order = $this->orderModel->getOrderDetail($orderId);

        if (!$order) {
            return $this->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        // Cập nhật trạng thái đơn hàng
        if (!$this->orderModel->updateOrderStatus($orderId, $data['data']['status'])) {
            return $this->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái đơn hàng'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công'
        ]);
    }

    /**
     * Xem chi tiết đơn hàng (admin)
     */
    public function view($id = null, $data = [])
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            if (isset($_GET['format']) && $_GET['format'] === 'json') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit();
            }
            header('Location: /PJ1/public/users/login');
            exit();
        }

        if ($id === null) {
            if (isset($_GET['format']) && $_GET['format'] === 'json') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Order ID is required']);
                exit();
            }
            header('Location: /PJ1/public/admin');
            exit();
        }

        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderDetail($id);
        
        if (!$order) {
            if (isset($_GET['format']) && $_GET['format'] === 'json') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Order not found']);
                exit();
            }
            parent::view('errors/404');
            return;
        }

        // Lấy danh sách sản phẩm trong đơn hàng
        $orderItems = $this->orderModel->getOrderItems($id);

        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy'
        ];

        // Nếu là yêu cầu JSON
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            header('Content-Type: application/json');
            echo json_encode([
                'order' => $order,
                'orderItems' => $orderItems,
                'statuses' => $statuses
            ]);
            exit();
        }

        // Nếu là yêu cầu thông thường
        $viewData = array_merge($data, [
            'pageTitle' => 'Chi tiết đơn hàng #' . $order['order_number'],
            'order' => $order,
            'orderItems' => $orderItems,
            'statuses' => $statuses
        ]);

        parent::view('admin/orders/view', $viewData);
    }

    /**
     * Chỉnh sửa đơn hàng (admin)
     */
    public function edit($id = null)
    {
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: /PJ1/public/users/login');
            exit();
        }

        if ($id === null) {
            header('Location: /PJ1/public/admin');
            exit();
        }


        // Xử lý khi form được gửi
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate dữ liệu
            $status = $_POST['status'] ?? '';
            $paymentStatus = $_POST['payment_status'] ?? '';
            $notes = $_POST['notes'] ?? '';

            // Cập nhật đơn hàng
            $result = $this->orderModel->updateOrderStatus($id, $status);
            
            if ($result) {
                // Cập nhật trạng thái thanh toán nếu cần
                if (!empty($paymentStatus)) {
                    $this->orderModel->updatePaymentStatus($id, $paymentStatus);
                }
                
                // Cập nhật ghi chú nếu có
                if (!empty($notes)) {
                    $this->orderModel->updateOrderNotes($id, $notes);
                }

                $_SESSION['success_message'] = 'Cập nhật đơn hàng thành công';
                header('Location: /PJ1/public/admin/orders/view/' . $id);
                exit();
            } else {
                $data['error_message'] = 'Có lỗi xảy ra khi cập nhật đơn hàng';
            }
        }

        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderDetail($id);
        
        if (!$order) {
            parent::view('errors/404');
            return;
        }

        // Lấy danh sách sản phẩm trong đơn hàng
        $orderItems = $this->orderModel->getOrderItems($id);

        $data = [
            'pageTitle' => 'Chỉnh sửa đơn hàng #' . $order['order_number'],
            'order' => $order,
            'orderItems' => $orderItems,
            'statuses' => [
                'pending' => 'Chờ xử lý',
                'processing' => 'Đang xử lý',
                'shipped' => 'Đang giao hàng',
                'delivered' => 'Đã giao hàng',
                'cancelled' => 'Đã hủy'
            ],
            'paymentStatuses' => [
                'pending' => 'Chờ thanh toán',
                'paid' => 'Đã thanh toán',
                'failed' => 'Thanh toán thất bại',
                'refunded' => 'Đã hoàn tiền'
            ]
        ];

        parent::view('admin/orders/edit', $data);
    }
} 