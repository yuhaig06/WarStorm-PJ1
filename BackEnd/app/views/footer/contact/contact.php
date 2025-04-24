<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Liên hệ với WarStorm - Nền tảng tin tức game hàng đầu">
    <meta name="keywords" content="liên hệ, hỗ trợ, game, tin tức game, eSports">
    <meta name="author" content="WarStorm">
    <title>Liên Hệ</title>
    <link rel="stylesheet" href="../../FrontEnd/Footer/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="96x96" href="../../FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="../../FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="../../FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="../../FrontEnd/Home/favicon/site.webmanifest">
</head>
<body>
    <header id="header">
        <button id="menu-toggle">☰</button>
        <nav id="mobile-menu">
            <ul>
                <li><a href="/PJ1/BackEnd/public/home">Trang chủ</a></li>
                <li><a href="/PJ1/BackEnd/public/news">Tin tức</a></li>
                <li><a href="/PJ1/BackEnd/public/esports">ESPORTS</a></li>
                <li><a href="/PJ1/BackEnd/public/store">Store</a></li>
                <li><a href="/PJ1/BackEnd/app/views/footer/contact/contact.php">Liên hệ</a></li>
            </ul>
        </nav>
    </header>
    <div class="breadcrumb">
        <a href="/PJ1/BackEnd/public/home" class="logo-link">
            <img src="../../FrontEnd/Home/img/logo.png" alt="Logo WarStorm" class="logo">
        </a>
        <span>Liên hệ</span>
    </div>
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Liên Hệ Chúng Tôi</h1>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <h2><i class="fas fa-info-circle"></i> Thông Tin Liên Hệ</h2>
                    <p>Nếu bạn có câu hỏi hoặc cần hỗ trợ, hãy liên hệ qua thông tin bên dưới:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> <b>Email:</b> warstorm.vn@gmail.com</li>
                        <li><i class="fas fa-phone me-2"></i> <b>Điện thoại:</b> 0123 456 789</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> <b>Địa chỉ:</b> Số 11 Nguyễn Đình Chiểu P. Đa Kao Quận 1, Ho Chi Minh City, Vietnam</li>
                    </ul>
                    <h3><i class="fas fa-clock"></i> Thời gian phản hồi</h3>
                    <p>Chúng tôi sẽ phản hồi trong vòng 24-48 giờ làm việc.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h2><i class="fas fa-paper-plane"></i> Gửi Tin Nhắn</h2>
                    <form class="contact-form">
                        <div class="form-group">
                            <label for="name">Họ và tên:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Nội dung:</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Gửi tin nhắn</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <button class="dark-mode-toggle" id="darkModeToggle">
        <i class="fas fa-moon"></i>
    </button>
</body>
</html>