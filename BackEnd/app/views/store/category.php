<?php /* require APPROOT . '/views/inc/header.php'; */ ?>

<?php /* ĐỒNG BỘ ĐƯỜNG DẪN RESOURCE */ ?>
<head>
    <title>Danh mục sản phẩm | Store</title>
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
<?php if (!empty($data['error'])): ?>
    <div class="category-error-container">
        <div class="category-error-card">
            <div class="category-error-icon">⚠️</div>
            <div class="category-error-title"><?php echo $data['error']; ?></div>
            <div class="category-error-actions">
                <a href="<?php echo URLROOT; ?>/store" class="category-btn">Về trang sản phẩm</a>
                <button onclick="window.history.back()" class="category-btn category-btn-outline">Quay lại</button>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="category-container">
        <div class="category-sidebar">
            <h3>Danh mục sản phẩm</h3>
            <ul class="category-list">
                <?php foreach($data['categories'] as $category): ?>
                    <li class="category-item <?php echo ($category->id == $data['category']->id) ? 'active' : ''; ?>">
                        <a href="<?php echo URLROOT; ?>/store/category/<?php echo $category->id; ?>">
                            <?php echo $category->name; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="category-main">
            <div class="category-header">
                <h1><?php echo $data['category']->name; ?></h1>
                <p class="description"> <?php echo $data['category']->description; ?> </p>
            </div>
            <div class="category-products">
                <?php if(empty($data['products'])): ?>
                    <div class="no-products">Không có sản phẩm nào trong danh mục này.</div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach($data['products'] as $product): ?>
                            <div class="product-card">
                                <a href="<?php echo URLROOT; ?>/store/details/<?php echo $product->id; ?>">
                                    <img src="<?php echo URLROOT; ?>/img/store/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>" class="product-image">
                                    <div class="product-info">
                                        <h4 class="product-name"><?php echo $product->name; ?></h4>
                                        <div class="product-price"><?php echo number_format($product->price); ?> VNĐ</div>
                                        <p class="card-text stock">Còn lại: <?php echo $product->stock; ?> sản phẩm</p>
                                        <div class="card-actions">
                                            <button class="btn btn-success" onclick="addToCart(<?php echo $product->id; ?>)">Thêm vào giỏ</button>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal xác nhận mua hàng -->
<div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">Xác nhận mua hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn mua sản phẩm này?</p>
                <p>Tên sản phẩm: <span id="productName"></span></p>
                <p>Giá: <span id="productPrice"></span> VNĐ</p>
                <div class="form-group">
                    <label for="quantity">Số lượng:</label>
                    <input type="number" class="form-control" id="quantity" min="1" value="1">
                </div>
                <p>Tổng tiền: <span id="totalPrice"></span> VNĐ</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="confirmPurchase()">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentProduct = null;

function addToCart(productId) {
    currentProduct = productId;
    
    // Lấy thông tin sản phẩm từ data attribute
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    const productName = productCard.querySelector('.card-title').textContent;
    const productPrice = parseFloat(productCard.querySelector('.price').textContent.replace(/[^\d]/g, ''));
    
    document.getElementById('productName').textContent = productName;
    document.getElementById('productPrice').textContent = number_format(productPrice);
    updateTotalPrice(productPrice);
    
    $('#purchaseModal').modal('show');
}

function updateTotalPrice(price) {
    const quantity = document.getElementById('quantity').value;
    const total = quantity * price;
    document.getElementById('totalPrice').textContent = number_format(total);
}

function confirmPurchase() {
    const quantity = document.getElementById('quantity').value;
    
    fetch('<?php echo URLROOT; ?>/store/processOrder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            productId: currentProduct,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đặt hàng thành công!');
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi đặt hàng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi đặt hàng');
    });
}

function number_format(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

document.getElementById('quantity').addEventListener('change', function() {
    const price = parseFloat(document.getElementById('productPrice').textContent.replace(/[^\d]/g, ''));
    updateTotalPrice(price);
});
</script>

<?php /* require APPROOT . '/views/inc/footer.php'; */ ?>