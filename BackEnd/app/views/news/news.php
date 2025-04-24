<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Trang tin tức Gaming">
    <meta name="keywords" content="game, tin tức game, eSports">
    <meta name="author" content="WarStorm">
    <title>Tin Tức</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/News/css/news.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
</head>
<body>
    <header id="header">
        <a href="/PJ1/BackEnd/public/home" class="logo-link">
            <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo WarStorm" class="logo">
        </a>
        <h1>TIN TỨC</h1>
        <button id="menu-toggle">☰</button>
        <nav id="mobile-menu">
            <ul>
                <li><a href="/PJ1/BackEnd/public/home">Trang chủ</a></li>
                <li><a href="/PJ1/BackEnd/public/news">Tin tức</a></li>
                <li><a href="/PJ1/FrontEnd/Esports/esports/esport.html">ESPORTS</a></li>
                <li><a href="/PJ1/FrontEnd/Store/store/store.html">Store</a></li>
                <li><a href="/PJ1/FrontEnd/Footer/contact.html">Liên hệ</a></li>
            </ul>
        </nav>
    </header>
    <section id="news-container">
        <div id="news-item">
            <a href="/PJ1/FrontEnd/News/details/detailsmain.html">
                <img src="/PJ1/FrontEnd/News/img/main.webp" alt="Zeus và T1, chỉ LCK Cup 2025 là chưa đủ" loading="lazy">
                <h2>Zeus và T1, chỉ LCK Cup 2025 là chưa đủ</h2>
            </a>
        </div>
    </section>
    <div id="news-container-1">
        <div id="news-item-1">
            <a href="/PJ1/FrontEnd/News/details/details1.html">
                <img src="/PJ1/FrontEnd/News/img/1.webp" alt="Bom tấn sinh tồn quá chất lượng bất ngờ giảm giá mạnh" loading="lazy">
                <h2>Bom tấn sinh tồn quá chất lượng bất ngờ giảm giá mạnh</h2>
            </a>
        </div>
        <div id="news-item-2">
            <a href="/PJ1/FrontEnd/News/details/details2.html">
                <img src="/PJ1/FrontEnd/News/img/2.png" alt="Trải nghiệm làm John Wick với I Am Your Beast" loading="lazy">
                <h2>Trải nghiệm làm John Wick với I Am Your Beast</h2>
            </a>
        </div>
        <div id="news-item-3">
            <a href="/PJ1/FrontEnd/News/details/details3.html">
                <img src="/PJ1/FrontEnd/News/img/3.webp" alt="Động thái mới của Doran trên stream" loading="lazy">
                <h2>Động thái mới của Doran trên stream</h2>
            </a>
        </div>
        <div id="news-item-4">
            <a href="/PJ1/FrontEnd/News/details/details4.html">
                <img src="/PJ1/FrontEnd/News/img/4.webp" alt="ĐTCL mùa 13: 3 đội hình được Riot 'hồi sinh' mạnh mẽ" loading="lazy">
                <h2>ĐTCL mùa 13: 3 đội hình được Riot "hồi sinh" mạnh mẽ</h2>
            </a>
        </div>
    </div>
    <div id="news-container-2">
        <div class="news-item-sub hidden">
            <a href="/PJ1/FrontEnd/News/details/details5.html">
                <img src="/PJ1/FrontEnd/News/img/5.png" alt="Steam bất ngờ tặng miễn phí một tựa game" loading="lazy">
                <h2>Steam bất ngờ tặng miễn phí một tựa game</h2>
            </a>
        </div>
        <div class="news-item-sub hidden">
            <a href="/PJ1/FrontEnd/News/details/details6.html">
                <img src="/PJ1/FrontEnd/News/img/6.webp" alt="Game kinh dị 'made in Việt Nam' mới ra mắt trên Steam nhận ý kiến trái chiều" loading="lazy">
                <h2>Game kinh dị "made in Việt Nam" mới ra mắt trên Steam nhận ý kiến trái chiều</h2>
            </a>
        </div>
        <div class="news-item-sub hidden">
            <a href="/PJ1/FrontEnd/News/details/details7.html">
                <img src="/PJ1/FrontEnd/News/img/7.webp" alt="Xuất hiện streamer kỹ năng đạt 'đỉnh cao đời người'" loading="lazy">
                <h2>Xuất hiện streamer kỹ năng đạt "đỉnh cao đời người"</h2>
            </a>
        </div>
        <div class="news-item-sub hidden">
            <a href="/PJ1/FrontEnd/News/details/details8.html">
                <img src="/PJ1/FrontEnd/News/img/8.webp" alt="Từng có giá 1,8 triệu, bom tấn bất ngờ giảm giá mạnh" loading="lazy">
                <h2>Từng có giá 1,8 triệu, bom tấn bất ngờ giảm giá mạnh</h2>
            </a>
        </div>
    </div>
    <div class="load-more-wrapper">
        <button class="btn-load-more" id="loadMoreBtn" aria-label="Tải thêm tin tức vào trang">Tải thêm bài viết</button>
    </div>        
    <footer class="container bg-black text-center py-4">
        <p>&copy; 2025 WarStorm. All rights reserved.</p>
        <a href="/PJ1/BackEnd/public/privacy-policy" class="text-danger">Chính sách bảo mật</a>
        <a href="/PJ1/BackEnd/public/terms" class="text-danger">Điều khoản sử dụng</a>
        <a href="/PJ1/FrontEnd/Footer/contact.html" class="text-danger">Liên hệ</a>
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
        const articlesPerClick = 4;

        loadMoreBtn.addEventListener("click", function() {
            const hiddenNews = document.querySelectorAll(".news-item-sub.hidden");
            
            for (let i = 0; i < articlesPerClick; i++) {
                if (hiddenNews[i]) {
                    hiddenNews[i].classList.remove("hidden");
                }
            }

            if (document.querySelectorAll(".news-item-sub.hidden").length === 0) {
                loadMoreBtn.style.display = "none";
            }
        });
    });
</script>  
</body>
</html>