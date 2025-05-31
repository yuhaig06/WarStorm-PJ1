<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\ProductModel;
use PDO;

class SearchController {
    private $db;
    private $productModel;
    private $postModel;

    public function __construct() {
        try {
            // Tạo kết nối database
            $database = Database::getInstance();
            $this->db = $database->getConnection();
            
            // Khởi tạo ProductModel
            $this->productModel = new ProductModel();
            
            // Kiểm tra xem PostModel có tồn tại không trước khi khởi tạo
            $postModelClass = 'App\Models\PostModel';
            if (class_exists($postModelClass)) {
                $this->postModel = new $postModelClass($this->db);
                error_log("PostModel loaded successfully");
            } else {
                error_log("PostModel class not found, search functionality for posts will be disabled");
            }
            
            // Log successful initialization
            error_log("SearchController initialized successfully");
        } catch (\Exception $e) {
            error_log("SearchController Error: " . $e->getMessage());
            throw new \Exception("Không thể khởi tạo SearchController: " . $e->getMessage());
        }
    }

    public function search() {
        // Set JSON header
        header('Content-Type: application/json');
        
        try {
            // Get and sanitize the search keyword
            $keyword = trim($_GET['q'] ?? '');
            
            // Validate input
            if (empty($keyword)) {
                return json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập từ khóa tìm kiếm',
                    'products' => [],
                    'posts' => []
                ]);
            }
            
            // Initialize results array
            $results = [
                'success' => true,
                'keyword' => $keyword,
                'products' => [],
                'posts' => []
            ];
            
            // Search products
            if ($this->productModel) {
                $results['products'] = $this->productModel->searchProducts($keyword);
            }
            
            // Search posts if PostModel is available
            if ($this->postModel) {
                $results['posts'] = $this->postModel->searchPosts($keyword);
            }
            
            // Trả về kết quả
            echo json_encode($results);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm',
                'error' => $e->getMessage()
            ]);
        }
    }
}
