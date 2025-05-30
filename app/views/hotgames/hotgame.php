<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Hot Nhất Tuần Này</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Hotgames/css/hotgame.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dark-mode">
    <header id="header">
        <a href="/PJ1/public/home" class="logo-link">
            <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo WarStorm" class="logo">
        </a>
        <div class="title-container">
        <h1>🔥 Game Hot Nhất Tuần</h1>
        </div>
    </header>

    <div class="search-section">
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Tìm kiếm game...">
            <button class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" data-category="all">Tất cả</button>
            <button class="filter-btn" data-category="strategy">Chiến thuật</button>
            <button class="filter-btn" data-category="rpg">Nhập vai</button>
            <button class="filter-btn" data-category="action">Hành động</button>
            <button class="filter-btn" data-category="sports">Thể thao</button>
    </div>
    </div>

    <?php
// Kết nối database
$pdo = new PDO('mysql:host=localhost;dbname=warstorm_db;charset=utf8', 'root', '');
$games = $pdo->query('SELECT *, 
    REPLACE(detail_url, "/PJ1/BackEnd/public/", "/PJ1/public/") as detail_url_fixed 
    FROM games ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);

// Cập nhật lại mảng với URL mới
foreach ($games as &$game) {
    $game['detail_url'] = $game['detail_url_fixed'];
    unset($game['detail_url_fixed']);
}
unset($game);

// Chia thành 2 phần: 4 game đầu và phần còn lại
$firstFourGames = array_slice($games, 0, 4);
$remainingGames = array_slice($games, 4);
?>

<section id="hot-games-1">
        <?php foreach ($firstFourGames as $game): ?>
            <div class="game-item" data-category="<?= htmlspecialchars(strtolower($game['category'])) ?>">
                <div class="game-image">
                    <img src="<?= htmlspecialchars($game['image']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" loading="lazy">
                </div>
                <div class="game-content">
                    <h2><?= htmlspecialchars($game['title']) ?></h2>
                    <a href="<?= htmlspecialchars($game['detail_url']) ?>" class="see-more-btn">XEM THÊM</a>
                </div>
        </div>
<?php endforeach; ?>
</section>

<section id="hot-games-2">
        <?php foreach ($remainingGames as $game): ?>
            <div class="game-item-sub hidden" data-category="<?= htmlspecialchars(strtolower($game['category'])) ?>">
                <div class="game-image">
                    <img src="<?= htmlspecialchars($game['image']) ?>" alt="<?= htmlspecialchars($game['title']) ?>" loading="lazy">
                </div>
                <div class="game-content">
                    <h2><?= htmlspecialchars($game['title']) ?></h2>
                    <a href="<?= htmlspecialchars($game['detail_url']) ?>" class="see-more-btn">XEM THÊM</a>
                </div>
        </div>
<?php endforeach; ?>
</section>

    <div class="load-more-wrapper">
        <button class="btn-load-more" id="loadMoreBtn">TẢI THÊM BÀI VIẾT</button>
    </div>

    <button class="dark-mode-toggle" id="darkModeToggle">
        <i class="fas fa-moon"></i>
    </button>

    <footer>
        <div class="footer-content">
            <div class="copyright">© 2025 WarStorm. All rights reserved.</div>
            <div class="footer-links">
                <a href="/PJ1/FrontEnd/Footer/privacy-policy.html">Chính sách bảo mật</a>
                <a href="/PJ1/FrontEnd/Footer/terms-and-conditions.html">Điều khoản sử dụng</a>
                <a href="/PJ1/FrontEnd/Footer/contact.html">Liên hệ</a>
            </div>
        </div>
    </footer>

    <style>
        body.dark-mode {
            background: #222 !important;
            color: #eee !important;
        }
        body.dark-mode .container, body.dark-mode footer.container {
            background: #222 !important;
            color: #eee !important;
        }
        body.dark-mode a { color: #80bfff !important; }
        body.dark-mode .game-item, body.dark-mode .game-item-sub {
            background: #333 !important;
            border-color: #444 !important;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Load More Button
            const loadMoreBtn = document.getElementById("loadMoreBtn");
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener("click", function () {
                    document.querySelectorAll(".game-item-sub.hidden").forEach(function(item) {
                        item.classList.remove("hidden");
                    });
                    this.style.display = "none";
                });
            }

            // Dark Mode Toggle
            const darkModeToggle = document.getElementById("darkModeToggle");
            if (darkModeToggle) {
                darkModeToggle.addEventListener("click", function() {
                    document.body.classList.toggle("dark-mode");
                    const icon = this.querySelector("i");
                    if (icon.classList.contains("fa-moon")) {
                        icon.classList.replace("fa-moon", "fa-sun");
                    } else {
                        icon.classList.replace("fa-sun", "fa-moon");
                    }
                });
            }

            // Search Functionality
            const searchInput = document.querySelector(".search-input");
            const gameItems = document.querySelectorAll(".game-item, .game-item-sub");
            
            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();
                gameItems.forEach(item => {
                    const title = item.querySelector("h2").textContent.toLowerCase();
                    if (title.includes(searchTerm)) {
                        item.style.display = "block";
                    } else {
                        item.style.display = "none";
                    }
                });
            });

            // Filter Buttons
            const filterButtons = document.querySelectorAll(".filter-btn");
            filterButtons.forEach(button => {
                button.addEventListener("click", function() {
                    filterButtons.forEach(btn => btn.classList.remove("active"));
                    this.classList.add("active");
                    const category = this.dataset.category;
                    
                    gameItems.forEach(item => {
                        if (category === "all" || item.dataset.category === category) {
                            item.style.display = "block";
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