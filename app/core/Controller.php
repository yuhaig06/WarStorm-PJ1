<?php

namespace App\Core;

class Controller {
    protected $db;
    protected $model;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function model($model) {
        $modelClass = "App\\Models\\" . $model;
        return new $modelClass();
    }

    protected function view($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = '../app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die('View does not exist: ' . $view);
        }
        
        // Get the contents of the buffer and clean it
        $content = ob_get_clean();
        
        // Output the content
        echo $content;
    }

    // Trả về JSON response chuẩn cho API
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Hàm validate sử dụng Validator
    protected function validate($rules) {
        $input = $_POST;
        if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {
            parse_str(file_get_contents('php://input'), $input);
        } elseif (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
        }
        $validator = new \App\Core\Validator($input);
        return $validator->validate($rules);
    }
}