<?php // require APPROOT . '/views/inc/header.php'; ?>

<?php /* ĐỒNG BỘ ĐƯỜNG DẪN RESOURCE */ ?>
<head>
    <title>Quản lý sản phẩm | Admin Store</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PJ1/FrontEnd/Store/css/store.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
</head>
<body>
    <div class="manage-container">
        <div class="manage-title">Quản lý sản phẩm</div>
        <div class="manage-header" style="justify-content:center;">
            <a href="<?php echo URLROOT; ?>/store/add" class="add-btn">
                <i class="fas fa-plus"></i> Thêm sản phẩm mới
            </a>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Tồn kho</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Đảm bảo không lỗi undefined $products
                                    $products = isset($data['products']) ? $data['products'] : [];
                                    ?>
                                    <?php if (!empty($products)): ?>
                                        <?php foreach($products as $product): ?>
                                            <tr>
                                                <td><?php echo $product->id; ?></td>
                                                <td>
                                                    <img src="<?php echo URLROOT; ?>/img/store/<?php echo $product->image; ?>" 
                                                         alt="<?php echo $product->name; ?>" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 50px;">
                                                </td>
                                                <td><?php echo $product->name; ?></td>
                                                <td>
                                                    <?php 
                                                    foreach($data['categories'] as $category) {
                                                        if($category->id == $product->category_id) {
                                                            echo $category->name;
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo number_format($product->price); ?> VNĐ</td>
                                                <td><?php echo $product->stock; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($product->created_at)); ?></td>
                                                <td>
                                                    <a href="<?php echo URLROOT; ?>/store/edit/<?php echo $product->id; ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="deleteProduct(<?php echo $product->id; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="no-products">Không có sản phẩm nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal xác nhận xóa sản phẩm -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa sản phẩm</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa sản phẩm này?</p>
                        <p>Hành động này không thể hoàn tác.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Xóa</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    let productToDelete = null;

    function deleteProduct(productId) {
        productToDelete = productId;
        $('#deleteModal').modal('show');
    }

    function confirmDelete() {
        if (productToDelete) {
            window.location.href = `<?php echo URLROOT; ?>/store/delete/${productToDelete}`;
        }
    }

    function number_format(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }
    </script>

    <?php // require_once APPROOT . '/app/views/inc/footer.php'; ?>