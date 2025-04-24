<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} else {
    die('.env file not found!');
}

// Hàm lấy biến môi trường an toàn
function env_var($key, $default = null) {
    $value = getenv($key);
    if ($value !== false && $value !== null && $value !== '') return $value;
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
    return $default;
}

$client_id = env_var('GOOGLE_CLIENT_ID', '');
$client_secret = env_var('GOOGLE_CLIENT_SECRET', '');
$redirect_uri = 'http://localhost/PJ1/BackEnd/login-google.php';

// Debug kiểm tra
if ($client_id === '' || $client_secret === '') {
    echo "Không lấy được GOOGLE_CLIENT_ID hoặc GOOGLE_CLIENT_SECRET từ .env<br>";
    echo "Debug:<br>";
    echo 'getenv: '; var_dump(getenv('GOOGLE_CLIENT_ID'));
    echo '$_ENV: '; var_dump($_ENV['GOOGLE_CLIENT_ID'] ?? null);
    echo '$_SERVER: '; var_dump($_SERVER['GOOGLE_CLIENT_ID'] ?? null);
    exit;
}

// Nếu không có code từ Google, tạo URL đăng nhập
if (!isset($_GET['code'])) {
    $auth_url = "https://accounts.google.com/o/oauth2/v2/auth?";
    $auth_url .= "client_id=" . $client_id;
    $auth_url .= "&redirect_uri=" . urlencode($redirect_uri);
    $auth_url .= "&response_type=code";
    $auth_url .= "&scope=email profile";
    header("Location: " . $auth_url);
    exit();
}

// Xử lý code từ Google
if (isset($_GET['code'])) {
    // Exchange code for access token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'code' => $_GET['code'],
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        // Get user info with access token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token_data['access_token']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo = curl_exec($ch);
        curl_close($ch);

        $user = json_decode($userinfo, true);

        if ($user) {
            // Redirect về frontend với thông tin user (truyền trực tiếp email, name, picture)
            $redirectUrl = '/PJ1/FrontEnd/Home/home/home.html?'
                . 'email=' . urlencode($user['email'])
                . '&name=' . urlencode($user['name'])
                . '&picture=' . urlencode($user['picture']);
            header('Location: ' . $redirectUrl);
            exit();
        }
    }
}

// Nếu có lỗi, quay về trang đăng nhập
header('Location: /PJ1/FrontEnd/Login-Register/Login/login.html?error=google_login_failed');
exit();
?>
