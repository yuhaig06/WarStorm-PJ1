<?php
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: /PJ1/public/users/login');
    exit();
}

// Format số tiền
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' ₫';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - WarStorm</title>
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
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
                    <a href="/PJ1/public/admin" class="active">
                        <i class="fas fa-tachometer-alt me-2"></i> Tổng quan
                    </a>
                    <a href="/PJ1/public/admin/add">
                        <i class="fas fa-plus-circle me-2"></i> Thêm sản phẩm
                    </a>
                    <a href="/PJ1/public/admin/edit">
                        <i class="fas fa-edit me-2"></i> Chỉnh sửa sản phẩm
                    </a>
                    <a href="/PJ1/public/admin/users">
                        <i class="fas fa-users me-2"></i> Người dùng
                    </a>
                    <a href="/PJ1/public/admin/settings">
                        <i class="fas fa-cog me-2"></i> Cài đặt
                    </a>
                    <form method="post" action="/PJ1/public/home" class="d-inline">
                        <input type="hidden" name="logout" value="1">
                        <button type="submit" class="nav-link border-0 bg-transparent text-start w-100 text-decoration-none">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tổng quan</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> In trang
                        </button>
                    </div>
                </div>

                <!-- Thông báo -->
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
                
                <!-- Thống kê nhanh -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Tổng doanh thu (tháng)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($monthlyRevenue) ? formatPrice($monthlyRevenue) : '0 ₫'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Đơn hàng mới (hôm nay)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $orderCountToday ?? '0'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Người dùng mới (tháng)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $newUsersThisMonth ?? '0'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Sản phẩm sắp hết hàng</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $lowStockProducts ?? '0'; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biểu đồ và bảng -->
                <div class="row">
                    <!-- Biểu đồ doanh thu -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Tổng quan doanh thu</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-area">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê đơn hàng -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Trạng thái đơn hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie pt-4 pb-2">
                                    <canvas id="orderStatusChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <span class="me-2">
                                        <i class="fas fa-circle text-primary"></i> Đang xử lý
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-circle text-success"></i> Đã giao hàng
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-circle text-info"></i> Đang giao
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Đơn hàng gần đây -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Đơn hàng gần đây</h6>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary" id="refreshOrders">
                                <i class="fas fa-sync-alt"></i> Làm mới
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Số sản phẩm</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="recentOrdersTable">
                                    <?php 
                                    // Lấy danh sách đơn hàng gần đây nếu chưa có
                                    $orderModel = new \App\Models\OrderModel();
                                    $recentOrders = $orderModel->getRecentOrders(5); // Lấy 5 đơn hàng gần nhất
                                    
                                    if (!empty($recentOrders)): 
                                        foreach ($recentOrders as $order): 
                                            $orderNumber = $order->order_number ?? 'ORD' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                                            $customerName = $order->customer_name ?? 'Khách vãng lai';
                                            
                                            // Lấy số lượng sản phẩm trong đơn hàng
                                            $itemCount = $orderModel->countItems($order->id);
                                            
                                            $totalAmount = $order->final_amount ?? $order->total_amount ?? 0;
                                            $status = $order->order_status ?? 'pending';
                                            $createdAt = $order->created_at ?? date('Y-m-d H:i:s');
                                            $paymentStatus = $order->payment_status ?? 'pending';
                                    ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars($orderNumber) ?></td>
                                        <td><?= htmlspecialchars($customerName) ?></td>
                                        <td><?= $itemCount ?></td>
                                        <td><?= number_format($totalAmount, 0, ',', '.') ?>đ</td>
                                        <td>
                                            <?php 
                                                // Màu sắc cho trạng thái đơn hàng
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'refunded' => 'secondary',
                                                    'failed' => 'danger'
                                                ][strtolower($status)] ?? 'secondary';
                                                
                                                // Màu sắc cho trạng thái thanh toán
                                                $paymentStatusClass = [
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'failed' => 'danger',
                                                    'refunded' => 'info',
                                                    'cancelled' => 'secondary'
                                                ][strtolower($paymentStatus)] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?> mb-1" title="Trạng thái đơn hàng">
                                                <?= ucfirst($status) ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                <span class="badge bg-<?= $paymentStatusClass ?>" title="Trạng thái thanh toán">
                                                    <?= ucfirst($paymentStatus) ?>
                                                </span>
                                            </small>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($createdAt)) ?></td>
                                            <td>
                                            <button onclick="viewOrderDetail(<?= $order->id ?>)" class="btn btn-sm btn-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="editOrder(<?= $order->id ?>)" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteOrder(<?= $order->id ?>, '<?= $orderNumber ?>')" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Không có đơn hàng nào gần đây</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div class="small text-muted">
                            Hiển thị <?= min(5, count($recentOrders ?? [])) ?> đơn hàng gần nhất
                        </div>
                        <a href="/PJ1/public/admin/orders" class="btn btn-sm btn-primary">
                            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <script>
                // Đợi tài liệu tải xong
                document.addEventListener('DOMContentLoaded', function() {
                    // Làm mới danh sách đơn hàng
                    const refreshButton = document.getElementById('refreshOrders');
                    if (refreshButton) {
                        refreshButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            // Thêm hiệu ứng xoay
                            const icon = this.querySelector('i');
                            icon.classList.add('fa-spin');
                            
                            // Làm mới dữ liệu bằng AJAX
                            fetch(window.location.href, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // Tạo DOM ảo để phân tích cú pháp HTML
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                
                                // Cập nhật bảng đơn hàng
                                const newTable = doc.getElementById('recentOrdersTable');
                                if (newTable) {
                                    document.getElementById('recentOrdersTable').innerHTML = newTable.innerHTML;
                                }
                                
                                // Hiển thị thông báo
                                showToast('Đã cập nhật danh sách đơn hàng', 'success');
                            })
                            .catch(error => {
                                console.error('Lỗi khi tải dữ liệu:', error);
                                showToast('Có lỗi xảy ra khi tải dữ liệu', 'danger');
                            })
                            .finally(() => {
                                // Dừng hiệu ứng xoay
                                icon.classList.remove('fa-spin');
                            });
                        });
                    }
                });

                // Hiển thị thông báo
                function showToast(message, type = 'info') {
                    const toastContainer = document.getElementById('toastContainer');
                    if (!toastContainer) return;
                    
                    const toast = document.createElement('div');
                    toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
                    toast.setAttribute('role', 'alert');
                    toast.setAttribute('aria-live', 'assertive');
                    toast.setAttribute('aria-atomic', 'true');
                    
                    toast.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    `;
                    
                    toastContainer.appendChild(toast);
                    
                    // Tự động ẩn thông báo sau 3 giây
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => toast.remove(), 150);
                    }, 3000);
                }

                // Khởi tạo biểu đồ doanh thu
                const ctx = document.getElementById('revenueChart').getContext('2d');
                const revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($chartLabels ?? []) ?>,
                        datasets: [{
                            label: 'Doanh thu',
                            data: <?= json_encode($chartData ?? []) ?>,
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            pointRadius: 3,
                            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                            pointBorderColor: 'rgba(78, 115, 223, 1)',
                            pointHoverRadius: 3,
                            pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                            pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 10,
                                right: 25,
                                top: 25,
                                bottom: 0
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    maxTicksLimit: 7
                                }
                            },
                            y: {
                                ticks: {
                                    maxTicksLimit: 5,
                                    padding: 10,
                                    callback: function(value, index, values) {
                                        return value.toLocaleString() + ' đ';
                                    }
                                },
                                grid: {
                                    color: 'rgb(234, 236, 244)',
                                    zeroLineColor: 'rgb(234, 236, 244)',
                                    drawBorder: false,
                                    borderDash: [2],
                                    zeroLineBorderDash: [2]
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgb(255,255,255)',
                                bodyColor: '#858796',
                                titleMarginBottom: 10,
                                titleFontSize: 14,
                                titleFontColor: '#6e707e',
                                titleFontStyle: 'bold',
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                intersect: false,
                                mode: 'index',
                                caretPadding: 10,
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
                </script>
    <!-- Toast Container -->
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <!-- Các thông báo sẽ được thêm vào đây bằng JavaScript -->
    </div>

    <!-- Modal Xem chi tiết đơn hàng -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn hàng <span id="orderNumber"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <!-- Nội dung sẽ được tải động -->
                    <div class="text-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <a href="#" id="printOrderBtn" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-1"></i> In hóa đơn
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chỉnh sửa đơn hàng -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quickEditOrderForm" class="needs-validation" novalidate>
                        <input type="hidden" id="editOrderId" name="order_id">
                        <div class="mb-3">
                            <label for="editOrderStatus" class="form-label">Trạng thái đơn hàng</label>
                            <select class="form-select" id="editOrderStatus" name="order_status" required>
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang xử lý</option>
                                <option value="shipped">Đang giao hàng</option>
                                <option value="delivered">Đã giao hàng</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPaymentStatus" class="form-label">Trạng thái thanh toán</label>
                            <select class="form-select" id="editPaymentStatus" name="payment_status" required>
                                <option value="pending">Chờ thanh toán</option>
                                <option value="paid">Đã thanh toán</option>
                                <option value="failed">Thanh toán thất bại</option>
                                <option value="refunded">Đã hoàn tiền</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editShippingAddress" class="form-label">Địa chỉ giao hàng</label>
                            <textarea class="form-control" id="editShippingAddress" name="shipping_address" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editNote" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="editNote" name="note" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="saveOrderChanges()">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast CSS -->
    <style>
    .toast {
        min-width: 250px;
        margin-bottom: 0.5rem;
        opacity: 1;
        transition: opacity 0.15s linear;
    }
    .toast:not(.show) {
        display: none;
    }
    .toast.show {
        display: block;
    }
    .toast-body {
        padding: 0.75rem;
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/PJ1/FrontEnd/Admin/js/admin.js"></script>
    <script>
    // Hàm chỉnh sửa đơn hàng
    function editOrder(orderId) {
        // Hiển thị loading
        const modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
        modal.show();

        // Lấy thông tin đơn hàng
        fetch(`/PJ1/public/admin/orders/get/${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const order = data.order;
                    document.getElementById('editOrderId').value = order.id;
                    document.getElementById('editOrderStatus').value = order.order_status;
                    document.getElementById('editPaymentStatus').value = order.payment_status;
                    document.getElementById('editShippingAddress').value = order.shipping_address || '';
                    document.getElementById('editNote').value = order.note || '';
                } else {
                    showToast('Không thể tải thông tin đơn hàng', 'danger');
                    modal.hide();
                }
            })
            .catch(error => {
                console.error('Lỗi khi tải thông tin đơn hàng:', error);
                showToast('Đã xảy ra lỗi khi tải thông tin đơn hàng', 'danger');
                modal.hide();
            });
    }

    // Hàm lưu thay đổi đơn hàng
    function saveOrderChanges() {
        const form = document.getElementById('quickEditOrderForm');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const formData = new FormData(form);
        const orderId = formData.get('order_id');

        // Hiển thị loading
        const saveButton = form.querySelector('button[type="submit"]');
        const originalText = saveButton.innerHTML;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';
        saveButton.disabled = true;

        fetch(`/PJ1/public/admin/orders/update/${orderId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Cập nhật đơn hàng thành công', 'success');
                // Đóng modal
                bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
                // Làm mới trang sau 1 giây
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Có lỗi xảy ra khi cập nhật đơn hàng', 'danger');
            }
        })
        .catch(error => {
            console.error('Lỗi khi cập nhật đơn hàng:', error);
            showToast('Đã xảy ra lỗi khi cập nhật đơn hàng', 'danger');
        })
        .finally(() => {
            // Khôi phục trạng thái nút
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        });
    }

    // Hàm xóa đơn hàng
    function deleteOrder(orderId, orderNumber) {
        if (confirm(`Bạn có chắc chắn muốn xóa đơn hàng #${orderNumber}?`)) {
            // Hiển thị loading
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            button.disabled = true;

            fetch(`/PJ1/public/admin/orders/delete/${orderId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Xóa đơn hàng thành công', 'success');
                    // Xóa dòng trong bảng
                    const row = button.closest('tr');
                    row.style.backgroundColor = '#ffebee';
                    row.style.transition = 'background-color 0.5s';
                    setTimeout(() => {
                        row.remove();
                    }, 500);
                } else {
                    showToast(data.message || 'Có lỗi xảy ra khi xóa đơn hàng', 'danger');
                    // Khôi phục trạng thái nút
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Lỗi khi xóa đơn hàng:', error);
                showToast('Đã xảy ra lỗi khi xóa đơn hàng', 'danger');
                // Khôi phục trạng thái nút
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
        }
    }
    </script>
    
    <script>
    // Biểu đồ doanh thu
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy context của canvas
        const revenueCtx = document.getElementById('revenueChart');
        const orderStatusCtx = document.getElementById('orderStatusChart');
        
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($revenueLabels ?? ['T1', 'T2', 'T3', 'T4', 'T5', 'T6']); ?>,
                    datasets: [{
                        label: 'Doanh thu',
                        data: <?php echo json_encode($revenueData ?? [0, 10000, 5000, 15000, 10000, 20000]); ?>,
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Doanh thu: ' + context.parsed.y.toLocaleString() + ' ₫';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' ₫';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        if (orderStatusCtx) {
            new Chart(orderStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Đang xử lý', 'Đã giao hàng', 'Đang giao', 'Chờ xử lý', 'Đã hủy'],
                    datasets: [{
                        data: <?php echo json_encode($orderStatusData ?? [0, 0, 0, 0, 0]); ?>,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                        hoverBorderColor: 'rgba(234, 236, 244, 1)',
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%',
                },
            });
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
    
    // Xác nhận xóa
    function confirmDelete(event, message = 'Bạn có chắc chắn muốn xóa mục này?') {
        if (!confirm(message)) {
            event.preventDefault();
            return false;
        }
        return true;
    }
    
    // Hàm in trang
    function printPage() {
        window.print();
    }
    </script>
</body>
</html>