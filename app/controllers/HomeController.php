<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // Lấy danh sách tin tức mới nhất
        $newsModel = new \App\Models\NewsModel();
        // Lấy 4 bài viết mới nhất, không filter status/category để hiển thị demo
        $posts = $newsModel->getAll(1, 4, []);

        // Lấy danh mục game từ database
        $categoryModel = new \App\Models\CategoryModel();
        $categories = $categoryModel->getAllCategories();

        // Trả về giao diện trang chủ
        header('Content-Type: text/html; charset=utf-8');
        include __DIR__ . '/../views/home/home.php';
    }
}
?>