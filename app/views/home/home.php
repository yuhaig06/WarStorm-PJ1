<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    unset($_SESSION['user_id']);
    header('Location: /PJ1/public/home');
    exit;
}
// Đây là nơi lấy dữ liệu từ controller, chắc chắn rằng $posts được truyền vào view
// Đảm bảo $posts là một mảng các bài viết từ controller (đã được truyền trong controller)
$categories = [
    (object)['slug' => 'lienquan', 'name' => 'Liên Quân Mobile'],
    (object)['slug' => 'genshin', 'name' => 'Genshin Impact'],
    (object)['slug' => 'pubg', 'name' => 'PUBG Mobile'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Home/css/home.css">
    <link rel="stylesheet" href="/PJ1/FrontEnd/Home/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="/PJ1/FrontEnd/Home/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
</head>
<body>
    <header id="header">
        <div class="col-auto">
            <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo" class="logo">
        </div>
        <button class="menu-toggle" id="menu-toggle" aria-label="Mở menu"><span>☰</span></button>
        <button class="mobile-search-btn" id="mobile-search-btn" aria-label="Tìm kiếm">
            <i class="fas fa-search"></i>
        </button>
        <ul class="nav">
            <li><a href="/PJ1/public/home">Trang chủ</a></li>
            <li><a href="/PJ1/public/news">Tin tức</a></li>
            <li><a href="/PJ1/public/esports">ESPORTS</a></li>
            <li><a href="/PJ1/public/store">Cửa hàng</a></li>
            <li><a href="/PJ1/public/admin">Admin</a></li>
        </ul>
        <div class="col-auto">
            <div id="search-box" class="search-box">
                <form id="global-search-form" onsubmit="return false;">
                    <input type="text" id="search-input" name="q" placeholder="Tìm kiếm sản phẩm, bài viết..." autocomplete="off" />
                    <button type="button" id="search-button" onclick="searchAll()">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </form>
                <div id="search-results" class="search-results"></div>
            </div>
            <div class="auth-buttons">
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id']): ?>
                    <a href="/PJ1/public/users/profile" class="auth-btn">Tài khoản</a>
                <?php else: ?>
                    <a href="/PJ1/public/users/login" class="auth-btn">Đăng nhập</a>
                    <a href="/PJ1/public/users/register" class="auth-btn">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
        <!-- Menu mobile (ẩn/hiện bằng JS) -->
        <nav id="mobile-menu">
            <ul>
                <li><a href="/PJ1/public/home">Trang chủ</a></li>
                <li><a href="/PJ1/public/news">Tin tức</a></li>
                <li><a href="/PJ1/public/esports">ESPORTS</a></li>
                <li><a href="/PJ1/public/store">Cửa hàng</a></li>
                <li><a href="/PJ1/public/contact">Liên hệ</a></li>
            </ul>
        </nav>
    </header>
    <!-- JS MENU MOBILE: Toggle mở/đóng menu -->
    <script>
    (function() {
        var menuBtn = document.getElementById('menu-toggle');
        var mobileMenu = document.getElementById('mobile-menu');
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle('active');
                document.body.classList.toggle('menu-open', mobileMenu.classList.contains('active'));
            });
            // Đóng menu khi click ra ngoài
            document.addEventListener('click', function(e) {
                if (
                    mobileMenu.classList.contains('active') &&
                    !mobileMenu.contains(e.target) &&
                    e.target !== menuBtn &&
                    !menuBtn.contains(e.target)
                ) {
                    mobileMenu.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
            // Ngăn click bên trong menu lan ra ngoài
            mobileMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    })();
    </script>

    <section id="hero-section" class="hero-banner">
        <img src="/PJ1/FrontEnd/Home/img/a1.webp" alt="Banner" class="img-big-banner">
        <div class="hero-content">
            <h1 class="big-heading">Welcome to WarStorm - Fastest game news updates</h1>
            <div class="call-to-action">
                <a href="/PJ1/public/news">Khám Phá Tin Tức</a>
            </div>
        </div>
    </section>

    <main id="main-content" class="container">
        <div class="row">
            <!-- Featured News -->
            <section class="col-md-8 featured-news">
                <ul class="list-of-latest-posts">
                    <?php
                    $defaultPosts = [
                        1 => [
                            'id' => 1,
                            'title' => 'Bom tấn sinh tồn quá chất lượng bất ngờ giảm giá mạnh, thấp nhất trên Steam cho game thủ',
                            'image' => 'p1.webp',
                        ],
                        2 => [
                            'id' => 2,
                            'title' => 'Trải nghiệm làm John Wick với I Am Your Beast - siêu phẩm hành động vừa ra mắt trên iOS',
                            'image' => 'p2.webp',
                        ],
                        3 => [
                            'id' => 3,
                            'title' => 'Động thái mới của Doran trên stream đã tự chứng minh một lý do khiến Zeus rời T1',
                            'image' => 'z3.webp',
                        ],
                        4 => [
                            'id' => 4,
                            'title' => 'ĐTCL mùa 13: 3 đội hình được Riot "hồi sinh" mạnh mẽ, sắp làm trùm tại meta mới',
                            'image' => '34.webp',
                        ],
                    ];
                    foreach ($defaultPosts as $post) : ?>
                        <li>
                            <a href="/PJ1/public/news/details/<?php echo $post['id']; ?>">
                                <img src="/PJ1/FrontEnd/Home/img/<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" onerror="this.onerror=null;this.src='/PJ1/FrontEnd/News/img/no-image.png';">
                                <span><?php echo htmlspecialchars($post['title']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <!-- Show Title -->
            <div class="col-md-4">
                <div class="show-title">
                    <img src="/PJ1/FrontEnd/Home/img/h1.jpg" alt="Ảnh thumbnail bài viết">
                    <p class="short-description">Tựa game hot nhất tuần này với nhiều cập nhật mới.</p>
                    <button class="see-more-btn"><a href="/PJ1/public/hotgames">Xem Thêm</a></button>
                </div>

                <!-- Categories -->
                <div class="categories">
                    <h2>Danh mục Games - Tin tức</h2>
                    <div class="content">
                        <div class="game-list">
                            <?php if(isset($categories) && !empty($categories)): ?>
                                <?php foreach($categories as $category): ?>
                                    <a href="/PJ1/public/news/category/<?php echo $category->slug; ?>" class="game-card"><?php echo $category->name; ?></a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="news-list">
                            <a href="/PJ1/public/mobilegames" class="news-card">
                                <p>Cập nhật tin nóng về thế giới game!</p>
                            </a>
                        </div>
                    </div>
                </div>              
            </div>

            <!-- Video Section -->
            <div class="col-md-12">
                <div class="video-stream text-center">
                    <video id="myVideo" controls loop muted playsinline>
                        <source src="/PJ1/FrontEnd/Home/video/n1.mp4" type="video/mp4">
                    </video>
                    <audio id="myAudio" loop>
                        <source src="/PJ1/FrontEnd/Home/audio/t1.mp3" type="audio/mp3">
                    </audio>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="row mt-4">
            <div class="col-md-4 text-center">
                <a href="/PJ1/public/aboutus">
                    <h2 class="about-us">Về chúng tôi</h2>
                    <img src="/PJ1/FrontEnd/Home/img/m1.jpg" alt="Về Chúng tôi" class="sidebar-img">
                </a>
            </div>
        
            <div class="col-md-4 text-center">
                <a href="/PJ1/public/gameranking">
                    <h2 class="game-rankings">Bảng xếp hạng game</h2>
                    <img src="/PJ1/FrontEnd/Home/img/u1.png" alt="Bảng xếp hạng game" class="sidebar-img">
                </a>
            </div>
        
            <div class="col-md-4 text-center">
                <a href="/PJ1/public/events">
                    <h2 class="events-ad">Sự kiện quảng cáo</h2>
                    <img src="/PJ1/FrontEnd/Home/img/u2.png" alt="Sự kiện quảng cáo" class="sidebar-img">
                </a>
            </div>
        </div>        
    </main>                    

    <div id="footer" class="container">
        <div class="row">
            <div class="col-md-4">
                <h4 class="information">Thông tin về WarStorm</h4>
                <p><strong>Giới thiệu:</strong> WarStorm là một nền tảng tin tức và cộng đồng dành cho game thủ, cung cấp thông tin mới nhất về trò chơi, bảng xếp hạng, và sự kiện game.</p>
                <p><strong>Mục tiêu:</strong>Chúng tôi hướng tới việc trở thành trang web hàng đầu về tin tức game, giúp game thủ cập nhật thông tin nhanh chóng và chính xác.</p>
                <p class="contact"><a href="/PJ1/public/contact">Liên hệ</a></p>
            </div>

            <div class="col-md-4">
                <h4 class="social">Liên kết mạng xã hội:</h4>
                <a href="https://www.facebook.com/profile.php?id=61575353064430"><i class="ti-facebook"></i></a>
                <a href="https://www.youtube.com/@WarStorm"><i class="ti-youtube"></i></a>
                <a href="https://discord.gg/btHGnVbR"><i class="fa-brands fa-discord"></i></a>
            </div>

            <div class="col-md-4">
                <h4 class="policies-terms">
                    <a href="/PJ1/public/privacy-policy">Chính sách bảo mật</a>
                    <a href="/PJ1/public/terms">Điều khoản sử dụng</a>
                </h4>
            </div>
        </div>
    </div>
</body>
<!-- Thêm script tìm kiếm toàn cục -->
<script src="/PJ1/public/assets/js/global-search.js"></script>
<script>
    // Xử lý hiển thị/ẩn ô tìm kiếm trên mobile
    document.addEventListener('DOMContentLoaded', function() {
        const mobileSearchBtn = document.getElementById('mobile-search-btn');
        const searchBox = document.querySelector('.search-box');
        
        if (mobileSearchBtn && searchBox) {
            mobileSearchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchBox.classList.toggle('active');
                
                // Tự động focus vào ô tìm kiếm khi hiển thị
                if (searchBox.classList.contains('active')) {
                    const searchInput = document.getElementById('search-input');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
            });
            
            // Ẩn ô tìm kiếm khi click ra ngoài
            document.addEventListener('click', function(e) {
                if (!searchBox.contains(e.target) && e.target !== mobileSearchBtn && !mobileSearchBtn.contains(e.target)) {
                    searchBox.classList.remove('active');
                }
            });
        }
    });
</script>
<script>
    // Khởi tạo biến toàn cục
    let globalSearch;
    
    // Hàm searchAll được gọi từ onclick của nút tìm kiếm
    function searchAll() {
        if (!globalSearch) {
            globalSearch = new GlobalSearch();
        }
        const searchInput = document.getElementById('search-input');
        if (searchInput && searchInput.value.trim() !== '') {
            globalSearch.handleSearch(new Event('click'));
        }
    }
    
    // Khởi tạo tìm kiếm toàn cục khi DOM đã tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo đối tượng tìm kiếm
        globalSearch = new GlobalSearch();
        
        // Thêm sự kiện click cho nút tìm kiếm
        const searchButton = document.getElementById('search-button');
        if (searchButton) {
            searchButton.addEventListener('click', function(e) {
                e.preventDefault();
                globalSearch.handleSearch(e);
            });
        }
        
        // Thêm sự kiện nhấn Enter trong ô tìm kiếm
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    globalSearch.handleSearch(e);
                }
            });
            
            // Tự động tìm kiếm khi nhập (với debounce)
            let searchTimeout;
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    globalSearch.handleSearch(e);
                }, 500);
            });
        }
    });
</script>
</html>