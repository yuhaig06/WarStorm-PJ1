<?php
define('APPROOT', dirname(__DIR__));
define('URLROOT', '/PJ1/BackEnd/public');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// header('Content-Type: application/json'); // Đã bỏ để view HTML hiển thị đúng

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../vendor/autoload.php';

// Load biến môi trường từ file .env
if (class_exists('Dotenv\\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

// Get request path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_ireplace('/pj1/backend/public/', '', $uri); // Không phân biệt hoa/thường
$uri = explode('/', $uri);

try {
    if ($uri[0] === 'auth') {
        $controller = new \App\Controllers\AuthController();
        
        switch ($uri[1]) {
            case 'register':
                echo $controller->register();
                break;
            case 'login':
                echo $controller->login();
                break;
            default:
                throw new Exception('Route not found');
        }
    } elseif ($uri[0] === 'users' && ($uri[1] ?? null) === 'login') {
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->showLoginForm();
        } else {
            echo $controller->login();
        }
    } elseif ($uri[0] === 'users' && ($uri[1] ?? null) === 'register') {
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->registerForm();
        } else {
            echo $controller->register();
        }
    } elseif ($uri[0] === 'home') {
        $controller = new \App\Controllers\HomeController();
        echo $controller->index();
    } elseif ($uri[0] === 'index' && ($uri[1] ?? null) === 'home') {
        $controller = new \App\Controllers\HomeController();
        echo $controller->index();
    } elseif ($uri[0] === 'news') {
        $controller = new \App\Controllers\NewsController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->newsPage();
        } else {
            throw new Exception('Invalid endpoint');
        }
    } elseif ($uri[0] === 'store') {
        $controller = new \App\Controllers\StoreController();
        if (!isset($uri[1]) || $uri[1] === '' || $uri[1] === 'index') {
            echo $controller->index();
        } elseif ($uri[1] === 'add') {
            echo $controller->add();
        } elseif ($uri[1] === 'manage') {
            echo $controller->manage();
        } elseif ($uri[1] === 'category' && !isset($uri[2])) {
            echo $controller->categoryList();
        } elseif ($uri[1] === 'category' && isset($uri[2])) {
            echo $controller->category($uri[2]);
        } elseif ($uri[1] === 'edit' && isset($uri[2])) {
            echo $controller->edit($uri[2]);
        } elseif ($uri[1] === 'search') {
            echo $controller->search();
        } else {
            throw new Exception('Invalid endpoint');
        }
    } elseif ($uri[0] === 'esports') {
        $controller = new \App\Controllers\EsportsController();
        echo $controller->index();
    } elseif ($uri[0] === 'contact') {
        $controller = new \App\Controllers\ContactController();
        echo $controller->index();
    } elseif ($uri[0] === 'privacy-policy') {
        $controller = new \App\Controllers\PrivacyPolicyController();
        echo $controller->index();
    } elseif ($uri[0] === 'terms') {
        $controller = new \App\Controllers\TermsController();
        echo $controller->index();
    } elseif ($uri[0] === 'aboutus') {
        $controller = new \App\Controllers\AboutusController();
        echo $controller->index();
    } elseif ($uri[0] === 'eventad') {
        $controller = new \App\Controllers\EventadController();
        echo $controller->index();
    } elseif ($uri[0] === 'gameranking') {
        $controller = new \App\Controllers\GamerankingController();
        echo $controller->index();
    } elseif ($uri[0] === 'gamerankings') {
        $controller = new \App\Controllers\GamerankingController();
        echo $controller->index();
    } elseif ($uri[0] === 'events') {
        $controller = new \App\Controllers\EventsController();
        echo $controller->index();
    } elseif ($uri[0] === 'hotgames') {
        $controller = new \App\Controllers\HotgamesController();
        echo $controller->index();
    } elseif ($uri[0] === 'mobilegames') {
        $controller = new \App\Controllers\MobilegamesController();
        echo $controller->index();
    } else {
        throw new Exception('Invalid endpoint');
    }
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}