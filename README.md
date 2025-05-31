# 🎮 WarStorm - Game Store & News Platform 🎮

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)

## 👨‍🏫 Giảng viên hướng dẫn
**ThS. Trần Hữu Quốc Văn**

## 👥 Thành viên nhóm
- **Nguyễn Thiện Gia Huy** - Team Leader
- **Lê Chí Bảo** - Support

## 📝 Mô tả dự án
WarStorm là một nền tảng toàn diện kết hợp giữa cửa hàng game trực tuyến và trang tin tức game, mang đến trải nghiệm mua sắm và cập nhật thông tin game mới nhất cho cộng đồng game thủ Việt Nam.

## ✨ Tính năng nổi bật

### 🎮 Trang chủ
- Hiển thị game nổi bật và mới nhất
- Tin tức game cập nhật liên tục
- Danh mục game phổ biến
- Giao diện đáp ứng trên mọi thiết bị

### 🛒 Cửa hàng
- **Danh mục đa dạng**: Phân loại game theo thể loại, nền tảng, giá cả
- **Tìm kiếm thông minh**: Tìm kiếm game theo tên, từ khóa, thể loại
- **Chi tiết sản phẩm**: Đầy đủ thông tin, hình ảnh, video đánh giá
- **Giỏ hàng & Thanh toán**: Hỗ trợ nhiều phương thức thanh toán an toàn
- **Khuyến mãi**: Các chương trình giảm giá, ưu đãi đặc biệt

### 📰 Tin tức & Cộng đồng
- Tin tức game mới nhất
- Đánh giá chi tiết các tựa game
- Bình luận và tương tác cộng đồng
- Chia sẻ bài viết lên mạng xã hội

### 👤 Tài khoản cá nhân
- Đăng ký/Đăng nhập đa nền tảng
- Quản lý thông tin cá nhân
- Theo dõi đơn hàng
- Lịch sử mua hàng
- Yêu thích và đánh giá sản phẩm

### 🛠️ Trang quản trị (Admin)
- **Quản lý người dùng**: Phân quyền, khóa/mở tài khoản
- **Quản lý sản phẩm**: Thêm/sửa/xóa game, danh mục
- **Quản lý đơn hàng**: Xử lý đơn hàng, cập nhật trạng thái
- **Quản lý bài viết**: Đăng tin tức, sự kiện
- **Thống kê & Báo cáo**: Doanh thu, sản phẩm bán chạy

## 🚀 Công nghệ sử dụng

### Frontend
- HTML5, CSS3, JavaScript (ES6+)
- Bootstrap 5.1.3
- jQuery 3.6.0
- SweetAlert2
- Slick Carousel

### Backend
- PHP 7.4+
- MySQL 5.7+
- MVC Architecture
- PDO Database Abstraction

### Công cụ phát triển
- XAMPP/WAMP
- Git & GitHub
- VS Code
- Composer

## 📦 Cài đặt và chạy dự án

### Yêu cầu hệ thống
- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Web server (Apache/Nginx)
- Composer (để quản lý thư viện PHP)

### Hướng dẫn cài đặt

1. **Clone dự án**
   ```bash
   git clone https://github.com/yourusername/warstorm.git
   cd warstorm
   ```

2. **Cài đặt thư viện PHP**
   ```bash
   composer install
   ```

3. **Cấu hình cơ sở dữ liệu**
   - Tạo database mới trong MySQL
   - Import file `database/warstorm.sql` vào database vừa tạo
   - Cập nhật thông tin kết nối database trong file `app/config/database.php`

4. **Cấu hình ứng dụng**
   - Sao chép file `.env.example` thành `.env`
   - Cập nhật các thông số cấu hình trong file `.env`

5. **Chạy ứng dụng**
   - Khởi động web server (Apache/Nginx)
   - Truy cập `http://localhost/warstorm/public`

## 📱 Giao diện

### Giao diện Desktop
![Giao diện Desktop](/public/images/screenshots/desktop-preview.png)

### Giao diện Mobile
![Giao diện Mobile](/public/images/screenshots/mobile-preview.png)

## 📝 Hướng dẫn sử dụng

### Dành cho người dùng
1. **Đăng ký tài khoản** mới hoặc **Đăng nhập** nếu đã có tài khoản
2. Duyệt cửa hàng và thêm sản phẩm vào giỏ hàng
3. Thanh toán đơn hàng và nhận mã kích hoạt game
4. Theo dõi tin tức và đánh giá game mới nhất

### Dành cho quản trị viên
1. Đăng nhập vào trang quản trị
2. Quản lý người dùng, sản phẩm, đơn hàng và bài viết
3. Xem báo cáo thống kê và quản lý khuyến mãi

## 📄 Giấy phép
Dự án được phát triển bởi nhóm WarStorm Team và được cấp phép theo giấy phép MIT.

## 📞 Liên hệ
- Email: contact@warstorm.com
- Website: https://warstorm.com
- Facebook: https://facebook.com/warstorm
- GitHub: https://github.com/yourusername/warstorm
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

## 🙏 Lời cảm ơn

Chúng em xin chân thành cảm ơn:
- Các giảng viên đã tận tình hướng dẫn và hỗ trợ trong suốt quá trình thực hiện dự án
- Các bạn đồng nghiệp đã đóng góp ý kiến quý báu
- Cộng đồng nguồn mở đã cung cấp các thư viện và công cụ hữu ích
- Tất cả mọi người đã ủng hộ và đồng hành cùng chúng tôi

Một lần nữa, xin chân thành cảm ơn!
- Website: https://warstorm.com