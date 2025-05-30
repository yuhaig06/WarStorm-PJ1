<?php
use App\Core\App;

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Require thủ công App class nếu không dùng composer
require_once BASE_PATH . '/app/Core/App.php';

// Check composer autoloader
$autoloadFile = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadFile)) {
    require_once $autoloadFile;
}
require_once BASE_PATH . '/app/config/config.php';

// Simple API routing
$uri = $_SERVER['REQUEST_URI'];
$base = str_replace('\\', '/', dirname(dirname(__FILE__)));
$scriptName = str_replace($base, '', $_SERVER['SCRIPT_FILENAME']);
$apiPath = str_replace($scriptName, '', $uri);
$apiPath = strtok($apiPath, '?'); // remove query string

switch ($apiPath) {
    case '/api/products':
        include_once BASE_PATH . '/app/controllers/StoreController.php';
        $controllerClass = 'StoreController';
        $controller = new $controllerClass();
        $controller->index();
        exit;
    case '/api/users':
        include_once BASE_PATH . '/app/controllers/UserController.php';
        $controllerClass = 'UserController';
        $controller = new $controllerClass();
        $controller->index();
        exit;
    case '/api/games':
        include_once BASE_PATH . '/app/controllers/MobilegamesController.php';
        $controllerClass = 'MobilegamesController';
        $controller = new $controllerClass();
        $controller->index();
        exit;
    case '/api/news':
        include_once BASE_PATH . '/app/controllers/NewsController.php';
        $controllerClass = 'NewsController';
        $controller = new $controllerClass();
        $controller->index();
        exit;
    // Add more routes as needed
}

try {
    $app = new App();
    $app->run(); // Thêm method run() để xử lý request
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}