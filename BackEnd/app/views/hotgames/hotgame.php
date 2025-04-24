<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Hot Nhất Tuần Này</title>
    <link rel="stylesheet" href="../../../FrontEnd/Hotgames/css/hotgame.css">
    <link rel="icon" type="image/png" sizes="96x96" href="../../../FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="../../../FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="../../../FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../../FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="../../../FrontEnd/Home/favicon/site.webmanifest">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header id="header">
        <a href="/PJ1/BackEnd/public/home" class="logo-link">
            <img src="../../../FrontEnd/Home/img/logo.png" alt="Logo WarStorm" class="logo">
        </a>
        <h1>🔥 Game Hot Nhất Tuần</h1>
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
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Tìm kiếm game...">
        <i class="fas fa-search search-icon"></i>
    </div>
    <div class="filter-container">
        <button class="filter-btn active" data-filter="all">Tất cả</button>
        <button class="filter-btn" data-filter="action">Hành động</button>
        <button class="filter-btn" data-filter="strategy">Chiến thuật</button>
        <button class="filter-btn" data-filter="rpg">RPG</button>
        <button class="filter-btn" data-filter="sports">Thể thao</button>
    </div>
    <section id="hot-games-1">
        <div class="game-item" data-category="strategy">
            <img src="../../../FrontEnd/Hotgames/img/a2.png" alt="Game 1" loading="lazy">
            <h2>[Tam Quốc Chí - Chiến Lược] Ra mắt mùa giải mới "Trận Đồng Quan", SP Mã Siêu long trọng ra mắt</h2>
            <a href="#" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="game-item" data-category="rpg">
            <img src="../../../FrontEnd/Hotgames/img/b1.webp" alt="Game 2" loading="lazy">
            <h2>VNGGames công bố phát hành siêu phẩm Lineage2M tại Việt Nam</h2>
            <a href="#" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="game-item" data-category="rpg">
            <img src="../../../FrontEnd/Hotgames/img/c1.webp" alt="Game 3" loading="lazy">
            <h2>Tây Du Truyền Kỳ Mobile - Game đấu tướng Tây Du Phong Thần kết hợp Tu Tiên sắp ra mắt</h2>
            <a href="#" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="game-item" data-category="action">
            <img src="../../../FrontEnd/Hotgames/img/d1.webp" alt="Game 4" loading="lazy">
            <h2>Xuất hiện tựa game mới là đối thủ nặng ký của CS:GO và Valorant, lượng người chơi tăng vọt trên Steam</h2>
            <a href="#" class="see-more-btn">Xem Thêm</a>
        </div>
    </section>
    <section id="hot-games-2">
        <div class="game-item-sub hidden" data-category="rpg">
            <img src="../../../FrontEnd/Hotgames/img/e1.webp" alt="Game 5" loading="lazy">
            <h2>Yulgang: Tái Chiến Võ Lâm chính thức ra mắt hôm nay 13/3</h2>
            <a href="#" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="game-item-sub hidden" data-category="sports">
            <img src="../../../FrontEnd/Hotgames/img/f1.webp" alt="Game 6" loading="lazy">
            <h2>Siêu phẩm đua xe của NetEase công bố ngày ra mắt chính thức khiến game thủ hào hứng</h2>
            <a href="#" class="see-more-btn">Xem Thêm</a>
        </div>
    </section>
</body>
</html>