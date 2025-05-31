<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

define('APPROOT', dirname(__DIR__));
define('URLROOT', '/PJ1/public');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/QueryBuilder.php';
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/models/GameModel.php';
require_once __DIR__ . '/../app/models/WalletModel.php';
require_once __DIR__ . '/../app/models/NewsModel.php';
require_once __DIR__ . '/../app/models/CommentModel.php';
require_once __DIR__ . '/../app/models/ProductModel.php';
require_once __DIR__ . '/../app/models/OrderModel.php';
require_once __DIR__ . '/../app/models/CartModel.php';
require_once __DIR__ . '/../app/models/CategoryModel.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/NewsController.php';
require_once __DIR__ . '/../app/controllers/EsportsController.php';
require_once __DIR__ . '/../app/controllers/StoreController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/ContactController.php';
require_once __DIR__ . '/../app/controllers/SearchController.php';
require_once __DIR__ . '/../app/controllers/HotgamesController.php';
require_once __DIR__ . '/../app/controllers/PrivacyPolicyController.php';
require_once __DIR__ . '/../app/controllers/TermsController.php';

// Xử lý URL
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = str_replace('/PJ1/public', '', $request_uri);
$request_uri = trim($request_uri, '/');
$uri = $request_uri ? explode('/', $request_uri) : [];


// Handle search route
if (isset($uri[0]) && $uri[0] === 'search') {
    $searchController = new \App\Controllers\SearchController();
    echo $searchController->search();
    exit;
}

try {
    if (empty($uri)) {
        // Redirect to home page if URI is empty
        $controller = new \App\Controllers\HomeController();
        echo $controller->index();
        exit;
    }
    
    // Xử lý route home cho mọi trường hợp
    if (isset($uri[0]) && strtolower($uri[0]) === 'home') {
        $controller = new \App\Controllers\HomeController();
        echo $controller->index();
        exit;
    }
    
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
    } elseif ($uri[0] === 'register') {
        $controller = new \App\Controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->registerForm();
        } else {
            echo $controller->register();
        }
    } elseif ($uri[0] === 'news') {
        // Nếu là truy vấn category động (game)
        if (isset($uri[1]) && $uri[1] === 'category' && isset($uri[2])) {
            // Sửa: Lấy danh sách game theo category từ bảng mobilegames
            require_once __DIR__ . '/../app/controllers/MobilegamesController.php';
            $controller = new \App\Controllers\MobileGamesController();
            $overview = $controller->getOverviewByCategory($uri[2]);
            require __DIR__ . '/../app/views/mobilegames/category.php';
            return;
        } else {
            $controller = new \App\Controllers\NewsController();
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (isset($uri[1]) && $uri[1] === 'details' && isset($uri[2])) {
                    // Hiển thị chi tiết tin tức
                    $controller->details($uri[2]);
                } else {
                    // Trang danh sách tin tức
                    $controller->newsPage();
                }
            } else {
                throw new Exception('Invalid endpoint');
            }
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
        } elseif ($uri[1] === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý thanh toán
            echo $controller->checkout();
        } else {
            throw new Exception('Invalid endpoint');
        }
    } elseif ($uri[0] === 'esports') {
        $controller = new \App\Controllers\EsportsController();
        if (isset($uri[1]) && $uri[1] === 'detail' && isset($uri[2])) {
            echo $controller->detail($uri[2]);
        } else {
            echo $controller->index();
        }
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
        require_once __DIR__ . '/../app/controllers/AboutusController.php';
        $controller = new \App\Controllers\AboutusController();
        echo $controller->index();
    } elseif ($uri[0] === 'gameranking') {
        require_once __DIR__ . '/../app/controllers/GamerankingController.php';
        $controller = new \App\Controllers\GamerankingController();
        echo $controller->index();
    } elseif ($uri[0] === 'events') {
        require_once __DIR__ . '/../app/controllers/EventsController.php';
        $controller = new \App\Controllers\EventsController();
        echo $controller->index();
    } elseif ($uri[0] === 'admin') {
        $controller = new \App\Controllers\AdminController();
        if (isset($uri[1])) {
            // Xử lý API endpoint
            if ($uri[1] === 'api' && isset($uri[2]) && $uri[2] === 'recent-orders') {
                $controller->apiRecentOrders();
                exit();
            }
            
            $method = $uri[1];
            $params = [];
            
            // Kiểm tra nếu có tham số ID (ví dụ: /admin/edit/1)
            if (isset($uri[2]) && is_numeric($uri[2])) {
                $params[] = $uri[2];
            }
            
            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], $params);
            } else {
                // Nếu method không tồn tại, chuyển hướng về trang admin mặc định
                header('Location: /PJ1/public/admin');
                exit();
            }
        } else {
            $controller->index();
        }
    } elseif ($uri[0] === 'hotgames') {
        $controller = new \App\Controllers\HotgamesController();
        if (isset($uri[1]) && $uri[1] === 'detail' && isset($uri[2])) {
            // Trang chi tiết game động
            $controller->detail((int)$uri[2]);
        } else {
            // Trang danh sách game hot
            echo $controller->index();
        }
    } elseif ($uri[0] === 'mobilegames') {
        require_once __DIR__ . '/../app/controllers/MobilegamesController.php';
        $controller = new \App\Controllers\MobilegamesController();
        echo $controller->index();
    } elseif ($uri[0] === 'esports') {
        $controller = new \App\Controllers\EsportsController();
        if (isset($uri[1]) && $uri[1] === 'detail' && isset($uri[2])) {
            $controller->detail($uri[2]);
            exit; // Thêm exit để đảm bảo không có mã nào chạy sau khi gọi controller
        } else {
            $controller->index();
            exit; // Thêm exit để đảm bảo không có mã nào chạy sau khi gọi controller
        }
    } elseif ($uri[0] === '403') {
        http_response_code(403);
        require __DIR__ . '/../app/views/error/403.php';
    } elseif ($uri[0] === '500') {
        http_response_code(500);
        require __DIR__ . '/../app/views/error/500.php';
    } elseif ($uri[0] === 'users' && ($uri[1] ?? null) === 'profile') {
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new \App\Controllers\AuthController();
        $controller->profile();
    } else {
        throw new Exception('Invalid endpoint');
    }
} catch (Exception $e) {
    http_response_code(404);
    $data = [
        'code' => 404,
        'title' => 'Không tìm thấy trang',
        'message' => $e->getMessage()
    ];
    require __DIR__ . '/../app/views/error/404.php';
}


?>