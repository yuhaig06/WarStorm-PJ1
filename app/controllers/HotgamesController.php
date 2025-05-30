<?php
namespace App\Controllers;

class HotgamesController {
    public function index() {
        require APPROOT . '/app/views/hotgames/hotgame.php';
    }
    public function detail($id) {
        // Biến $id sẽ được dùng trong view detail.php
        require APPROOT . '/app/views/hotgames/detail.php';
    }
}
