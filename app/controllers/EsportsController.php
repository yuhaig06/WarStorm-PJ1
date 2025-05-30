<?php
namespace App\Controllers;

class EsportsController {
    public function index() {
        require APPROOT . '/app/views/esports/esports.php';
    }

    public function detail($id) {
        // Kết nối database
        $db = new \PDO('mysql:host=localhost;dbname=warstorm_db;charset=utf8mb4', 'root', '', array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ));
        $stmt = $db->prepare("SELECT * FROM esports WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$post) {
            // Nếu không tìm thấy thì chuyển về trang danh sách
            header("Location: /PJ1/public/esports");
            exit;
        }

        // Pass the post data to the view
        $data = [
            'post' => $post,
            'title' => $post['title']
        ];
        
        // Load the view with data
        extract($data);
        require APPROOT . '/app/views/esports/detail.php';
    }
}