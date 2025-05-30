<?php
namespace App\Controllers;

use App\Core\Controller;

class AboutusController extends Controller {
    public function index() {
        require APPROOT . '/app/views/sidebar/aboutus/about.php';
    }
}
