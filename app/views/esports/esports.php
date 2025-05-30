<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESPORTS</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Esports/css/esport.css">
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
        <h1>ESPORTS</h1>
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

    <section id="hot-esports-1">
        <div class="esport-item">
            <img src="/PJ1/FrontEnd/Esports/img/h1.webp" alt="esport 1">
            <h2>Đây có thể chính là cái tên góp phần khiến T1 bùng nổ drama như hiện tại</h2>
            <a href="/PJ1/public/esports/detail/1" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="esport-item">
            <img src="/PJ1/FrontEnd/Esports/img/g4.webp" alt="esport 2">
            <h2>Chính "người được chọn" biến lời nói của CEO T1 thành "trò cười"</h2>
            <a href="/PJ1/public/esports/detail/2" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="esport-item">
            <img src="/PJ1/FrontEnd/Esports/img/f4.webp" alt="esport 3">
            <h2>Hé lộ lịch tập "địa ngục", siêu sao BLG "gáy khét" sẽ ôm trọn danh hiệu LPL lẫn MSI</h2>
            <a href="/PJ1/public/esports/detail/3" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="esport-item">
            <img src="/PJ1/FrontEnd/Esports/img/e1.webp" alt="esport 4">
            <h2>Người trong nghề "vạch mặt" CEO T1, nghi vấn có "âm mưu lớn" đằng sau</h2>
            <a href="/PJ1/public/esports/detail/4" class="see-more-btn">Xem Thêm</a>
        </div>
    </section>

    <section id="hot-esports-2">
        <div class="esport-item-sub hidden">
            <img src="/PJ1/FrontEnd/Esports/img/d1.webp" alt="esport 5">
            <h2>CEO T1 tiếp tục lên tiếng về tương lai Gumayusi, có dấu hiệu đùn đẩy trách nhiệm</h2>
            <a href="/PJ1/public/esports/detail/5" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="esport-item-sub hidden">
            <img src="/PJ1/FrontEnd/Esports/img/c1.webp" alt="esport 6">
            <h2>Đại diện LPL rơi vào khủng hoảng thực sự, HLV cũng "bỏ rơi" tuyển thủ</h2>
            <a href="/PJ1/public/esports/detail/6" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="esport-item-sub hidden">
            <img src="/PJ1/FrontEnd/Esports/img/b1.webp" alt="esport 7">
            <h2>Zeus "tạt nước lạnh" vào kỳ vọng của fan với chia sẻ liên quan Faker</h2>
            <a href="/PJ1/public/esports/detail/7" class="see-more-btn">Xem Thêm</a>
        </div>
        <div class="esport-item-sub hidden">
            <img src="/PJ1/FrontEnd/Esports/img/a1.webp" alt="esport 8">
            <h2>Tuyển thủ CFO bất ngờ "tri ân" VCS sau chiến tích tại First Stand 2025</h2>
            <a href="/PJ1/public/esports/detail/8" class="see-more-btn">Xem Thêm</a>
        </div>
    </section>

    <div class="load-more-wrapper">
        <button class="btn-load-more" id="loadMoreBtn" aria-label="Tải thêm tin tức vào trang">Tải thêm bài viết</button>
    </div>

    <footer class="container bg-black text-center py-4">
        <p>&copy; 2025 WarStorm. All rights reserved.</p>
        <a href="#" class="text-danger">Chính sách bảo mật</a>
        <a href="#" class="text-danger">Điều khoản sử dụng</a>
        <a href="#" class="text-danger">Liên hệ</a>
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
    const hiddenEsports = document.querySelectorAll(".esport-item-sub.hidden");
    let currentIndex = 0;
    const esportsPerClick = 2;
    loadMoreBtn.addEventListener("click", function() {
        for (let i = currentIndex; i < currentIndex + esportsPerClick; i++) {
            if (hiddenEsports[i]) {
                hiddenEsports[i].classList.remove("hidden");
            }
        }
        currentIndex += esportsPerClick;
        if (currentIndex >= hiddenEsports.length) {
            loadMoreBtn.style.display = "none";
        }
    });
});
</script>
</body>
</html>