<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
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
        <div class="form-box" id="login-box">
            <div class="logo-container">
                <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo" class="logo">
            </div>
            <h2>Đăng nhập</h2>
            <form id="loginForm" method="POST" action="/PJ1/BackEnd/public/users/login">
                <div class="input-group">
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <span class="input-icon">✉</span>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                    <span class="input-icon">🔒</span>
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" id="remember" name="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a href="#" class="forgot-link">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="submit-btn">
                    <span>Đăng nhập</span>
                    <span class="btn-icon">→</span>
                </button>
                <p class="switch-form">Chưa có tài khoản? <a href="/PJ1/BackEnd/public/users/register">Đăng ký ngay</a></p>
                <div class="social-login">
                    <p>Hoặc đăng nhập với</p>
                    <div class="social-buttons">
                        <a href="/PJ1/BackEnd/public/auth/login/google" class="social-button google">
                            <i class="fab fa-google"></i>
                            <span>Google</span>
                        </a>
                        <a href="/PJ1/BackEnd/public/auth/login/discord" class="social-button discord">
                            <i class="fab fa-discord"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                </div>
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