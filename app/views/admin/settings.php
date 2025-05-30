<?php 
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /PJ1/public/users/login');
    exit();
}

$title = $title ?? 'Cài đặt hệ thống | Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/PJ1/FrontEnd/Admin/css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="text-center py-3">
                    <a href="/PJ1/public/home" class="logo-link">
                        <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo" class="logo">
                    </a>
                </div>
                <nav class="nav flex-column">
                    <a href="/PJ1/public/admin" class="nav-link">
                        <i class="fas fa-tachometer-alt me-2"></i> Tổng quan
                    </a>
                    <a href="/PJ1/public/admin/add" class="nav-link">
                        <i class="fas fa-plus-circle me-2"></i> Thêm sản phẩm
                    </a>
                    <a href="/PJ1/public/admin/edit" class="nav-link">
                        <i class="fas fa-edit me-2"></i> Chỉnh sửa sản phẩm
                    </a>
                    <a href="/PJ1/public/admin/users" class="nav-link">
                        <i class="fas fa-users me-2"></i> Người dùng
                    </a>
                    <a href="/PJ1/public/admin/settings" class="nav-link active">
                        <i class="fas fa-cog me-2"></i> Cài đặt
                    </a>
                    <form method="post" action="/PJ1/public/home" class="d-inline w-100">
                        <input type="hidden" name="logout" value="1">
                        <button type="submit" class="nav-link border-0 bg-transparent text-start w-100 text-decoration-none">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Cài đặt hệ thống</h1>
                </div>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-cog me-2"></i>Cài đặt chung</span>
                    </div>
                    <div class="card-body">
                        <form action="/PJ1/public/admin/settings" method="POST">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Tên website</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($config['site_name'] ?? 'WarStorm'); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Email quản trị</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($config['admin_email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="items_per_page" class="form-label">Số mục mỗi trang</label>
                                <input type="number" class="form-control" id="items_per_page" name="items_per_page" min="5" max="100" value="<?php echo htmlspecialchars($config['items_per_page'] ?? '10'); ?>">
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode" <?php echo (isset($config['maintenance_mode']) && $config['maintenance_mode']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="maintenance_mode">Chế độ bảo trì</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Thông tin hệ thống
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Phiên bản PHP</th>
                                        <td><?php echo phpversion(); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Phiên bản MySQL</th>
                                        <td><?php 
                                            try {
                                                $pdo = new PDO('mysql:host=localhost;dbname=warstorm_db', 'root', '');
                                                $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
                                                echo $version ?: 'Không xác định';
                                            } catch (Exception $e) {
                                                echo 'Không thể kết nối database: ' . $e->getMessage();
                                            }
                                        ?></td>
                                    </tr>

                                    <tr>
                                        <th>Hệ điều hành</th>
                                        <td><?php echo php_uname('s') . ' ' . php_uname('r'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Web Server</th>
                                        <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Không xác định'; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Xử lý form cài đặt
        document.querySelector('form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Thêm hiệu ứng loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';
            
            // Gửi form
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                }
                return response.text();
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Đã xảy ra lỗi khi lưu cài đặt. Vui lòng thử lại.',
                    confirmButtonColor: '#0d6efd'
                });
            });
        });
    </script>
</body>
</html>
