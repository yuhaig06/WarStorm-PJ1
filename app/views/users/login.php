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
    <style>
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        .loading-spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top: 4px solid #00ccff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="container">
        <div class="form-box" id="login-box">
            <div class="logo-container">
                <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo" class="logo">
            </div>
            <h2>Đăng nhập</h2>
            <form id="loginForm" method="POST" action="/PJ1/public/users/login" onsubmit="return showLoadingSpinner()">
                <div class="input-group">
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <span class="input-icon">✉</span>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                    <span class="input-icon">🔒</span>
                    <span class="password-toggle" tabindex="0" onclick="togglePassword('password')" onkeydown="if(event.key==='Enter'){togglePassword('password');}">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" id="remember" name="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a href="#" class="forgot-link">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="submit-btn" id="loginBtn">
                    Đăng nhập <span class="btn-icon"><i class="fas fa-arrow-right"></i></span>
                </button>
                <div class="register-link">
                    Chưa có tài khoản? <a href="/PJ1/public/users/register">Đăng ký ngay</a>
                </div>
            </form>
        </div>
    </div>
    <script>
    function togglePassword(id) {
        var input = document.getElementById(id);
        var icon = document.querySelector('.password-toggle i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            icon.style.transform = 'rotate(25deg) scale(1.1)';
            icon.style.color = '#00ccff';
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            icon.style.transform = 'rotate(0deg) scale(1)';
            icon.style.color = '';
        }
        setTimeout(()=>{icon.style.transform='';}, 350);
    }

    function showLoadingSpinner() {
        var btn = document.getElementById('loginBtn');
        btn.innerHTML = 'Đang xử lý <span class="loading-spinner"></span>';
        btn.disabled = true;
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = 'Đăng nhập <span class="btn-icon"><i class="fas fa-arrow-right"></i></span>';
        }, 1200); // Đảm bảo spinner hiển thị đủ lâu
        return true; // Cho phép submit bình thường
    }
    </script>
</body>
</html>