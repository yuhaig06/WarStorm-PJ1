<?php

namespace App\Models;

use PDO;

class PostModel {
    private $db;
    private $table = 'news';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Tìm kiếm bài viết theo từ khóa
     * @param string $keyword Từ khóa tìm kiếm
     * @return array Mảng các bài viết phù hợp
     */
    public function searchPosts($keyword) {
        $query = "SELECT id, title, slug, excerpt, image, created_at 
                 FROM {$this->table} 
                 WHERE title LIKE :keyword 
                 OR content LIKE :keyword 
                 ORDER BY created_at DESC 
                 LIMIT 5";
        
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm, PDO::PARAM_STR);
        
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Search posts error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách bài viết mới nhất
     * @param int $limit Số lượng bài viết cần lấy
     * @return array Mảng các bài viết
     */
    public function getLatestPosts($limit = 5) {
        $query = "SELECT id, title, slug, excerpt, image, created_at 
                 FROM {$this->table} 
                 WHERE status = 'published' 
                 ORDER BY created_at DESC 
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Get latest posts error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết bài viết theo slug
     * @param string $slug Slug của bài viết
     * @return array|false Thông tin bài viết hoặc false nếu không tìm thấy
     */
    public function getPostBySlug($slug) {
        $query = "SELECT n.*, u.username as author_name, c.name as category_name 
                 FROM {$this->table} n
                 LEFT JOIN users u ON n.author_id = u.id
                 LEFT JOIN news_categories c ON n.category_id = c.id
                 WHERE n.slug = :slug AND n.status = 'published'
                 LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        
        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Get post by slug error: " . $e->getMessage());
            return false;
        }
    }
}
