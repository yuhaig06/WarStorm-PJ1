let cart = [];

function openCart() {
    renderCart();
    document.getElementById('shopping-cart-modal').style.display = 'block';
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
    cart = cart.filter(item => item.id !== id);
    updateCartCount();
    renderCart();
}
window.onclick = function(event) {
    const modal = document.getElementById('shopping-cart-modal');
    if (event.target == modal) {
        closeCartModal();
    }
}
