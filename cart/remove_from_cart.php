<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: cart.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Remove item from cart
if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
    $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng!";
}

// Redirect back to cart
header("Location: cart.php");
exit(); 