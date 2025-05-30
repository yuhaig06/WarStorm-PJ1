<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <link rel="stylesheet" href="/PJ1/FrontEnd/Login-Register/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="form-box" id="register-box">
            <div class="logo-container">
                <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo" class="logo">
            </div>
            <h2>Đăng ký tài khoản</h2>
            <?php if (isset($error) && $error): ?>
                <div class="error-message" style="color: #ff4d4f; margin-bottom: 10px; text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form id="registerForm" method="POST" action="/PJ1/public/users/register">
                <div class="input-group">
                    <input type="text" id="full_name" name="full_name" placeholder="Họ và tên" required>
                    <span class="input-icon">👤</span>
                </div>
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Tên đăng nhập" required>
                    <span class="input-icon">👤</span>
                </div>
                <div class="input-group">
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <span class="input-icon">✉</span>
                </div>
                <div class="input-group">
                    <input type="tel" id="phone" name="phone" placeholder="Số điện thoại" required>
                    <span class="input-icon">📱</span>  
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                    <span class="input-icon">🔒</span>
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="input-group">
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Xác nhận mật khẩu" required>
                    <span class="input-icon">🔒</span>
                    <span class="password-toggle" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="terms">
                    <label>
                        <input type="checkbox" id="agreeTerms" required>
                        Tôi đồng ý với <a href="#">điều khoản sử dụng</a>
                    </label>
                </div>
                <button type="submit" class="submit-btn">
                    <span>Đăng ký</span>
                    <span class="btn-icon">→</span>
                </button>
                <p class="switch-form">Đã có tài khoản? <a href="/PJ1/public/users/login">Đăng nhập</a></p>
            </form>
        </div>
    </div>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.nextElementSibling.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
