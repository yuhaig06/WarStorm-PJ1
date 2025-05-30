<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News MobileGames</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Mobilegame/css/mobilegame.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header id="header">
        <a href="/PJ1/public/home" class="logo-link">
            <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo WarStorm" class="logo">
        </a>
        <h1>📱 Tin Tức Game Mobile</h1>
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

    <div class="search-container">
        <input type="text" class="search-input" placeholder="Tìm kiếm tin tức...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <div class="filter-container">
        <button class="filter-btn active" data-filter="all">Tất cả</button>
        <button class="filter-btn" data-filter="lienquan">Liên Quân</button>
        <button class="filter-btn" data-filter="genshin">Genshin Impact</button>
        <button class="filter-btn" data-filter="pubg">PUBG Mobile</button>
    </div>

    <?php
    // Kết nối database
    $pdo = new PDO('mysql:host=localhost;dbname=warstorm_db;charset=utf8', 'root', '');
    
    // Lấy dữ liệu và thay thế đường dẫn
    $news = $pdo->query('SELECT * FROM mobilegames ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    
    // Cập nhật lại các đường dẫn
    foreach ($news as &$item) {
        // Kiểm tra nếu detail_url không null trước khi thay thế
        if (isset($item['detail_url']) && $item['detail_url'] !== null) {
            $item['detail_url'] = str_replace(
                ['/PJ1/BackEnd/public/', '/PJ1/BackEnd/'], 
                '/PJ1/public/', 
                $item['detail_url']
            );
        } else {
            // Nếu detail_url là null, tạo URL mặc định dựa trên ID
            $item['detail_url'] = '/PJ1/public/mobilegame/detail/' . $item['id'];
        }
    }
    unset($item);

    // Chia thành 2 phần: 4 tin đầu và phần còn lại
    $firstFourNews = array_slice($news, 0, 4);
    $remainingNews = array_slice($news, 4);
    ?>

    <section id="news-mobile-1">
        <?php foreach ($firstFourNews as $item): ?>
            <div class="mobile-item" data-category="<?= htmlspecialchars($item['category']) ?>">
                <img src="/PJ1/FrontEnd/Mobilegame<?= str_replace('../', '/', htmlspecialchars($item['image'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                <h2><?= htmlspecialchars($item['title']) ?></h2>
                <a href="<?= htmlspecialchars($item['detail_url']) ?>" class="see-more-btn">XEM THÊM</a>
        </div>
        <?php endforeach; ?>
    </section>

    <section id="news-mobile-2">
        <?php foreach ($remainingNews as $item): ?>
            <div class="mobile-item" data-category="<?= htmlspecialchars($item['category']) ?>">
                <img src="/PJ1/FrontEnd/Mobilegame<?= str_replace('../', '/', htmlspecialchars($item['image'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy">
                <h2><?= htmlspecialchars($item['title']) ?></h2>
                <a href="<?= htmlspecialchars($item['detail_url']) ?>" class="see-more-btn">XEM THÊM</a>
        </div>
        <?php endforeach; ?>
    </section>

    <div class="loading"></div>
    <div class="load-more-wrapper">
        <button class="btn-load-more" id="loadMoreBtn">TẢI THÊM BÀI VIẾT</button>
    </div>

    <button class="dark-mode-toggle" id="darkModeToggle">
        <i class="fas fa-moon"></i>
    </button>

    <footer class="container">
        <p>&copy; 2025 WarStorm. All rights reserved.</p>
        <a href="/PJ1/FrontEnd/Footer/privacy-policy.html">Chính sách bảo mật</a>
        <a href="/PJ1/FrontEnd/Footer/terms-and-conditions.html">Điều khoản sử dụng</a>
        <a href="/PJ1/FrontEnd/Footer/contact.html">Liên hệ</a>
    </footer>

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

    document.addEventListener("DOMContentLoaded", function() {
        const loadMoreBtn = document.getElementById("loadMoreBtn");
        const hiddenMobiles = document.querySelectorAll(".mobile-item-sub");
        const loading = document.querySelector(".loading");
        let currentIndex = 0;
        const mobilePerClick = 4;

        // Initially hide items in news-mobile-2
        document.querySelectorAll("#news-mobile-2 .mobile-item").forEach(item => {
            item.style.display = "none";
        });

        loadMoreBtn.addEventListener("click", function() {
            loading.style.display = "block";
            loadMoreBtn.style.display = "none";

            setTimeout(() => {
                // Show next set of items
                const items = document.querySelectorAll("#news-mobile-2 .mobile-item");
                for (let i = currentIndex; i < currentIndex + mobilePerClick; i++) {
                    if (items[i]) {
                        items[i].style.display = "block";
                        items[i].style.animation = "fadeIn 0.5s ease forwards";
                    }
                }

                currentIndex += mobilePerClick;
                loading.style.display = "none";

                // Show load more button only if there are more items to show
                if (currentIndex < items.length) {
                    loadMoreBtn.style.display = "block";
                }
            }, 1000);
        });

        // Search Functionality
        const searchInput = document.querySelector(".search-input");
        const mobileItems = document.querySelectorAll(".mobile-item");

        searchInput.addEventListener("input", function() {
            const searchTerm = this.value.toLowerCase();

            mobileItems.forEach(item => {
                const title = item.querySelector("h2").textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });

        // Filter Functionality
        const filterBtns = document.querySelectorAll(".filter-btn");
        
        filterBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                // Remove active class from all buttons
                filterBtns.forEach(btn => btn.classList.remove("active"));
                // Add active class to clicked button
                this.classList.add("active");

                const filter = this.getAttribute("data-filter");

                mobileItems.forEach(item => {
                    if (filter === "all" || item.getAttribute("data-category") === filter) {
                        item.style.display = "block";
                        item.style.animation = "fadeIn 0.5s ease forwards";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        });

        // Dark Mode Toggle
        const darkModeToggle = document.getElementById("darkModeToggle");
        const body = document.body;

        // Check for saved dark mode preference
        if (localStorage.getItem("darkMode") === "enabled") {
            body.classList.add("dark-mode");
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }

        darkModeToggle.addEventListener("click", function() {
            body.classList.toggle("dark-mode");
            
            if (body.classList.contains("dark-mode")) {
                localStorage.setItem("darkMode", "enabled");
                this.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                localStorage.setItem("darkMode", "disabled");
                this.innerHTML = '<i class="fas fa-moon"></i>';
            }
        });
    });
</script>

</body>
</html>