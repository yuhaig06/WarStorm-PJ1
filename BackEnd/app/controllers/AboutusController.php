<?php
namespace App\Controllers;

class AboutusController {
    public function index() {
        require APPROOT . '/app/views/sidebar/aboutus/about.php';
    }
}
