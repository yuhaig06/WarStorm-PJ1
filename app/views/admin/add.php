<?php 
// Chỉ include phần sidebar nếu chưa được include
if (!isset($sidebar_included)) {
    $sidebar_included = true;
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thêm sản phẩm mới - Quản trị</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="/PJ1/FrontEnd/Admin/css/admin.css">
        <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
        <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
        <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
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
                        <a href="/PJ1/public/admin/add" class="nav-link active">
                            <i class="fas fa-plus-circle me-2"></i> Thêm sản phẩm
                        </a>
                        <a href="/PJ1/public/admin/edit">
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
    <?php } ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Thêm sản phẩm mới</h1>
                </div>

                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <?php 
                        // Hiển thị thông báo lỗi/success từ session
                        if (isset($_SESSION['error_message'])): 
                        ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['error_message']; 
                                unset($_SESSION['error_message']); // Xóa thông báo sau khi hiển thị
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php 
                        elseif (isset($_SESSION['success_message'])): 
                        ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['success_message']; 
                                unset($_SESSION['success_message']); // Xóa thông báo sau khi hiển thị
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php 
                        elseif (isset($data['message'])): 
                        ?>
                            <div class="alert alert-<?php echo $data['message_type'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $data['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card shadow-sm">
                            <div class="card-body">
                                <form action="/PJ1/public/admin/add" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control <?php echo !empty($data['name_err']) ? 'is-invalid' : ''; ?>" 
                                               id="name" value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>">
                                        <div class="invalid-feedback"><?php echo $data['name_err'] ?? ''; ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select" id="category_id" required>
                                            <option value="">Chọn danh mục</option>
                                            <option value="1">Bàn phím cơ</option>
                                            <option value="2">Chuột gaming</option>
                                            <option value="3">Tai nghe gaming</option>
                                            <option value="4">Lót chuột</option>
                                            <option value="5">Ghế gaming</option>
                                            <option value="6">Màn hình</option>
                                            <option value="7">Bàn gaming</option>
                                            <option value="8">Phụ kiện khác</option>
                                        </select>
                                        <div class="invalid-feedback">Vui lòng chọn danh mục</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="price" class="form-control <?php echo !empty($data['price_err']) ? 'is-invalid' : ''; ?>" 
                                                       id="price" min="0" step="1000" value="<?php echo htmlspecialchars($data['price'] ?? '0'); ?>">
                                                <span class="input-group-text">₫</span>
                                                <div class="invalid-feedback"><?php echo $data['price_err'] ?? ''; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="stock" class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                            <input type="number" name="stock" class="form-control <?php echo !empty($data['stock_err']) ? 'is-invalid' : ''; ?>" 
                                                   id="stock" min="0" value="<?php echo htmlspecialchars($data['stock'] ?? '0'); ?>">
                                            <div class="invalid-feedback"><?php echo $data['stock_err'] ?? ''; ?></div>
                                        </div>
                                    </div>
                        </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả sản phẩm</label>
                                        <textarea name="description" class="form-control" id="description" rows="4"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                                        <div class="invalid-feedback"><?php echo $data['description_err'] ?? ''; ?></div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="image" class="form-label">Hình ảnh sản phẩm <span class="text-danger">*</span></label>
                                        <input type="file" name="image" class="form-control <?php echo !empty($data['image_err']) ? 'is-invalid' : ''; ?>" 
                                               id="image" accept="image/*" onchange="previewImage(this)">
                                        <div class="invalid-feedback"><?php echo $data['image_err'] ?? ''; ?></div>
                                        <div id="imagePreview" class="mt-2 text-center">
                                            <img id="preview" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" 
                                                 style="max-width: 200px; max-height: 200px; display: none;" class="img-thumbnail">
                                        </div>
                                        <small class="text-muted">Định dạng: JPG, JPEG, PNG. Kích thước tối đa: 2MB</small>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="/PJ1/public/admin" class="btn btn-outline-secondary me-md-2">
                                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Lưu sản phẩm
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
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
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = ['name', 'category_id', 'price', 'stock'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const element = document.querySelector(`[name="${field}"]`);
                if (!element.value.trim()) {
                    element.classList.add('is-invalid');
                    const errorElement = element.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                        errorElement.textContent = 'Vui lòng nhập trường này';
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
                }
            });
        });
    </script>
</body>
</html>