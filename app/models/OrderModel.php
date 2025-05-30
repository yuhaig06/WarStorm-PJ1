<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'discount_amount',
        'final_amount', 'payment_method', 'payment_status', 'order_status',
        'notes'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Tạo mã đơn hàng duy nhất
     * @return string Mã đơn hàng có dạng ORD-YYYYMMDD-XXXX
     */
    public function generateOrderNumber()
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        
        // Lấy số thứ tự đơn hàng trong ngày
        $orderCount = $this->countOrdersToday() + 1;
        
        // Tạo mã đơn hàng với số thứ tự 4 chữ số (0001, 0002, ...)
        return $prefix . str_pad($orderCount, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Đếm tổng số đơn hàng
     */
    public function countOrders()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM orders");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    

    /**
     * Tạo đơn hàng mới
     */
    /**
     * Tạo đơn hàng mới với danh sách sản phẩm
     * @param array $orderData Thông tin đơn hàng
     * @param array $items Danh sách sản phẩm
     * @return int ID đơn hàng vừa tạo
     */
    public function createOrder($orderData, $items)
    {
        try {
            $this->db->beginTransaction();
            
            // Tạo đơn hàng
            $orderId = $this->create($orderData);
            if (!$orderId) {
                throw new \Exception('Không thể tạo đơn hàng');
            }

            // Tạo chi tiết đơn hàng
            $this->createOrderItems($orderId, $items);
            
            $this->db->commit();
            return $orderId;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Tạo chi tiết đơn hàng
     */
    protected function createOrderItems($orderId, $items)
    {
        $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($items as $item) {
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }
    }
    
    /**
     * Đếm số đơn hàng trong ngày hiện tại
     * @return int Số lượng đơn hàng
     */
    public function countOrdersToday()
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM orders 
            WHERE DATE(created_at) = :today
        ");
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     * @param int $orderId ID đơn hàng
     * @param string $status Trạng thái mới
     * @return bool Thành công hay không
     */
    public function updateOrderStatus($orderId, $status)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET order_status = :status, 
                    updated_at = NOW() 
                WHERE id = :id
            ");
            
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log('Lỗi khi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật trạng thái thanh toán
     * @param int $orderId ID đơn hàng
     * @param string $paymentStatus Trạng thái thanh toán mới
     * @return bool Thành công hay không
     */
    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET payment_status = :payment_status, 
                    updated_at = NOW() 
                WHERE id = :id
            ");
            
            $stmt->bindParam(':payment_status', $paymentStatus);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log('Lỗi khi cập nhật trạng thái thanh toán: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật ghi chú đơn hàng
     * @param int $orderId ID đơn hàng
     * @param string $notes Ghi chú mới
     * @return bool Thành công hay không
     */
    public function updateOrderNotes($orderId, $notes)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET notes = :notes, 
                    updated_at = NOW() 
                WHERE id = :id
            ");
            
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log('Lỗi khi cập nhật ghi chú đơn hàng: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy chi tiết đơn hàng
     * @param int $orderId ID đơn hàng
     * @return array|false Thông tin đơn hàng hoặc false nếu không tìm thấy
     */
    public function getOrderDetail($orderId)
    {
        try {
            $sql = "SELECT o.*, u.username, u.email, u.phone, u.full_name as customer_name 
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id 
                    WHERE o.id = :orderId";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Lỗi khi lấy chi tiết đơn hàng: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy danh sách sản phẩm trong đơn hàng
     * @param int $orderId ID đơn hàng
     * @return array|false Danh sách sản phẩm hoặc false nếu có lỗi
     */
    public function getOrderItems($orderId)
    {
        try {
            $sql = "SELECT oi.*, g.name as game_name, g.thumbnail, g.slug as game_slug, 
                           g.price as original_price, g.discount_price
                    FROM order_items oi
                    LEFT JOIN games g ON oi.game_id = g.id
                    WHERE oi.order_id = :orderId";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Lỗi khi lấy danh sách sản phẩm đơn hàng: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy danh sách đơn hàng gần đây
     * @param int $limit Số lượng đơn hàng cần lấy
     * @return array Danh sách đơn hàng
     */
    public function getRecentOrders($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, u.username as customer_name, u.email as customer_email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    
    /**
     * Đếm số lượng sản phẩm trong đơn hàng
     * @param int $orderId ID đơn hàng
     * @return int Số lượng sản phẩm
     */
    public function countItems($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT SUM(quantity) as total_items
            FROM order_items
            WHERE order_id = :order_id
            GROUP BY order_id
        ");
        $stmt->execute([':order_id' => $orderId]);
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        
        return $result ? $result->total_items : 0;
    }
    
    /**
     * Lấy thống kê đơn hàng
     * @return object Các chỉ số thống kê
     */
    public function getOrderStats()
    {
        $stats = new \stdClass();
        
        // Tổng số đơn hàng
        $stmt = $this->db->query("SELECT COUNT(*) as total_orders FROM orders");
        $stats->total_orders = $stmt->fetch(\PDO::FETCH_OBJ)->total_orders;
        
        // Đơn hàng mới trong ngày
        $stmt = $this->db->query("
            SELECT COUNT(*) as today_orders 
            FROM orders 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stats->today_orders = $stmt->fetch(\PDO::FETCH_OBJ)->today_orders;
        
        // Doanh thu tháng này
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(final_amount), 0) as monthly_revenue 
            FROM orders 
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
            AND order_status = 'completed'
        ");
        $stats->monthly_revenue = $stmt->fetch(\PDO::FETCH_OBJ)->monthly_revenue;
        
        // Đơn hàng đang chờ xử lý
        $stmt = $this->db->query("
            SELECT COUNT(*) as pending_orders 
            FROM orders 
            WHERE order_status = 'pending'
        ");
        $stats->pending_orders = $stmt->fetch(\PDO::FETCH_OBJ)->pending_orders;
        
        return $stats;
    }
    
    /**
     * @deprecated Sẽ bị xóa trong phiên bản tiếp theo
     */
    public function createOrderOld($data)
    {
        $this->db->beginTransaction();

        try {
            // Tạo đơn hàng
            $orderId = $this->create($data);
            if (!$orderId) {
                throw new \Exception('Không thể tạo đơn hàng');
            }

            // Tạo chi tiết đơn hàng
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $item['order_id'] = $orderId;
                    if (!$this->createOrderItem($item)) {
                        throw new \Exception('Không thể tạo chi tiết đơn hàng');
                    }
                }
            }

            // Tạo giao dịch thanh toán
            if (isset($data['transaction'])) {
                $data['transaction']['order_id'] = $orderId;
                if (!$this->createTransaction($data['transaction'])) {
                    throw new \Exception('Không thể tạo giao dịch thanh toán');
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Tạo chi tiết đơn hàng
     */
    public function createOrderItem($data)
    {
        $sql = "INSERT INTO order_items (order_id, game_id, price, discount_price, final_price) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['order_id'],
            $data['game_id'],
            $data['price'],
            $data['discount_price'] ?? null,
            $data['final_price']
        ]);
    }

    /**
     * Tạo giao dịch thanh toán
     */
    public function createTransaction($data)
    {
        $sql = "INSERT INTO transactions (order_id, user_id, amount, payment_method, transaction_id, status, payment_details) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['order_id'],
            $data['user_id'],
            $data['amount'],
            $data['payment_method'],
            $data['transaction_id'] ?? null,
            $data['status'] ?? 'pending',
            $data['payment_details'] ?? null
        ]);
    }

    /**
     * Lấy danh sách giao dịch của đơn hàng
     */
    public function getOrderTransactions($orderId)
    {
        $sql = "SELECT * FROM transactions WHERE order_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách đơn hàng của người dùng
     */
    public function getUserOrders($userId, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $params = [$userId];

        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        if ($offset !== null) {
            $sql .= " OFFSET ?";
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách đơn hàng theo trạng thái
     */
    public function getOrdersByStatus($status, $limit = null, $offset = null)
    {
        $sql = "SELECT o.*, u.username, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.order_status = ? 
                ORDER BY o.created_at DESC";
        
        $params = [$status];

        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        if ($offset !== null) {
            $sql .= " OFFSET ?";
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách đơn hàng theo phương thức thanh toán
     */
    public function getOrdersByPaymentMethod($method, $limit = null, $offset = null)
    {
        $sql = "SELECT o.*, u.username, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.payment_method = ? 
                ORDER BY o.created_at DESC";
        
        $params = [$method];

        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        if ($offset !== null) {
            $sql .= " OFFSET ?";
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm đơn hàng
     */
    public function searchOrders($keyword, $limit = null, $offset = null)
    {
        $sql = "SELECT o.*, u.username, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.order_number LIKE ? 
                OR u.username LIKE ? 
                OR u.email LIKE ? 
                ORDER BY o.created_at DESC";
        
        $keyword = "%{$keyword}%";
        $params = [$keyword, $keyword, $keyword];

        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        if ($offset !== null) {
            $sql .= " OFFSET ?";
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }



    /**
     * Lấy doanh thu theo khoảng thời gian (today, week, month, year)
     * @param string $period
     * @return float
     */
    public function getRevenueByPeriod($period)
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
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result && $result['revenue'] !== null ? (float)$result['revenue'] : 0.0;
    }

    /**
     * Lấy tổng doanh thu
     * @return float
     */
    public function getTotalRevenue()
    {
        $sql = "SELECT SUM(final_amount) as revenue FROM orders WHERE payment_status = 'paid'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result && $result['revenue'] !== null ? (float)$result['revenue'] : 0.0;
    }

    /**
     * Lấy doanh thu theo phương thức thanh toán
     * @return array
     */
    public function getRevenueByPaymentMethod()
    {
        $sql = "SELECT payment_method, SUM(final_amount) as revenue FROM orders WHERE payment_status = 'paid' GROUP BY payment_method";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy doanh thu theo danh mục
     * @return array
     */
    public function getRevenueByCategory()
    {
        $sql = "SELECT c.name as category, SUM(o.final_amount) as revenue
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN games g ON oi.game_id = g.id
                JOIN categories c ON g.category_id = c.id
                WHERE o.payment_status = 'paid'
                GROUP BY c.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
} 
