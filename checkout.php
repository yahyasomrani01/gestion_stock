<?php
// checkout.php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/cart.php';
require_once 'includes/product.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$cart = new Cart();
$product = new Product();

// Redirect if not logged in
if (!$auth->isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

$cartItems = $cart->getItems($db);
$total = $cart->getTotal($db);

if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

$user = $auth->getUser($_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        try {
            $db->beginTransaction();

            // Create Order
            $query = "INSERT INTO Commande (id_client, total, adresse_livraison, mode_paiement) 
                      VALUES (:id_client, :total, :adresse, :paiement)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id_client", $_SESSION['user_id']);
            $stmt->bindParam(":total", $total);
            $stmt->bindParam(":adresse", $_POST['adresse']);
            $stmt->bindParam(":paiement", $_POST['paiement']);
            $stmt->execute();
            
            $orderId = $db->lastInsertId();

            // Create Order Items and Update Stock
            foreach ($cartItems as $item) {
                // Insert line item
                $query = "INSERT INTO Ligne_commande (id_cmd, id_pdt, quantite, prix_unitaire) 
                          VALUES (:id_cmd, :id_pdt, :quantite, :prix)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":id_cmd", $orderId);
                $stmt->bindParam(":id_pdt", $item['id_pdt']);
                $stmt->bindParam(":quantite", $item['cart_quantity']);
                $stmt->bindParam(":prix", $item['prix']);
                $stmt->execute();

                // Update stock
                $product->updateStock($item['id_pdt'], -$item['cart_quantity']);
            }

            $db->commit();
            $cart->clear();
            $success = "Votre commande a été validée avec succès !";
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Une erreur est survenue lors de la commande : " . $e->getMessage();
        }
    } else {
        $error = "Session invalide.";
    }
}

include 'includes/header.php';
?>

<div class="container">
    <?php if($success): ?>
        <div class="alert alert-success text-center">
            <h2><i class="fas fa-check-circle"></i> Merci !</h2>
            <p><?php echo $success; ?></p>
            <a href="index.php" class="btn btn-primary mt-3">Retour à l'accueil</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="checkout-form box">
                    <h2>Finaliser la commande</h2>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form action="checkout.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['nom']); ?>" class="form-control" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse">Adresse de livraison</label>
                            <textarea name="adresse" id="adresse" required class="form-control" rows="3"><?php echo htmlspecialchars($user['adresse']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="paiement">Mode de paiement</label>
                            <select name="paiement" id="paiement" class="form-control" required>
                                <option value="carte">Carte Bancaire</option>
                                <option value="paypal">PayPal</option>
                                <option value="virement">Virement Bancaire</option>
                                <option value="especes">Espèces à la livraison</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block btn-lg">Confirmer la commande (<?php echo number_format($total, 2); ?> €)</button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="order-summary box">
                    <h3>Récapitulatif</h3>
                    <ul class="summary-list">
                        <?php foreach ($cartItems as $item): ?>
                            <li>
                                <span><?php echo htmlspecialchars($item['nom_pdt']); ?> (x<?php echo $item['cart_quantity']; ?>)</span>
                                <span><?php echo number_format($item['subtotal'], 2); ?> €</span>
                            </li>
                        <?php endforeach; ?>
                        <li class="total">
                            <strong>Total</strong>
                            <strong><?php echo number_format($total, 2); ?> €</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.box {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.summary-list {
    list-style: none;
    margin-top: 1rem;
}

.summary-list li {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.summary-list li.total {
    border-top: 2px solid #ddd;
    border-bottom: none;
    margin-top: 1rem;
    padding-top: 1rem;
    font-size: 1.2rem;
}

.mt-3 {
    margin-top: 1rem;
}

.btn-lg {
    padding: 1rem;
    font-size: 1.2rem;
}
</style>

<?php include 'includes/footer.php'; ?>
