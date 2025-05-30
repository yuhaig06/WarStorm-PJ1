<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sự Kiện & Quảng Cáo</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/sidebar/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
</head>
<body>
    <header id="header">
        <a href="/PJ1/public/home" class="logo-link">
            <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo WarStorm" class="logo">
        </a>
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
                <img src="/PJ1/FrontEnd/Sidebar/img/sk1.jpg" alt="Sự kiện 1" loading="lazy">
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
                <img src="/PJ1/FrontEnd/Sidebar/img/sk2.jpg" alt="Sự kiện 2" loading="lazy">
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
                <img src="/PJ1/FrontEnd/Sidebar/img/bo.webp" alt="Sự kiện 3" loading="lazy">
                <div class="event-info">
                    <h2>Call of Duty: Black Ops 6</h2>
                    <p>Call of Duty: Black Ops 6 sẽ là tựa game đầu tiên góp mặt tại Esports World Cup 2025. Ra mắt năm 2024, trò chơi thu hút sự quan tâm nhờ lối chơi bắn súng tốc độ nhanh và hỗ trợ trên nhiều nền tảng.</p>
                    <div class="event-meta">
                        <span><i class="far fa-calendar"></i> 15/06/2025 - 30/06/2025</span>
                        <span><i class="fas fa-gamepad"></i> Esports</span>
                    </div>
                </div>
            </div>

            <div class="event-item card" data-category="mobile">
                <img src="/PJ1/FrontEnd/Sidebar/img/hk.webp" alt="Sự kiện 4" loading="lazy">
                <div class="event-info">
                    <h2>Honor of Kings</h2>
                    <p>Honor of Kings là tựa game MOBA di động thứ hai được xác nhận góp mặt tại Esports World Cup 2025. Được phát hành bởi Tencent Games vào năm 2015, trò chơi nhanh chóng trở nên phổ biến.</p>
                    <div class="event-meta">
                        <span><i class="far fa-calendar"></i> 01/07/2025 - 15/07/2025</span>
                        <span><i class="fas fa-gamepad"></i> Game Mobile</span>
                    </div>
                </div>
            </div>

            <div class="event-item card" data-category="console">
                <img src="/PJ1/FrontEnd/Sidebar/img/xc.jpg" alt="Sự kiện 5" loading="lazy">
                <div class="event-info">
                    <h2>Ra mắt "Xenoblade Chronicles X: Definitive Edition" trên Nintendo Switch</h2>
                    <p>Tựa game "Xenoblade Chronicles X: Definitive Edition" đã chính thức phát hành trên Nintendo Switch vào tháng 3 năm 2025. Đây là phiên bản nâng cấp của tựa game nhập vai thế giới mở nổi tiếng.</p>
                    <div class="event-meta">
                        <span><i class="far fa-calendar"></i> 01/03/2025</span>
                        <span><i class="fas fa-gamepad"></i> Game Console</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Xử lý menu mobile
        document.addEventListener("DOMContentLoaded", function() {
            const menuToggle = document.getElementById("menu-toggle");
            const mobileMenu = document.getElementById("mobile-menu");

            menuToggle.addEventListener("click", function() {
                mobileMenu.classList.toggle("active");
            });

            document.addEventListener("click", function(event) {
                if (!menuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.remove("active");
                }
            });

            // Xử lý tìm kiếm
            const searchInput = document.getElementById("searchInput");
            const eventItems = document.querySelectorAll(".event-item");

            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();
                
                eventItems.forEach(item => {
                    const title = item.querySelector("h2").textContent.toLowerCase();
                    const description = item.querySelector("p").textContent.toLowerCase();
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        item.style.display = "flex";
                    } else {
                        item.style.display = "none";
                    }
                });
            });

            // Xử lý lọc theo thể loại
            const filterButtons = document.querySelectorAll(".filter-btn");
            
            filterButtons.forEach(button => {
                button.addEventListener("click", function() {
                    // Xóa class active khỏi tất cả các nút
                    filterButtons.forEach(btn => btn.classList.remove("active"));
                    // Thêm class active cho nút được nhấn
                    this.classList.add("active");
                    
                    const category = this.getAttribute("data-category");
                    
                    eventItems.forEach(item => {
                        item.style.display = "none"; // Ẩn tất cả
                        
                        if (category === "all" || item.getAttribute("data-category") === category) {
                            // Hiển thị nếu là 'tất cả' hoặc đúng thể loại
                            item.style.display = "flex";
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>