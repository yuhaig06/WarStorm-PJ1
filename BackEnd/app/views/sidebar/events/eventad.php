<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sự Kiện & Quảng Cáo</title>
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
        <h1><i class="fas fa-calendar-alt"></i> Sự Kiện & Quảng Cáo</h1>
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Tìm kiếm sự kiện..." id="searchInput">
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" data-category="all">Tất cả</button>
            <button class="filter-btn" data-category="mobile">Game Mobile</button>
            <button class="filter-btn" data-category="console">Game Console</button>
            <button class="filter-btn" data-category="esports">Esports</button>
        </div>
        <div class="event-list">
            <div class="event-item card" data-category="mobile">
                <img src="../../FrontEnd/Sidebar/img/sk1.jpg" alt="Sự kiện 1" loading="lazy">
                <div class="event-info">
                    <h2>LORDS MOBILE: Tưng bừng sự kiện "Đua top cao thủ - Tìm chủ nhân Iphone 15"</h2>
                    <p>Khai mở sự kiện "Đua top Nạp game Lords Mobile"</p>
                    <div class="event-meta">
                        <span><i class="far fa-calendar"></i> 01/04/2025 - 30/04/2025</span>
                        <span><i class="fas fa-gamepad"></i> Game Mobile</span>
                    </div>
                </div>
            </div>
            <div class="event-item card" data-category="console">
                <img src="../../FrontEnd/Sidebar/img/sk2.jpg" alt="Sự kiện 2" loading="lazy">
                <div class="event-info">
                    <h2>PlayStation Plus tháng 3/2025</h2>
                    <p>Sony đã công bố danh sách ba tựa game miễn phí dành cho các thuê bao PlayStation Plus trong tháng 3 năm 2025. Người chơi sẽ được trải nghiệm các tựa game đa dạng, từ phiêu lưu hành động đến platformer sôi động và bộ sưu tập game kinh điển.</p>
                    <div class="event-meta">
                        <span><i class="far fa-calendar"></i> 01/03/2025 - 31/03/2025</span>
                        <span><i class="fas fa-gamepad"></i> Game Console</span>
                    </div>
                </div>
            </div>
            <div class="event-item card" data-category="esports">
                <img src="../../FrontEnd/Sidebar/img/bo.webp" alt="Sự kiện 3" loading="lazy">
                <div class="event-info">
                    <h2>Call of Duty: Black Ops 6</h2>
                    <p>Call of Duty: Black Ops 6 sẽ là tựa game đầu tiên góp mặt tại Esports World Cup 2025. Ra mắt năm 2024, trò chơi thu hút sự quan tâm nhờ lối chơi bắn súng tốc độ nhanh và hỗ trợ trên nhiều nền tảng.</p>
                    <div class="event-meta">
                        <span><i class="far fa-calendar"></i> 15/06/2025 - 30/06/2025</span>
                        <span><i class="fas fa-gamepad"></i> Esports</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>