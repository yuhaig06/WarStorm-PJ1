<?php
// Post data is already available from the controller
if (!isset($post)) {
    header('Location: /PJ1/public/esports');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Esports/css/esport.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <style>
        /* Style cho phần hiển thị thời gian */
        .time-display {
            display: block;
            margin: 15px 0;
            padding: 10px 0;
            color: #666;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        
        /* Ẩn menu mobile trên desktop */
        #mobile-menu {
            display: none;
        }
        
        /* Hiển thị menu khi click vào nút menu-toggle */
        #mobile-menu.active {
            display: block;
        }
        
        /* Hiển thị nút menu chỉ trên mobile */
        #menu-toggle {
            display: none;
        }
        
        /* Responsive cho mobile */
        @media (max-width: 768px) {
            #menu-toggle {
                display: block;
                position: absolute;
                top: 20px;
                right: 20px;
                font-size: 24px;
                background: none;
                border: none;
                cursor: pointer;
                z-index: 1000;
            }
            
            #mobile-menu {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.9);
                z-index: 999;
                padding: 60px 20px 20px;
                box-sizing: border-box;
            }
            
            #mobile-menu ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            #mobile-menu li {
                margin: 20px 0;
                text-align: center;
            }
            
            #mobile-menu a {
                color: white;
                text-decoration: none;
                font-size: 18px;
                display: block;
                padding: 10px;
            }
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .article-header {
            margin-bottom: 30px;
        }
        .article-title {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }
        .article-content {
            line-height: 1.6;
            color: #333;
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px 0;
        }
        .back-button {
            display: inline-flex;
            align-items: center;
            margin: 30px 0;
            padding: 12px 24px;
            background-color: #2c3e50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .back-button:hover {
            background-color: #34495e;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .back-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        .back-button::before {
            content: '←';
            margin-right: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <section class="main-content">
        <div class="time-display" id="time"></div>
        <button id="menu-toggle">☰</button>
        <nav id="mobile-menu">
            <ul>
                <li><a href="/PJ1/public/home">Trang chủ</a></li>
                <li><a href="/PJ1/public/news">Tin tức</a></li>
                <li><a href="/PJ1/public/esports">ESPORTS</a></li>
                <li><a href="/PJ1/public/store">Store</a></li>
                <li><a href="/PJ1/public/contact">Liên hệ</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="article-header">
            <h1 class="article-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        </div>
        
        <div class="article-content">
            <?php 
            $content = $post['content'] ?? '';
            // Sửa đường dẫn ảnh
            $content = str_replace('src="../img/', 'src="/PJ1/FrontEnd/Esports/img/', $content);
            // Xóa tất cả các thẻ nav và nội dung bên trong
            $content = preg_replace('#<nav[^>]*>.*?<\/nav>#is', '', $content);
            echo $content; 
            ?>
        </div>
        
        <a href="/PJ1/public/esports" class="back-button">Quay lại danh sách</a>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuToggle = document.getElementById("menu-toggle");
        const mobileMenu = document.getElementById("mobile-menu");
        
        menuToggle.addEventListener("click", function () {
            mobileMenu.classList.toggle("active");
        });
        
        document.addEventListener("click", function (event) {
            if (!menuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.remove("active");
            }
        });
    });
    </script>
</body>
</html>
