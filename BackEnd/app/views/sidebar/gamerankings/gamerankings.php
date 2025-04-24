<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Xếp Hạng Game</title>
    <link rel="stylesheet" href="../../FrontEnd/sidebar/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="96x96" href="../../FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="../../FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="../../FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="../../FrontEnd/Home/favicon/site.webmanifest">
</head>
<body>
    <header id="header">
        <a href="/PJ1/BackEnd/public/home" class="logo-link">
            <img src="../../FrontEnd/Home/img/logo.png" alt="Logo WarStorm" class="logo">
        </a>
        <button id="menu-toggle">☰</button>
        <nav id="mobile-menu">
            <ul>
                <li><a href="/PJ1/BackEnd/public/home">Trang chủ</a></li>
                <li><a href="/PJ1/BackEnd/public/news">Tin tức</a></li>
                <li><a href="/PJ1/BackEnd/public/esports">ESPORTS</a></li>
                <li><a href="/PJ1/BackEnd/public/store">Store</a></li>
                <li><a href="/PJ1/BackEnd/public/contact">Liên hệ</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1><i class="fas fa-trophy"></i> Bảng Xếp Hạng Game</h1>
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Tìm kiếm game..." id="searchInput">
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" data-category="all">Tất cả</button>
            <button class="filter-btn" data-category="moba">MOBA</button>
            <button class="filter-btn" data-category="fps">FPS</button>
            <button class="filter-btn" data-category="rpg">RPG</button>
            <button class="filter-btn" data-category="strategy">Chiến thuật</button>
        </div>
        <ul class="game-list">
            <li class="game-item card" data-category="moba">
                <span class="rank">#1</span>
                <img src="../../FrontEnd/Sidebar/img/lq.jpg" alt="Liên Quân Mobile" loading="lazy">
                <div class="game-info">
                    <h2>Liên Quân Mobile</h2>
                    <p>Tựa game MOBA hàng đầu với lối chơi chiến thuật và cộng đồng đông đảo.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 10M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.8/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="fps">
                <span class="rank">#2</span>
                <img src="../../FrontEnd/Sidebar/img/pm.jpg" alt="PUBG Mobile" loading="lazy">
                <div class="game-info">
                    <h2>PUBG Mobile</h2>
                    <p>Trò chơi bắn súng sinh tồn nổi tiếng với đồ họa chân thực và gameplay hấp dẫn.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 8M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.7/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="fps">
                <span class="rank">#3</span>
                <img src="../../FrontEnd/Sidebar/img/ff.jpg" alt="Free Fire" loading="lazy">
                <div class="game-info">
                    <h2>Free Fire</h2>
                    <p>Game bắn súng sinh tồn với lối chơi nhanh, phù hợp với nhiều cấu hình máy.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 7M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.6/5</span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</body>
</html>