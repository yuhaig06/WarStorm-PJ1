<?php

namespace App\Models;

use PDO;
use App\Core\Model;
use App\Core\Database;

class ProductModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Lấy tất cả sản phẩm
     * @return array Danh sách sản phẩm
     */
    public function getAllProducts()
    {
        try {
            $sql = "SELECT * FROM products ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Lỗi khi lấy danh sách sản phẩm: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy thông tin sản phẩm theo ID
     * @param int $id ID của sản phẩm
     * @return object|false Thông tin sản phẩm hoặc false nếu không tìm thấy
     */
    public function getProductById($id)
    {
        try {
            $sql = "SELECT * FROM products WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Lỗi khi lấy thông tin sản phẩm: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả sản phẩm
     * @return array Danh sách sản phẩm
     */
    /**
     * Thêm sản phẩm mới vào cơ sở dữ liệu
     * 
     * @param array $data Dữ liệu sản phẩm
     * @return bool Thành công hay thất bại
     */
    public function addProduct($data)
    {
        try {
            $sql = "INSERT INTO products (name, description, price, image, image_url, category, stock, original_image_name) 
                    VALUES (:name, :description, :price, :image, :image_url, :category, :stock, :original_image_name)";
            
            $stmt = $this->db->prepare($sql);
            
            $params = [
                ':name' => $data['name'],
                ':description' => $data['description'] ?? '',
                ':price' => $data['price'] ?? 0,
                ':image' => $data['image'] ?? 'default.jpg',
                ':image_url' => $data['image_url'] ?? '',
                ':category' => $data['category'] ?? '',
                ':stock' => $data['stock'] ?? 0,
                ':original_image_name' => $data['original_image_name'] ?? ($data['image'] ?? 'default.jpg')
            ];
            
            return $stmt->execute($params);
            
        } catch (\PDOException $e) {
            error_log('Lỗi khi thêm sản phẩm: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy tất cả sản phẩm
     * @return array Danh sách sản phẩm
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        if (isset($filters['category'])) {
            $sql .= " AND category = :category";
            $params[':category'] = $filters['category'];
        }
        
        if (isset($filters['search'])) {
            $sql .= " AND (name LIKE :search OR description LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        if (isset($filters['min_price'])) {
            $sql .= " AND price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (isset($filters['max_price'])) {
            $sql .= " AND price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
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
        } else {
            $sql .= " ORDER BY created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm theo ID
     * @param int $id ID sản phẩm
     * @return object Thông tin sản phẩm
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm theo danh mục
     * @param int $categoryId ID danh mục
     * @return array Danh sách sản phẩm
     */
    public function getProductsByCategory($categoryId)
    {
        $this->db->query('SELECT * FROM products WHERE category_id = :category_id ORDER BY created_at DESC');
        $this->db->bind(':category_id', $categoryId);
        return $this->db->resultSet();
    }

    /**
     * Thêm sản phẩm mới
     * @param array $data Dữ liệu sản phẩm
     * @return bool Kết quả thêm sản phẩm
     */
    public function create($data)
    {
        $sql = "INSERT INTO products (name, description, price, stock, category, image_url, created_at) 
                VALUES (:name, :description, :price, :stock, :category, :image_url, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':image_url', $data['image_url']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Cập nhật thông tin sản phẩm
     * @param array $data Dữ liệu sản phẩm
     * @return bool Kết quả cập nhật sản phẩm
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [];
        
        // Đảm bảo sử dụng đúng tên cột 'category' thay vì 'category_id'
        if (isset($data['category_id'])) {
            $data['category'] = $data['category_id'];
            unset($data['category_id']);
        }
        
        foreach ($data as $key => $value) {
            // Bỏ qua các trường không tồn tại trong bảng
            $allowedFields = ['name', 'description', 'price', 'stock', 'category', 'image', 'image_url', 'original_image_name'];
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = :$key";
                $values[":$key"] = $value;
            }
        }
        
        if (empty($fields)) {
            return false; // Không có trường nào để cập nhật
        }
        
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $values[':id'] = $id;
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (\PDOException $e) {
            error_log('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sản phẩm
     * @param int $id ID sản phẩm
     * @return bool Kết quả xóa sản phẩm
     */
    /**
     * Xóa sản phẩm
     * @param int $id ID sản phẩm cần xóa
     * @return bool True nếu xóa thành công, False nếu thất bại
     */
    public function delete($id)
    {
        try {
            // Kiểm tra xem sản phẩm có tồn tại không
            $product = $this->getProductById($id);
            if (!$product) {
                error_log("Không tìm thấy sản phẩm với ID: " . $id);
                return false;
            }

            // Xóa các bản ghi liên quan trong bảng order_items trước (nếu cần)
            $this->deleteOrderItemsByProductId($id);
            
            // Thực hiện xóa sản phẩm
            $sql = "DELETE FROM products WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            
            $result = $stmt->execute();
            
            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("Lỗi khi xóa sản phẩm: " . json_encode($error));
            }
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Lỗi PDO khi xóa sản phẩm: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa các bản ghi liên quan trong bảng order_items
     * @param int $productId ID sản phẩm
     * @return bool Kết quả thực hiện
     */
    private function deleteOrderItemsByProductId($productId)
    {
        try {
            $sql = "DELETE FROM order_items WHERE product_id = :product_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':product_id', $productId, \PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Lỗi khi xóa order_items: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật số lượng tồn kho
     * @param int $id ID sản phẩm
     * @param int $quantity Số lượng mua
     * @return bool Kết quả cập nhật
     */
    public function updateStock($id, $change)
    {
        $sql = "UPDATE products SET stock = stock + :change WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':change', $change);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Tìm kiếm sản phẩm
     * @param string $keyword Từ khóa tìm kiếm
     * @return array Danh sách sản phẩm
     */
    /**
     * Tìm kiếm sản phẩm theo từ khóa
     * @param string $keyword Từ khóa tìm kiếm
     * @return array Danh sách sản phẩm phù hợp
     */
    public function searchProducts($keyword)
    {
        try {
            $query = "SELECT id, name, slug, price, sale_price, image, description, stock 
                     FROM products 
                     WHERE name LIKE :keyword 
                     OR description LIKE :keyword 
                     AND status = 'active'
                     ORDER BY created_at DESC 
                     LIMIT 10";
            
            $stmt = $this->db->prepare($query);
            $searchTerm = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchTerm, PDO::PARAM_STR);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Search products error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm sản phẩm (thân thiện với controller Store)
     * @param string $keyword Từ khóa tìm kiếm
     * @return array Danh sách sản phẩm
     */
    public function searchByKeyword($keyword)
    {
        // Có thể tận dụng searchProducts nếu muốn
        return $this->searchProducts($keyword);
    }

    /**
     * Lấy danh sách danh mục sản phẩm
     * @return array Danh sách danh mục
     */
    public function getCategories()
    {
        $this->db->query('SELECT * FROM product_categories ORDER BY name ASC');
        return $this->db->resultSet();
    }

    /**
     * Lấy danh mục theo ID
     * @param int $id ID danh mục
     * @return object Thông tin danh mục
     */
    public function getCategoryById($id)
    {
        $this->db->query('SELECT * FROM product_categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
} 