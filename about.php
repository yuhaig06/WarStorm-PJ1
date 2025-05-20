<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu - Warstorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .about-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .about-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .about-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .about-title {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        .about-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #3498db;
        }
        .feature-item {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .feature-item:hover {
            transform: translateY(-5px);
        }
        .feature-item i {
            font-size: 2.5em;
            color: #3498db;
            margin-bottom: 15px;
        }
        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 5px solid #f8f9fa;
        }
        .team-member h5 {
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .team-member p {
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        .social-links a {
            display: inline-block;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            background: #3498db;
            color: white;
            border-radius: 50%;
            margin: 0 5px;
            transition: all 0.3s;
        }
        .social-links a:hover {
            background: #2980b9;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-laptop"></i> Warstorm
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-shopping-cart"></i> Giỏ hàng
                    </a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="btn btn-outline-success me-2">
                            <i class="fas fa-user"></i> Tài khoản
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                        <a href="register.php" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container about-container">
        <div class="about-card">
            <h1 class="about-title">Về chúng tôi</h1>
            
            <p class="lead">
                <h2>
                Warstorm được thành lập vào năm 2020 với sứ mệnh cung cấp các sản phẩm công nghệ chất lượng cao 
                và dịch vụ chuyên nghiệp cho khách hàng.
                    </h2>
            </p>
            <p>
                Giới thiệu về sự uy tín của trang web bán thiết bị công nghệ Warstorm
Trong thời đại công nghệ phát triển mạnh mẽ, việc lựa chọn một địa chỉ mua sắm trực tuyến đáng tin cậy là điều vô cùng quan trọng. Warstorm.vn tự hào là một trong những trang web hàng đầu tại Việt Nam chuyên cung cấp các thiết bị công nghệ chính hãng, chất lượng cao và giá cả cạnh tranh.
</p>
<p>
    1. Sản phẩm chính hãng – Đảm bảo chất lượng
Tất cả sản phẩm tại Warstorm đều được nhập khẩu hoặc phân phối chính hãng từ các thương hiệu nổi tiếng như Apple, Samsung, MSI, Logitech, Razer, Lenovo… Chúng tôi cam kết không kinh doanh hàng giả, hàng nhái hay hàng kém chất lượng. Mỗi sản phẩm đều đi kèm đầy đủ tem bảo hành, hóa đơn và chứng nhận nguồn gốc rõ ràng
</p>
<p>
    2. Chính sách bảo hành minh bạch
Warstorm áp dụng chính sách bảo hành minh bạch, rõ ràng theo đúng tiêu chuẩn của nhà sản xuất. Khi có sự cố kỹ thuật, khách hàng sẽ được hỗ trợ nhanh chóng với các phương án bảo hành hoặc đổi trả linh hoạt, đảm bảo quyền lợi tối đa cho người mua.
</p>
<p>
    3. Dịch vụ khách hàng chuyên nghiệp
Đội ngũ tư vấn viên của Warstorm được đào tạo bài bản, luôn sẵn sàng hỗ trợ khách hàng 24/7 qua điện thoại, email hoặc chat trực tuyến. Chúng tôi đặt sự hài lòng của khách hàng lên hàng đầu, luôn đồng hành cùng bạn từ lúc tư vấn đến sau khi nhận hàng.
</p>
<p>
    4. Giao hàng nhanh – Thanh toán linh hoạt
Warstorm sở hữu hệ thống kho hàng và đối tác vận chuyển trên toàn quốc, đảm bảo giao hàng nhanh trong 24–72 giờ tùy khu vực. Khách hàng có thể lựa chọn nhiều hình thức thanh toán linh hoạt như: thanh toán khi nhận hàng (COD), chuyển khoản ngân hàng, ví điện tử hoặc trả góp lãi suất thấp.
</p>
<p>
    5. Đánh giá tích cực từ cộng đồng người dùng
Warstorm luôn nhận được hàng nghìn phản hồi tích cực từ khách hàng trên toàn quốc. Sự tin tưởng của người tiêu dùng chính là nền tảng vững chắc giúp chúng tôi ngày càng hoàn thiện và phát triển.
</p>
<p>
    Warstorm – Thiết bị công nghệ chính hãng, dịch vụ tận tâm, uy tín hàng đầu!

Hãy để Warstorm.vn trở thành người bạn đồng hành đáng tin cậy của bạn trong thế giới công nghệ hiện đại và bền vững.
</p>
        </div>

        <div class="row mt-5">
            <div class="col-md-4">
                <div class="feature-item">
                    <i class="fas fa-medal"></i>
                    <h4>Chất lượng</h4>
                    <p>Cam kết cung cấp sản phẩm chính hãng, chất lượng cao</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <i class="fas fa-handshake"></i>
                    <h4>Uy tín</h4>
                    <p>Xây dựng niềm tin với khách hàng qua dịch vụ chuyên nghiệp</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h4>Hỗ trợ</h4>
                    <p>Đội ngũ tư vấn nhiệt tình, hỗ trợ 24/7</p>
                </div>
            </div>
        </div>

        <div class="about-card mt-5">
            <h2 class="about-title">Đội ngũ của chúng tôi</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="images/team1.jpg" alt="Team Member">
                        <h5>Nguyễn Văn A</h5>
                        <p>Giám đốc</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="images/team2.jpg" alt="Team Member">
                        <h5>Trần Thị B</h5>
                        <p>Quản lý kinh doanh</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <img src="images/team3.jpg" alt="Team Member">
                        <h5>Lê Văn C</h5>
                        <p>Kỹ thuật viên</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-card mt-5">
            <h2 class="about-title">Tầm nhìn và Sứ mệnh</h2>
            <div class="row">
                <div class="col-md-6">
                    <h4><i class="fas fa-eye text-primary"></i> Tầm nhìn</h4>
                    <p>
                        Trở thành địa chỉ mua sắm công nghệ uy tín hàng đầu tại Việt Nam, 
                        mang đến trải nghiệm mua sắm tốt nhất cho khách hàng.
                    </p>
                </div>
                <div class="col-md-6">
                    <h4><i class="fas fa-bullseye text-primary"></i> Sứ mệnh</h4>
                    <p>
                        Cung cấp sản phẩm chất lượng, dịch vụ chuyên nghiệp và giá cả cạnh tranh, 
                        góp phần nâng cao chất lượng cuộc sống của khách hàng.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về chúng tôi</h5>
                    <p>Warstorm - Cửa hàng máy tính uy tín, chất lượng với nhiều năm kinh nghiệm trong lĩnh vực công nghệ.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh<br>
                        <i class="fas fa-phone"></i> 0123 456 789<br>
                        <i class="fas fa-envelope"></i> info@warstorm.com<br>
                        <i class="fas fa-clock"></i> 8:00 - 22:00 (Thứ 2 - Chủ nhật)
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-2" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="mt-3">
                        <h5>Liên kết nhanh</h5>
                        <ul class="list-unstyled">
                            <li><a href="products.php" class="text-light text-decoration-none">Sản phẩm</a></li>
                            <li><a href="contact.php" class="text-light text-decoration-none">Liên hệ</a></li>
                            <li><a href="about.php" class="text-light text-decoration-none">Giới thiệu</a></li>
                            <li><a href="policy.php" class="text-light text-decoration-none">Chính sách</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <p class="mb-0">&copy; 2024 Warstorm. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 