<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Login-Register/css/auth.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <style>
        body { background: #181c24; color: #fff; font-family: Arial, sans-serif; }
        .profile-box { max-width: 400px; margin: 60px auto; background: #23272f; border-radius: 12px; padding: 32px 24px; box-shadow: 0 4px 24px rgba(0,0,0,0.2); }
        h2 { text-align: center; color: #1ea7fd; margin-bottom: 24px; }
        .profile-info { margin-bottom: 24px; }
        .profile-info p { margin: 10px 0; font-size: 1.1em; }
        .logout-btn { width: 100%; padding: 12px; background: linear-gradient(90deg,#1ea7fd 60%,#4f8cff 100%); color: #fff; border: none; border-radius: 8px; font-size: 1.1em; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .logout-btn:hover { background: #ff9800; }
    </style>
</head>
<body>
    <div class="profile-box">
        <h2>Thông tin tài khoản</h2>
        <div class="profile-info">
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($user->full_name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user->email) ?></p>
            <p><strong>Tên đăng nhập:</strong> <?= htmlspecialchars($user->username) ?></p>
        </div>
        <form method="post" action="/PJ1/public/home">
            <input type="hidden" name="logout" value="1">
            <button type="submit" class="logout-btn">Đăng xuất</button>
        </form>
    </div>
</body>
</html> 