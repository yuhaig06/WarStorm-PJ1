<?php 
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /PJ1/public/users/login');
    exit();
}

$title = $pageTitle ?? 'Quản lý sản phẩm | Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PJ1/FrontEnd/Admin/css/admin.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(3px);
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .main-content { 
            padding: 20px;
            width: 100%;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
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
                    <a href="/PJ1/public/admin/edit" class="nav-link active">
                        <i class="fas fa-edit me-2"></i> Chỉnh sửa sản phẩm
                    </a>
                    <a href="/PJ1/public/admin/users" class="nav-link">
                        <i class="fas fa-users me-2"></i> Người dùng
                    </a>
                    <a href="/PJ1/public/admin/settings" class="nav-link">
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
                <?php if (isset($message) && $message): ?>
                    <div class="alert alert-<?php echo $message_type ?? 'success'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($products) && !empty($products)): // Hiển thị danh sách sản phẩm ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Danh sách sản phẩm</h2>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="/PJ1/FrontEnd/Store/img/<?php echo htmlspecialchars($p->image ?? 'default.jpg'); ?>" 
                                                 class="product-image" alt="<?php echo htmlspecialchars($p->name); ?>">
                                        </td>
                                        <td><?php echo htmlspecialchars($p->name); ?></td>
                                        <td><?php echo number_format($p->price, 0, ',', '.') . ' ₫'; ?></td>
                                        <td><?php echo $p->stock; ?></td>
                                        <td class="d-flex gap-2">
                                            <a href="/PJ1/public/admin/edit/<?php echo $p->id; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <form action="/PJ1/public/admin/delete/<?php echo $p->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.');">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php elseif (isset($product) && $product): // Hiển thị form chỉnh sửa ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Chỉnh sửa sản phẩm</h2>
                        <a href="/PJ1/public/admin/edit" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form action="/PJ1/public/admin/update/<?php echo $product->id; ?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_method" value="PUT">
                                
                                <div class="mb-3">
                                    <label class="form-label">Tên sản phẩm</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($product->name); ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá (VNĐ)</label>
                                            <input type="number" name="price" class="form-control" 
                                                   value="<?php echo $product->price; ?>" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng</label>
                                            <input type="number" name="stock" class="form-control" 
                                                   value="<?php echo $product->stock; ?>" min="0" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="description" rows="3" class="form-control"><?php 
                                        echo htmlspecialchars($product->description ?? ''); 
                                    ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Hình ảnh</label>
                                    <?php if (!empty($product->image)): ?>
                                        <div class="mb-2">
                                            <img src="/PJ1/FrontEnd/Store/img/<?php echo htmlspecialchars($product->image); ?>" 
                                                 class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="image" class="form-control">
                                    <small class="text-muted">Để trống nếu không đổi ảnh</small>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Cập nhật sản phẩm
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Không tìm thấy thông tin sản phẩm. Vui lòng kiểm tra lại.
                    </div>
                    <a href="/PJ1/public/admin" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i> Về trang quản trị
                    </a>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Preview image before upload -->
    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }
            
            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }

        // Validate form before submit
        document.querySelector('form')?.addEventListener('submit', function(e) {
            const requiredFields = ['name', 'category_id', 'price', 'stock'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const element = this.querySelector(`[name="${field}"]`);
                if (!element) return;
                
                if (!element.value.trim()) {
                    element.classList.add('is-invalid');
                    const errorElement = element.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                        errorElement.textContent = 'Vui lòng nhập trường này';
                    } else {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Vui lòng nhập trường này';
                        element.parentNode.insertBefore(errorDiv, element.nextSibling);
                    }
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Remove invalid class when user starts typing
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    const errorElement = this.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                        errorElement.remove();
                    }
                }
            });
        });
    </script>
</body>
</html>