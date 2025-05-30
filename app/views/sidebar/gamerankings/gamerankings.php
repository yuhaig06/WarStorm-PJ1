<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Xếp Hạng Game</title>
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
                <img src="/PJ1/FrontEnd/Sidebar/img/lq.jpg" alt="Liên Quân Mobile" loading="lazy">
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
                <img src="/PJ1/FrontEnd/Sidebar/img/pm.jpg" alt="PUBG Mobile" loading="lazy">
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
                <img src="/PJ1/FrontEnd/Sidebar/img/ff.jpg" alt="Free Fire" loading="lazy">
                <div class="game-info">
                    <h2>Free Fire</h2>
                    <p>Game bắn súng sinh tồn với lối chơi nhanh, phù hợp với nhiều cấu hình máy.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 7M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.6/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="rpg">
                <span class="rank">#4</span>
                <img src="/PJ1/FrontEnd/Sidebar/img/gi.jpg" alt="Genshin Impact" loading="lazy">
                <div class="game-info">
                    <h2>Genshin Impact</h2>
                    <p>Tựa game nhập vai thế giới mở với đồ họa tuyệt đẹp và cốt truyện sâu sắc.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 6M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.9/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="fps">
                <span class="rank">#5</span>
                <img src="/PJ1/FrontEnd/Sidebar/img/cd.jpg" alt="Call of Duty" loading="lazy">
                <div class="game-info">
                    <h2>Call of Duty</h2>
                    <p>Phiên bản di động của series bắn súng huyền thoại, mang đến trải nghiệm FPS chất lượng cao.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 5M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.5/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="rpg">
                <span class="rank">#6</span>
                <img src="/PJ1/FrontEnd/Sidebar/img/pg.jpg" alt="Pokémon Go" loading="lazy">
                <div class="game-info">
                    <h2>Pokémon Go</h2>
                    <p>Trò chơi thực tế ảo tăng cường, cho phép người chơi bắt Pokémon trong thế giới thực.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 4M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.4/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="strategy">
                <span class="rank">#7</span>
                <img src="/PJ1/FrontEnd/Sidebar/img/ll.webp" alt="Đấu Trường Chân Lý Mobile" loading="lazy">
                <div class="game-info">
                    <h2>Đấu Trường Chân Lý Mobile</h2>
                    <p>Phiên bản di động của game cờ nhân phẩm nổi tiếng từ Liên Minh Huyền Thoại.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 3M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.3/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="moba">
                <span class="rank">#8</span>
                <img src="/PJ1/FrontEnd/Sidebar/img/ll1.jpg" alt="LMHT: Tốc Chiến" loading="lazy">
                <div class="game-info">
                    <h2>LMHT: Tốc Chiến</h2>
                    <p>Phiên bản mobile của Liên Minh Huyền Thoại, mang đến trải nghiệm MOBA đỉnh cao trên di động.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 3M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.2/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="rpg">
                <span class="rank">#9</span>
                <img src="/PJ1/FrontEnd/Sidebar/img/dr.jpg" alt="Dragon Raja" loading="lazy">
                <div class="game-info">
                    <h2>Dragon Raja</h2>
                    <p>Game nhập vai với đồ họa ấn tượng và thế giới mở rộng lớn.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 2M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.1/5</span>
                    </div>
                </div>
            </li>
            <li class="game-item card" data-category="strategy">
                <span class="rank">#10</span>
                <img src="/PJ1/FrontEnd/Sidebar/img/rk.jpg" alt="Rise of Kingdoms" loading="lazy">
                <div class="game-info">
                    <h2>Rise of Kingdoms</h2>
                    <p>Trò chơi chiến thuật xây dựng đế chế với lối chơi đa dạng và cộng đồng lớn mạnh.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-users"></i> 2M+ người chơi</span>
                        <span><i class="fas fa-star"></i> 4.0/5</span>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <script>
        // Xử lý menu mobile
        document.addEventListener("DOMContentLoaded", function() {
            const menuToggle = document.getElementById("menu-toggle");
            const mobileMenu = document.getElementById("mobile-menu");

            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener("click", function() {
                    mobileMenu.classList.toggle("active");
                });

                document.addEventListener("click", function(event) {
                    if (!menuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.remove("active");
                    }
                });
            }

            // Xử lý tìm kiếm
            const searchInput = document.getElementById("searchInput");
            const gameItems = document.querySelectorAll(".game-item");

            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    gameItems.forEach(item => {
                        const title = item.querySelector("h2").textContent.toLowerCase();
                        const description = item.querySelector("p").textContent.toLowerCase();
                        
                        if (title.includes(searchTerm) || description.includes(searchTerm)) {
                            item.style.display = "flex";
                        } else {
                            item.style.display = "none";
                        }
                    });
                });
            }

            // Xử lý lọc theo thể loại
            const filterButtons = document.querySelectorAll(".filter-btn");
            
            filterButtons.forEach(button => {
                button.addEventListener("click", function() {
                    // Xóa class active khỏi tất cả các nút
                    filterButtons.forEach(btn => btn.classList.remove("active"));
                    // Thêm class active cho nút được nhấn
                    this.classList.add("active");
                    
                    const category = this.getAttribute("data-category");
                    
                    gameItems.forEach(item => {
                        if (category === "all" || item.getAttribute("data-category") === category) {
                            item.style.display = "flex";
                        } else {
                            item.style.display = "none";
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>