<?php
namespace App\Controllers;

class GamerankingController {
    public function index() {
        require APPROOT . '/app/views/sidebar/gamerankings/gamerankings.php';
    }
}
