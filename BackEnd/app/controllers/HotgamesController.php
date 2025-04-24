<?php
namespace App\Controllers;

class HotgamesController {
    public function index() {
        require APPROOT . '/app/views/hotgames/hotgame.php';
    }
}
