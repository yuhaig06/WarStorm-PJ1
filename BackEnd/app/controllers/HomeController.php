<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // Trả về giao diện trang chủ
        header('Content-Type: text/html; charset=utf-8');
        include __DIR__ . '/../views/home/home.php';
    }
}
?>