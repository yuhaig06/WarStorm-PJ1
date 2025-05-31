<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cửa hàng game trực tuyến với các sản phẩm chất lượng cho game thủ">
    <title>Store - Cửa hàng game</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Store/css/store.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="/PJ1/public/home">
                <img src="/PJ1/FrontEnd/Home/img/logo.png" alt="Logo" class="logo">
            </a>
            <h1>STORE</h1>
            <div class="cart-icon" onclick="openCart()">
                🛒 <span id="cart-count">0</span>
            </div>
            <div class="search-icon" onclick="toggleSearchBar()" title="Tìm kiếm sản phẩm">
                <svg width="26" height="26" fill="#ff9800" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke="#ff9800" stroke-width="2" fill="none"/><line x1="16.5" y1="16.5" x2="22" y2="22" stroke="#ff9800" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div class="menu-icon">☰</div>
        </div>
    </header>
    <main>

        <form id="main-search-form" class="search-form" onsubmit="return false;">
            <div class="search-input-container">
                <input type="text" id="search-input" name="keyword" class="form-control" 
                       placeholder="Nhập tên sản phẩm..." autocomplete="off">
                <button type="button" class="search-button" onclick="searchProducts()">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
        <section class="products" id="product-list">
            <?php if(isset($products) && !empty($products)): ?>
                <?php foreach($products as $product): ?>
                    <div class="product" data-name="<?php echo strtolower(htmlspecialchars($product['name'])); ?>" data-desc="<?php echo strtolower(htmlspecialchars($product['description'])); ?>">
                        <img src="/PJ1/public/assets/img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="price-stock">
                            <span class="price">Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                            <span class="stock">Còn: <?php echo (int)$product['stock']; ?></span>
                        </div>
                        <input type="number" min="1" max="<?php echo (int)$product['stock']; ?>" value="1" class="quantity" data-id="<?php echo (int)$product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo (int)$product['price']; ?>">
                        <div class="button-container" style="display: flex; gap: 10px; justify-content: center;">
                            <button class="add-to-cart-btn" onclick="addToCart(<?php echo (int)$product['id']; ?>)" <?php echo ((int)$product['stock'] <= 0) ? 'disabled style="background:#888;cursor:not-allowed;"' : ''; ?>><?php echo ((int)$product['stock'] <= 0) ? 'Hết hàng' : 'Thêm vào giỏ'; ?></button>
                            <button class="buy-btn" onclick="buyNow(<?php echo (int)$product['id']; ?>)" <?php echo ((int)$product['stock'] <= 0) ? 'disabled style="background:#888;cursor:not-allowed;"' : ''; ?>><?php echo ((int)$product['stock'] <= 0) ? 'Hết hàng' : 'Mua ngay'; ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="edit-error-card" style="margin-top:30px;">
                    <div class="edit-error-icon">😥</div>
                    <div class="edit-error-title">Không tìm thấy sản phẩm phù hợp!</div>
                </div>
            <?php endif; ?>
        </section>
        <!-- Shopping Cart Modal -->
        <div id="shopping-cart-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCartModal()">&times;</span>
                <h2>Giỏ hàng</h2>
                <div id="cart-items"></div>
                <div id="cart-total"></div>
                <div class="payment-methods">
                    <h3>Chọn phương thức thanh toán:</h3>
                    <div class="payment-options">
                        <label>
                            <input type="radio" name="payment" value="cash" checked>
                            <span class="payment-icon">💵</span>
                            Thanh toán tiền mặt khi nhận hàng
                        </label>
                        <label>
                            <input type="radio" name="payment" value="banking">
                            <span class="payment-icon">🏦</span>
                            Chuyển khoản ngân hàng
                        </label>
                        <label>
                            <input type="radio" name="payment" value="momo">
                            <span class="payment-icon">📱</span>
                            Ví điện tử MoMo
                        </label>
                    </div>
                    <div class="shipping-info">
                        <h3>Thông tin giao hàng:</h3>
                        <div class="error-message" style="display: none; color: #ff4444; margin-bottom: 10px;"></div>
                        <input type="text" id="fullname" placeholder="Họ và tên" required>
                        <input type="tel" id="phone" placeholder="Số điện thoại" required>
                        <input type="text" id="address" placeholder="Địa chỉ giao hàng" required>
                        <textarea id="note" placeholder="Ghi chú đơn hàng (nếu có)"></textarea>
                    </div>
                </div>
                <button onclick="checkout()" id="checkout-button">Thanh toán</button>
            </div>
        </div>
    </main>

    <script>
    function toggleSearchBar() {
        var form = document.getElementById('main-search-form');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'flex';
            setTimeout(function() { form.querySelector('input').focus(); }, 100);
        } else {
            form.style.display = 'none';
        }
    }

    function searchProducts() {
        const searchTerm = document.getElementById('search-input').value.trim().toLowerCase();
        const products = document.querySelectorAll('.product');
        let hasResults = false;

        // Ẩn tất cả sản phẩm trước khi tìm kiếm
        products.forEach(product => {
            product.style.display = 'none';
        });

        // Nếu không có từ khóa, hiển thị tất cả sản phẩm
        if (!searchTerm) {
            products.forEach(product => {
                product.style.display = 'block';
            });
            const noResults = document.getElementById('no-results');
            if (noResults) noResults.remove();
            return;
        }

        // Tìm kiếm sản phẩm phù hợp
        products.forEach(product => {
            const productName = product.getAttribute('data-name');
            const productDesc = product.getAttribute('data-desc');
            
            if (productName && productName.includes(searchTerm) || 
                productDesc && productDesc.includes(searchTerm)) {
                product.style.display = 'block';
                hasResults = true;
            }
        });

        // Hiển thị thông báo nếu không tìm thấy kết quả
        const noResults = document.getElementById('no-results');
        if (!hasResults) {
            if (!noResults) {
                const productList = document.getElementById('product-list');
                const message = document.createElement('div');
                message.id = 'no-results';
                message.className = 'no-results';
                message.innerHTML = `
                    <i class="fas fa-search"></i>
                    <p>Không tìm thấy sản phẩm phù hợp với từ khóa "${searchTerm}"</p>
                `;
                productList.appendChild(message);
            }
        } else if (noResults) {
            noResults.remove();
        }
    }

    // Thêm sự kiện khi trang tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý sự kiện nhấn Enter để tìm kiếm
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchProducts();
            }
        });

        // Tự động tìm kiếm khi nhập (với độ trễ 300ms)
        let searchTimer;
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                searchProducts();
            }, 300);
        });
    });
    </script>
    <script src="/PJ1/public/assets/js/store.js"></script>
    </script>
</body>
</html>