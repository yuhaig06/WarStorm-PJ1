<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Check if product exists
$stmt = $conn->prepare("SELECT id, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Sản phẩm không tồn tại!";
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get current quantity in cart
$current_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;

// Check if adding one more would exceed stock
if ($current_quantity + 1 > $product['stock']) {
    $_SESSION['error'] = "Số lượng sản phẩm trong kho không đủ!";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Add to cart
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

$_SESSION['success'] = "Đã thêm sản phẩm vào giỏ hàng!";

// Redirect back to previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit(); 