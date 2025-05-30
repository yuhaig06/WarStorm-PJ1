let cart = [];

function openCart() {
    renderCart();
    document.getElementById('shopping-cart-modal').style.display = 'block';
    // Reset form mỗi khi mở giỏ hàng
    document.getElementById('fullname').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('address').value = '';
    document.getElementById('note').value = '';
}

function closeCartModal() {
    document.getElementById('shopping-cart-modal').style.display = 'none';
}
function addToCart(id) {
    const productDiv = document.querySelector('.product input[data-id="' + id + '"]').closest('.product');
    const name = productDiv.querySelector('h3').innerText;
    const price = parseInt(productDiv.querySelector('.price').innerText.replace(/\D/g, ''));
    const image = productDiv.querySelector('img').getAttribute('src');
    const quantityInput = productDiv.querySelector('input.quantity');
    const quantity = parseInt(quantityInput.value);

    let item = cart.find(p => p.id === id);
    if (item) {
        item.quantity += quantity;
    } else {
        cart.push({id, name, price, image, quantity});
    }
    updateCartCount();
    alert('Đã thêm vào giỏ!');
}
function buyNow(id) {
    addToCart(id);
    openCart();
}
function updateCartCount() {
    let count = cart.reduce((sum, p) => sum + p.quantity, 0);
    document.getElementById('cart-count').innerText = count;
}
function renderCart() {
    let cartItems = document.getElementById('cart-items');
    if (cart.length === 0) {
        cartItems.innerHTML = '<p>Giỏ hàng trống.</p>';
        document.getElementById('cart-total').innerText = '';
        return;
    }
    let html = '<ul>';
    let total = 0;
    cart.forEach(item => {
        html += `<li>
            <img src='${item.image}' style='width:40px;vertical-align:middle;margin-right:8px;'>
            ${item.name} x ${item.quantity} = <b>${(item.price * item.quantity).toLocaleString()} VNĐ</b>
            <button onclick='removeFromCart(${item.id})' style='margin-left:8px;'>Xóa</button>
        </li>`;
        total += item.price * item.quantity;
    });
    html += '</ul>';
    cartItems.innerHTML = html;
    document.getElementById('cart-total').innerText = 'Tổng cộng: ' + total.toLocaleString() + ' VNĐ';
}

function removeFromCart(id) {
    cart = cart.filter(p => p.id !== id);
    renderCart();
    updateCartCount();
}

function showError(message) {
    const errorDiv = document.querySelector('.error-message');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

async function checkout() {
    try {
        const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
        const fullname = document.getElementById('fullname').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const address = document.getElementById('address').value.trim();
        const note = document.getElementById('note').value.trim();

        // Validate thông tin
        if (!fullname || !phone || !address) {
            showError('Vui lòng điền đầy đủ thông tin giao hàng');
            return;
        }

        // Validate số điện thoại
        const phoneRegex = /^(0|\+84)[1-9][0-9]{8}$/;
        if (!phoneRegex.test(phone)) {
            showError('Số điện thoại không hợp lệ');
            return;
        }

        // Tạo thông tin đơn hàng
        const order = {
            items: cart.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity
            })),
            total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
            paymentMethod,
            customerInfo: { fullname, phone, address, note },
            status: 'pending'
        };

        // Thông tin tài khoản ngân hàng và MoMo
        const paymentInfo = {
            'banking': '\n\n💳 THÔNG TIN CHUYỂN KHOẢN:\n' +
                     'Ngân hàng: Techcombank\n' +
                     'Số tài khoản: 1903666668888\n' +
                     'Chủ tài khoản: NGUYEN VAN A\n' +
                     'Nội dung: Mã đơn hàng ' + Date.now(),
            'momo': '\n\n📱 THÔNG TIN VÍ MoMo:\n' +
                   'Số điện thoại: 0909123456\n' +
                   'Tên: NGUYEN VAN A\n' +
                   'Nội dung: Mã đơn hàng ' + Date.now(),
            'cash': ''
        };

        // Hiển thị xác nhận
        const confirmMessage = `XÁC NHẬN ĐẶT HÀNG\n\n` +
                            `Tổng tiền: ${order.total.toLocaleString()} VNĐ\n` +
                            `Hình thức thanh toán: ${getPaymentMethodName(paymentMethod)}` +
                            paymentInfo[paymentMethod] +
                            '\n\nSau khi chuyển khoản, vui lòng giữ lại biên lai để đối chiếu khi cần thiết.';
        
        if (!confirm(confirmMessage)) {
            return;
        }

        // Log dữ liệu gửi đi
        console.log('Dữ liệu gửi đi (raw):', order);
        
        // Gửi đơn hàng lên server
        const response = await fetch('/PJ1/public/store/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(order)
        });

        // Lấy nội dung phản hồi
        const responseText = await response.text();
        console.log('Phản hồi từ server (raw):', responseText);
        console.log('Status:', response.status);
        console.log('Content-Type:', response.headers.get('content-type'));

        let result;
        try {
            // Thử parse JSON
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('Lỗi khi parse JSON:', e);
            throw new Error('Lỗi xử lý dữ liệu từ máy chủ: ' + responseText.substring(0, 200));
        }

        if (!response.ok) {
            console.error('Lỗi từ server:', result);
            throw new Error(result.message || `Lỗi từ máy chủ (${response.status}): ${response.statusText}`);
        }

        // Xóa giỏ hàng
        cart = [];
        updateCartCount();
        closeCartModal();
        
        // Hiển thị thông báo thành công
        alert('Đặt hàng thành công! Cảm ơn bạn đã mua hàng.');
        
        // Tải lại trang để cập nhật giao diện
        window.location.reload();
        
    } catch (error) {
        console.error('Lỗi khi đặt hàng:', error);
        alert('Có lỗi xảy ra: ' + (error.message || 'Không thể kết nối đến máy chủ'));
    }
}

function getPaymentMethodName(method) {
    const methods = {
        'cash': 'Tiền mặt khi nhận hàng',
        'banking': 'Chuyển khoản ngân hàng',
        'momo': 'Ví điện tử MoMo'
    };
    return methods[method] || method;
}

window.onclick = function(event) {
    const modal = document.getElementById('shopping-cart-modal');
    if (event.target == modal) {
        closeCartModal();
    }
}
