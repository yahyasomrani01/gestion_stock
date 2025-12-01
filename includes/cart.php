<?php
// includes/cart.php
class Cart {
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Add product to cart
    public function add($productId, $quantity = 1) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        return true;
    }

    // Update quantity
    public function update($productId, $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
            return true;
        } else {
            return $this->remove($productId);
        }
    }

    // Remove product
    public function remove($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }
        return false;
    }

    // Clear cart
    public function clear() {
        $_SESSION['cart'] = [];
        return true;
    }

    // Get cart items with product details
    public function getItems($db) {
        $items = [];
        if (empty($_SESSION['cart'])) {
            return $items;
        }

        $ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        $query = "SELECT * FROM Produit WHERE id_pdt IN ($placeholders)";
        $stmt = $db->prepare($query);
        $stmt->execute($ids);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id_pdt'];
            $row['cart_quantity'] = $_SESSION['cart'][$id];
            $row['subtotal'] = $row['prix'] * $row['cart_quantity'];
            $items[] = $row;
        }
        
        return $items;
    }

    // Get total count
    public function getCount() {
        return array_sum($_SESSION['cart']);
    }

    // Get total price
    public function getTotal($db) {
        $total = 0;
        $items = $this->getItems($db);
        foreach ($items as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }
}
?>
