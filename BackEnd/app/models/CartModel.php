<?php

namespace App\Models;

class CartModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy giỏ hàng của user
    public function getCart($userId) {
        $sql = "SELECT * FROM carts WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addItem($userId, $productId, $quantity) {
        $sql = "INSERT INTO carts (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    // Lấy 1 item trong giỏ hàng
    public function getItem($itemId) {
        $sql = "SELECT * FROM carts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $itemId);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateItem($itemId, $quantity) {
        $sql = "UPDATE carts SET quantity = :quantity WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $itemId);
        $stmt->execute();
        return $this->getItem($itemId);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeItem($itemId) {
        $sql = "DELETE FROM carts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $itemId);
        return $stmt->execute();
    }

    // Xóa toàn bộ giỏ hàng của user
    public function clearCart($userId) {
        $sql = "DELETE FROM carts WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
