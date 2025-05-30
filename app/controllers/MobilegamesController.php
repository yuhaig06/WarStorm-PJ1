<?php
namespace App\Controllers;

use PDO;

class MobileGamesController {
    private $db;

    public function __construct() {
        // Kết nối database
        $this->db = new PDO('mysql:host=localhost;dbname=warstorm_db;charset=utf8mb4', 'root', '', array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ));
    }

    public function index() {
        require APPROOT . '/app/views/mobilegames/mobilegame.php';
    }

    public function getByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM mobilegames WHERE category = ?");
        $stmt->execute([$category]);
        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $games;
    }

    // Lấy bản ghi tổng quan duy nhất cho category (title đúng với tên game)
    public function getOverviewByCategory($category) {
        $titleMap = [
            'lienquan' => 'Liên Quân Mobile',
            'genshin' => 'Genshin Impact',
            'pubg' => 'PUBG Mobile'
        ];
        $title = isset($titleMap[$category]) ? $titleMap[$category] : '';
        if ($title) {
            $stmt = $this->db->prepare("SELECT * FROM mobilegames WHERE category = ? AND title = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$category, $title]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function detail($id) {
        // Lấy thông tin bài viết từ database
        $stmt = $this->db->prepare("SELECT * FROM mobilegames WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nếu không tìm thấy bài viết, trả về false
        if (!$news) {
            return false;
        }

        // Thêm thời gian tạo nếu chưa có
        if (!isset($news['created_at'])) {
            $news['created_at'] = date('Y-m-d H:i:s');
        }

        return $news;
    }
}
