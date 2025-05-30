<?php

namespace App\Models;

use PDO;
use App\Core\Model;
use App\Core\Database;

class CategoryModel extends Model
{
    // Sửa lại access level của $db từ private thành protected để đúng chuẩn OOP và tránh lỗi kế thừa
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }
    
    /**
     * Lấy thông tin danh mục theo ID
     * 
     * @param int $id ID của danh mục
     * @return object|null Đối tượng danh mục hoặc null nếu không tìm thấy
     */
    public function getCategoryById($id)
    {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log('Lỗi khi lấy danh mục: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo danh mục mới
     */
    public function createCategory($data)
    {
        $sql = "INSERT INTO categories (name, slug, description, parent_id, type, created_at, updated_at) 
                VALUES (:name, :slug, :description, :parent_id, :type, NOW(), NOW())";
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':parent_id', $data['parent_id'] ?? null);
        $stmt->bindParam(':type', $data['type']);
        $stmt->execute();
        return $pdo->lastInsertId();
    }

    /**
     * Cập nhật danh mục
     */
    public function updateCategory($id, $data)
    {
        $sql = "UPDATE categories 
                SET name = :name, 
                    slug = :slug, 
                    description = :description, 
                    parent_id = :parent_id,
                    updated_at = NOW() 
                WHERE id = :id";
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':parent_id', $data['parent_id'] ?? null);
        return $stmt->execute();
    }

    /**
     * Xóa danh mục
     */
    public function deleteCategory($id)
    {
        // Kiểm tra xem có danh mục con không
        $hasChildren = $this->hasChildren($id);
        if ($hasChildren) {
            return false;
        }

        // Kiểm tra xem có nội dung liên quan không
        $hasContent = $this->hasContent($id);
        if ($hasContent) {
            return false;
        }

        $sql = "DELETE FROM categories WHERE id = :id";
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAll()
    {
        $sql = "SELECT * FROM categories ORDER BY id DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Lấy danh sách tất cả danh mục
     */
    public function getAllCategories()
    {
        // Kiểm tra nếu bảng categories chưa tồn tại
        $tableExists = $this->db->query("SHOW TABLES LIKE 'categories'");
        if (empty($tableExists)) {
            return [];
        }
        
        // Kiểm tra xem có dữ liệu trong bảng chưa
        $count = $this->db->query("SELECT COUNT(*) as count FROM categories");
        if ($count[0]->count == 0) {
            // Thêm các danh mục mặc định nếu bảng rỗng
            $defaultCategories = [
                ['name' => 'Bàn phím cơ', 'slug' => 'ban-phim-co', 'type' => 'product'],
                ['name' => 'Chuột gaming', 'slug' => 'chuot-gaming', 'type' => 'product'],
                ['name' => 'Tai nghe gaming', 'slug' => 'tai-nghe-gaming', 'type' => 'product'],
                ['name' => 'Lót chuột', 'slug' => 'lot-chuot', 'type' => 'product'],
                ['name' => 'Ghế gaming', 'slug' => 'ghe-gaming', 'type' => 'product'],
                ['name' => 'Màn hình', 'slug' => 'man-hinh', 'type' => 'product'],
                ['name' => 'Bàn gaming', 'slug' => 'ban-gaming', 'type' => 'product'],
                ['name' => 'Phụ kiện khác', 'slug' => 'phu-kien-khac', 'type' => 'product']
            ];
            
            $stmt = $this->db->prepare("INSERT INTO categories (name, slug, type, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            foreach ($defaultCategories as $category) {
                $stmt->execute([$category['name'], $category['slug'], $category['type']]);
            }
        }
        
        $sql = "SELECT * FROM categories WHERE type = 'product' ORDER BY name";
        $result = $this->db->query($sql);
        
        return $result ?: [];
    }

    /**
     * Lấy danh sách danh mục theo loại
     */
    public function getCategoriesByType($type)
    {
        $sql = "SELECT c.*, 
                       p.name as parent_name,
                       (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as child_count,
                       (SELECT COUNT(*) FROM news WHERE category_id = c.id) as news_count
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                WHERE c.type = :type
                ORDER BY c.parent_id, c.name";
        
        return $this->db->query($sql, [':type' => $type]);
    }

    /**
     * Lấy danh sách danh mục con
     */
    public function getChildCategories($parentId)
    {
        $sql = "SELECT c.*, 
                       p.name as parent_name,
                       (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as child_count,
                       (SELECT COUNT(*) FROM news WHERE category_id = c.id) as news_count
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                WHERE c.parent_id = :parent_id
                ORDER BY c.name";
        
        return $this->db->query($sql, [':parent_id' => $parentId]);
    }

    /**
     * Lấy chi tiết danh mục
     */
    public function getCategoryDetail($id)
    {
        $sql = "SELECT c.*, 
                       p.name as parent_name,
                       (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as child_count,
                       (SELECT COUNT(*) FROM news WHERE category_id = c.id) as news_count
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                WHERE c.id = :id";
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh mục theo ID
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Kiểm tra danh mục có tồn tại không
     */
    public function exists($id)
    {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE id = :id";
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Kiểm tra slug có tồn tại không
     */
    public function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE slug = :slug";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Kiểm tra danh mục có danh mục con không
     */
    private function hasChildren($id)
    {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE parent_id = :id";
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Kiểm tra danh mục có nội dung liên quan không
     */
    private function hasContent($id)
    {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM games WHERE category_id = :id) +
                (SELECT COUNT(*) FROM news WHERE category_id = :id) as count";
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Lấy cấu trúc cây danh mục
     */
    public function getCategoryTree($type = null)
    {
        $sql = "SELECT c.*, 
                       p.name as parent_name,
                       (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as child_count,
                       (SELECT COUNT(*) FROM games WHERE category_id = c.id) as game_count,
                       (SELECT COUNT(*) FROM news WHERE category_id = c.id) as news_count
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id";
        
        $params = [];
        if ($type) {
            $sql .= " WHERE c.type = :type";
            $params[':type'] = $type;
        }
        
        $sql .= " ORDER BY c.type, c.parent_id, c.name";
        
        $categories = $this->db->query($sql, $params);
        return $this->buildTree($categories);
    }

    /**
     * Xây dựng cây danh mục
     */
    private function buildTree($categories, $parentId = null)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $tree[] = $category;
            }
        }
        return $tree;
    }

    // Lấy tất cả danh mục và số lượng tin
    public function getCategoriesWithCount() {
        $this->db->query('SELECT c.*, COUNT(n.id) as news_count 
                         FROM categories c 
                         LEFT JOIN news n ON c.id = n.category_id AND n.status = "published" 
                         GROUP BY c.id 
                         ORDER BY c.name ASC');
        
        return $this->db->resultSet();
    }

    // Lấy danh mục theo slug
    public function getCategoryBySlug($slug) {
        $this->db->query('SELECT * FROM categories WHERE slug = :slug');
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    // Lấy danh mục con
    public function getSubCategories($parentId) {
        $this->db->query('SELECT c.*, COUNT(n.id) as news_count 
                         FROM categories c 
                         LEFT JOIN news n ON c.id = n.category_id AND n.status = "published" 
                         WHERE c.parent_id = :parent_id 
                         GROUP BY c.id 
                         ORDER BY c.name ASC');
        
        $this->db->bind(':parent_id', $parentId);
        return $this->db->resultSet();
    }

    // Lấy danh mục gốc (không có parent)
    public function getRootCategories() {
        $this->db->query('SELECT c.*, COUNT(n.id) as news_count 
                         FROM categories c 
                         LEFT JOIN news n ON c.id = n.category_id AND n.status = "published" 
                         WHERE c.parent_id IS NULL 
                         GROUP BY c.id 
                         ORDER BY c.name ASC');
        
        return $this->db->resultSet();
    }

    // Lấy đường dẫn danh mục (breadcrumb)
    public function getCategoryPath($categoryId) {
        $path = [];
        $currentId = $categoryId;

        while ($currentId) {
            $this->db->query('SELECT id, name, slug, parent_id FROM categories WHERE id = :id');
            $this->db->bind(':id', $currentId);
            $category = $this->db->single();
            
            if ($category) {
                array_unshift($path, $category);
                $currentId = $category->parent_id;
            } else {
                break;
            }
        }

        return $path;
    }
} 