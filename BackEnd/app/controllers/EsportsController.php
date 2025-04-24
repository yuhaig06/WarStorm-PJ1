<?php
namespace App\Controllers;

class EsportsController {
    public function index() {
        require APPROOT . '/app/views/esports/esports.php';
    }
}