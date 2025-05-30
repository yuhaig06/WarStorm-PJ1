Nhóm thành viên: 2 người là Nguyễn Thiện Gia Huy (nhóm trưởng) và Lê Chí Bảo (phụ trợ)

# WarStorm - Game Store & News Platform

## 📝 Mô tả dự án
WarStorm là một nền tảng bán game và tin tức game, cung cấp trải nghiệm mua sắm và cập nhật thông tin game mới nhất cho người dùng.

## 🚀 Tính năng chính

### 🎮 Trang chủ
- Hiển thị game nổi bật
- Tin tức mới nhất
- Danh mục game phổ biến

### 🛒 Cửa hàng
- Danh sách sản phẩm theo danh mục
- Tìm kiếm và lọc sản phẩm
- Chi tiết sản phẩm
- Giỏ hàng và thanh toán

### 📰 Tin tức
- Danh sách bài viết
- Chi tiết bài viết
- Bình luận và đánh giá

### 👤 Tài khoản
- Đăng nhập/Đăng ký
- Quản lý thông tin cá nhân
- Lịch sử đơn hàng

### 🛠️ Trang quản trị
- Quản lý người dùng
- Quản lý sản phẩm
- Quản lý đơn hàng
- Quản lý bài viết
- Thống kê doanh thu

## 🏗️ Cấu trúc dự án

```
PJ1/
├── app/                    # Mã nguồn back-end
│   ├── config/             # Cấu hình ứng dụng
│   ├── controllers/        # Các controller xử lý logic
│   ├── core/               # Core framework
│   ├── models/             # Models tương tác với database
│   └── views/              # Giao diện người dùng
│
├── database/              # Cơ sở dữ liệu
│   ├── warstorm_db.sql     # File SQL tổng hợp
│   └── import_html_to_db.php # Script import dữ liệu
│
├── FrontEnd/              # Giao diện người dùng
│   ├── Admin/             # Giao diện quản trị
│   ├── Home/              # Trang chủ
│   ├── Login-Register/    # Đăng nhập/Đăng ký
│   └── ...
│
└── public/                # Thư mục công khai
    ├── index.php          # File chính
    └── assets/            # Tài nguyên tĩnh (CSS, JS, hình ảnh)
```

## 🛠️ Yêu cầu hệ thống

- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc MariaDB 10.2 trở lên
- Web server (Apache/Nginx) với mod_rewrite bật
- Composer (để cài đặt các thư viện PHP)

## ⚙️ Cài đặt

1. **Cài đặt cơ sở dữ liệu**
   ```bash
   mysql -u root -p < database/warstorm_db.sql
   ```

2. **Cấu hình kết nối CSDL**
   Chỉnh sửa file `app/config/database.php` với thông tin phù hợp:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'warstorm_db');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Cấu hình URL gốc**
   Chỉnh sửa file `app/config/config.php`:
   ```php
   define('URLROOT', 'http://localhost/PJ1/public');
   ```

4. **Phân quyền thư mục**
   Đảm bảo các thư mục sau có quyền ghi:
   - `/uploads`
   - `/app/logs`

## 👨‍💻 Tài khoản mặc định

- **Quản trị viên**
  - Email: admin@example.com
  - Mật khẩu: admin

- **Người dùng thử nghiệm**
  - Email: user@example.com
  - Mật khẩu: user

## 📄 Giấy phép

Dự án được phát triển bởi WarStorm Team. Mọi quyền được bảo lưu.

## 📞 Liên hệ

Nếu bạn có thắc mắc hoặc cần hỗ trợ, vui lòng liên hệ:
- Email: support@warstorm.com
- Website: https://warstorm.com