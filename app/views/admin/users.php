<?php 
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /PJ1/public/users/login');
    exit();
}

$title = 'Quản lý người dùng | Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.5rem 1rem;
            margin: 0.2rem 0;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 500;
        }
        .table th {
            border-top: none;
            border-bottom: 2px solid #dee2e6;
        }
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        .status-active {
            background-color: #198754;
        }
        .status-inactive {
            background-color: #6c757d;
        }
        .role-admin {
            background-color: #0d6efd;
        }
        .role-user {
            background-color: #6f42c1;
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
                        <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo" class="logo" style="max-width: 150px;">
                    </a>
                </div>
                <nav class="nav flex-column">
                    <a href="/PJ1/public/admin" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i> Tổng quan
                    </a>
                    <a href="/PJ1/public/admin/add" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Thêm sản phẩm
                    </a>
                    <a href="/PJ1/public/admin/edit" class="nav-link">
                        <i class="fas fa-edit"></i> Chỉnh sửa sản phẩm
                    </a>
                    <a href="/PJ1/public/admin/users" class="nav-link active">
                        <i class="fas fa-users"></i> Người dùng
                    </a>
                    <a href="/PJ1/public/admin/settings" class="nav-link">
                        <i class="fas fa-cog"></i> Cài đặt
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
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($data['message'])): ?>
                    <div class="alert alert-<?php echo $data['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo $data['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Quản lý người dùng</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-1"></i> Thêm mới
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đăng ký</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['users']['data'])): ?>
                                        <?php foreach ($data['users']['data'] as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo !empty($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Chưa cập nhật'; ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <?php 
                                                    $roleNames = [
                                                        'admin' => 'Quản trị viên',
                                                        'author' => 'Tác giả',
                                                        'user' => 'Người dùng'
                                                    ];
                                                    $roleClass = [
                                                        'admin' => 'role-admin',
                                                        'author' => 'role-author',
                                                        'user' => 'role-user'
                                                    ][$user['role']] ?? 'role-user';
                                                    ?>
                                                    <span class="badge rounded-pill <?php echo $roleClass; ?>">
                                                        <?php echo $roleNames[$user['role']] ?? $user['role']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill status-<?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                                        <?php echo $user['is_active'] ? 'Đã xác thực' : 'Chưa xác thực'; ?>
                                                    </span>
                                                </td>
                                                <td title="<?php echo date('d/m/Y H:i:s', strtotime($user['created_at'])); ?>">
                                                    <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                                                data-id="<?php echo $user['id']; ?>"
                                                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                                data-role="<?php echo $user['role']; ?>"
                                                                data-status="<?php echo $user['is_active']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="/PJ1/public/admin/delete/<?php echo $user['id']; ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Không có dữ liệu người dùng</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($data['users']['data']) && $data['users']['last_page'] > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $data['users']['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $data['users']['current_page'] - 1; ?>">Trước</a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $data['users']['last_page']; $i++): ?>
                                        <li class="page-item <?php echo $i == $data['users']['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo $data['users']['current_page'] >= $data['users']['last_page'] ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $data['users']['current_page'] + 1; ?>">Tiếp</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/PJ1/public/admin/addUser" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Thêm người dùng mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (!empty($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        
                        <?php 
                        $formData = $_SESSION['form_data'] ?? [];
                        unset($_SESSION['form_data']);
                        ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                   value="<?php echo htmlspecialchars($formData['full_name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user" <?php echo ($formData['role'] ?? '') === 'user' ? 'selected' : ''; ?>>Người dùng</option>
                                <option value="admin" <?php echo ($formData['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editUserForm" action="" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Chỉnh sửa người dùng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Vai trò</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="user">Người dùng</option>
                                <option value="admin">Quản trị viên</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                Kích hoạt tài khoản
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Xử lý sự kiện khi click nút xóa
        $(document).on('click', '.btn-delete-user', function(e) {
            e.preventDefault();
            
            if (!confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
                return false;
            }
            
            const userId = $(this).data('id');
            const $deleteButton = $(this);
            
            // Vô hiệu hóa nút xóa để tránh click nhiều lần
            $deleteButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xóa...');
            
            console.log('Đang gửi yêu cầu xóa user ID:', userId);
            
            // Sử dụng URL tương đối để tránh vấn đề về base URL
            $.ajax({
                url: '/PJ1/public/admin/delete/' + userId,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>',
                    ajax: '1'
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Phản hồi từ server:', response);
                    if (response && response.success) {
                        // Hiển thị thông báo thành công
                        showAlert('success', response.message || 'Xóa người dùng thành công');
                        // Xóa dòng khỏi bảng
                        $deleteButton.closest('tr').fadeOut(400, function() {
                            $(this).remove();
                        });
                    } else {
                        const errorMsg = response && response.message 
                            ? response.message 
                            : 'Có lỗi xảy ra khi xóa người dùng';
                        console.error('Lỗi từ server:', errorMsg);
                        showAlert('danger', errorMsg);
                        $deleteButton.prop('disabled', false).html('<i class="fas fa-trash-alt"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Lỗi AJAX:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    let errorMessage = 'Có lỗi xảy ra khi xóa người dùng';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response && response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Không thể phân tích phản hồi JSON:', e);
                    }
                    
                    showAlert('danger', errorMessage);
                    $deleteButton.prop('disabled', false).html('<i class="fas fa-trash-alt"></i>');
                }
            });
        });

        // Hàm hiển thị thông báo
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Thêm thông báo vào đầu phần main content
            $('main').prepend(alertHtml);
            
            // Tự động ẩn thông báo sau 5 giây
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        // Xử lý sự kiện khi modal chỉnh sửa hiển thị
        const editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const username = button.getAttribute('data-username');
                const email = button.getAttribute('data-email');
                const role = button.getAttribute('data-role');
                const isActive = button.getAttribute('data-status') === '1';
                
                // Cập nhật form
                const form = document.getElementById('editUserForm');
                form.action = `/PJ1/public/admin/users/update/${id}`;
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_role').value = role;
                document.getElementById('edit_is_active').checked = isActive;
            });
        }
        
        // Thêm debug log khi trang load
        console.log('Page loaded');

        // Thêm event listener cho tất cả các form xóa
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            const deleteButtons = document.querySelectorAll('.delete-user-btn');
            console.log('Found delete buttons:', deleteButtons.length);
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const username = this.dataset.username;
                    
                    if (confirm(`Bạn có chắc chắn muốn xóa người dùng "${username}"?`)) {
                        // Tạo form và submit ngay lập tức
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/PJ1/public/admin/users/delete/${id}`;
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
