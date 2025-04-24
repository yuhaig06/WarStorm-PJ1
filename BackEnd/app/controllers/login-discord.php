<?php
session_start();

$client_id = '1361611004852699206';
$client_secret = '_lB1xle440gwhldUVI8s5fBse9xh79hQ';
$redirect_uri = 'http://localhost/PJ1/BackEnd/login-discord.php';  // URL phải trùng với URL trong Discord Developer Portal

// Nếu không có code từ Discord, tạo URL đăng nhập
if (!isset($_GET['code'])) {
    $auth_url = "https://discord.com/api/oauth2/authorize?";
    $auth_url .= "client_id=" . $client_id;
    $auth_url .= "&redirect_uri=" . urlencode($redirect_uri);
    $auth_url .= "&response_type=code";
    $auth_url .= "&scope=identify email";
    $auth_url .= "&prompt=consent";  // Thêm prompt=consent để luôn hiện hộp thoại xác nhận
    header("Location: " . $auth_url);
    exit();
}

// Xử lý code từ Discord
if (isset($_GET['code'])) {
    // Exchange code for access token
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://discord.com/api/oauth2/token',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'authorization_code',
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        // Get user info with access token
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://discord.com/api/users/@me',
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token_data['access_token']],
            CURLOPT_RETURNTRANSFER => true
        ]);
        
        $userinfo = curl_exec($ch);
        curl_close($ch);

        $user = json_decode($userinfo, true);

        if ($user) {
            // Add user data to URL parameters
            $redirect_url = '/PJ1/FrontEnd/Home/home/home.html?';
            $redirect_url .= 'discordUser=' . urlencode(json_encode([
                'email' => $user['email'],
                'name' => $user['username'],
                'picture' => 'https://cdn.discordapp.com/avatars/' . $user['id'] . '/' . $user['avatar'] . '.png',
                'id' => $user['id']
            ]));
            
            header('Location: ' . $redirect_url);
            exit();
        }
    }
}

// Nếu có lỗi, quay về trang đăng nhập
header('Location: /PJ1/FrontEnd/Login-Register/Login/login.html?error=discord_login_failed');
exit();
?>