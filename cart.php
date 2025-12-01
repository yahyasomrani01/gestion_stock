<?php
// cart.php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/cart.php';

$database = new Database();
$db = $database->getConnection();
$cart = new Cart();

$cartItems = $cart->getItems($db);
$total = $cart->getTotal($db);

include 'includes/header.php';
?>

<div class="cart-container">
    <h1>Votre Panier</h1>
    
    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <p>Votre panier est vide.</p>
            <a href="index.php" class="btn btn-primary">Continuer vos achats</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="cart-product-info">
                                    <?php if ($item['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['nom_pdt']); ?>" class="cart-thumb">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($item['nom_pdt']); ?></span>
                                </div>
                            </td>
                            <td><?php echo number_format($item['prix'], 2); ?> €</td>
                            <td>
                                <input type="number" min="1" value="<?php echo $item['cart_quantity']; ?>" 
                                       class="form-control cart-quantity" data-id="<?php echo $item['id_pdt']; ?>">
                            </td>
                            <td><?php echo number_format($item['subtotal'], 2); ?> €</td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-from-cart" data-id="<?php echo $item['id_pdt']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total</strong></td>
                        <td colspan="2"><strong><?php echo number_format($total, 2); ?> €</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="cart-actions">
            <a href="index.php" class="btn btn-secondary">Continuer vos achats</a>
            <a href="checkout.php" class="btn btn-success">Commander</a>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.cart-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

.cart-table th, .cart-table td {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    text-align: left;
}

.cart-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.cart-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    margin-right: 1rem;
    border-radius: 4px;
}

.cart-product-info {
    display: flex;
    align-items: center;
}

.cart-quantity {
    width: 80px;
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
}

.text-right {
    text-align: right;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-danger {
    background-color: var(--danger-color);
    color: white;
}

.btn-success {
    background-color: var(--success-color);
    color: white;
}

.btn-secondary {
    background-color: var(--secondary-color);
    color: white;
}

@media (max-width: 768px) {
    .cart-table {
        display: block;
        overflow-x: auto;
    }
}
</style>

<?php include 'includes/footer.php'; ?>