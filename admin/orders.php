<?php
// admin/orders.php
require_once '../config/database.php';
require_once '../includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$success = '';
$error = '';

// Update Order Status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $query = "UPDATE Commande SET statut = :status WHERE id_cmd = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":status", $_POST['status']);
        $stmt->bindParam(":id", $_POST['order_id']);
        
        if ($stmt->execute()) {
            $success = "Statut de la commande mis à jour.";
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    }
}

// Get Orders
$query = "SELECT c.*, u.nom as client_nom, u.email as client_email 
          FROM Commande c 
          JOIN Utilisateur u ON c.id_client = u.id 
          ORDER BY c.date_cmd DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Order Details if ID is set
$orderDetails = null;
$orderItems = [];
if (isset($_GET['id'])) {
    $query = "SELECT c.*, u.nom as client_nom, u.email as client_email, u.telephone, u.adresse as client_adresse
              FROM Commande c 
              JOIN Utilisateur u ON c.id_client = u.id 
              WHERE c.id_cmd = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $_GET['id']);
    $stmt->execute();
    $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($orderDetails) {
        $query = "SELECT lc.*, p.nom_pdt 
                  FROM Ligne_commande lc 
                  JOIN Produit p ON lc.id_pdt = p.id_pdt 
                  WHERE lc.id_cmd = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $_GET['id']);
        $stmt->execute();
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

include '../includes/header.php';
?>

<div class="admin-container">
    <div class="sidebar-admin">
        <h3>Menu Admin</h3>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Commandes</a></li>
            <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            <li><a href="../index.php"><i class="fas fa-home"></i> Retour au site</a></li>
        </ul>
    </div>

    <div class="main-admin">
        <?php if ($orderDetails): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Détails Commande #<?php echo $orderDetails['id_cmd']; ?></h1>
                <a href="orders.php" class="btn btn-secondary">Retour</a>
            </div>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6" style="width: 48%; display: inline-block; vertical-align: top;">
                    <div class="section">
                        <h3>Info Client</h3>
                        <p><strong>Nom:</strong> <?php echo htmlspecialchars($orderDetails['client_nom']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['client_email']); ?></p>
                        <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($orderDetails['telephone']); ?></p>
                        <p><strong>Adresse de livraison:</strong><br><?php echo nl2br(htmlspecialchars($orderDetails['adresse_livraison'])); ?></p>
                    </div>
                </div>
                <div class="col-md-6" style="width: 48%; display: inline-block; vertical-align: top;">
                    <div class="section">
                        <h3>Info Commande</h3>
                        <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($orderDetails['date_cmd'])); ?></p>
                        <p><strong>Mode de paiement:</strong> <?php echo ucfirst($orderDetails['mode_paiement']); ?></p>
                        <p><strong>Total:</strong> <?php echo number_format($orderDetails['total'], 2); ?> €</p>
                        
                        <form action="orders.php?id=<?php echo $orderDetails['id_cmd']; ?>" method="POST" class="mt-3">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="order_id" value="<?php echo $orderDetails['id_cmd']; ?>">
                            <input type="hidden" name="update_status" value="1">
                            
                            <div class="form-group">
                                <label>Statut</label>
                                <div class="d-flex">
                                    <select name="status" class="form-control" style="margin-right: 10px;">
                                        <option value="en attente" <?php echo $orderDetails['statut'] == 'en attente' ? 'selected' : ''; ?>>En attente</option>
                                        <option value="confirme" <?php echo $orderDetails['statut'] == 'confirme' ? 'selected' : ''; ?>>Confirmé</option>
                                        <option value="expedie" <?php echo $orderDetails['statut'] == 'expedie' ? 'selected' : ''; ?>>Expédié</option>
                                        <option value="livre" <?php echo $orderDetails['statut'] == 'livre' ? 'selected' : ''; ?>>Livré</option>
                                        <option value="annule" <?php echo $orderDetails['statut'] == 'annule' ? 'selected' : ''; ?>>Annulé</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="section mt-3">
                <h3>Articles</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nom_pdt']); ?></td>
                                <td><?php echo $item['quantite']; ?></td>
                                <td><?php echo number_format($item['prix_unitaire'], 2); ?> €</td>
                                <td><?php echo number_format($item['quantite'] * $item['prix_unitaire'], 2); ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <h1>Gestion des Commandes</h1>
            
            <div class="section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id_cmd']; ?></td>
                                <td><?php echo htmlspecialchars($order['client_nom']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['date_cmd'])); ?></td>
                                <td><?php echo number_format($order['total'], 2); ?> €</td>
                                <td>
                                    <span class="badge badge-<?php 
                                        switch ($order['statut']) {
                                            case 'en attente': echo 'warning'; break;
                                            case 'confirme': echo 'info'; break;
                                            case 'expedie': echo 'primary'; break;
                                            case 'livre': echo 'success'; break;
                                            case 'annule': echo 'danger'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo ucfirst($order['statut']); ?>
                                    </span>
                                </td>
                                <td><a href="orders.php?id=<?php echo $order['id_cmd']; ?>" class="btn btn-sm btn-secondary">Voir</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-primary { background-color: #007bff; color: white; }
.badge-success { background-color: #28a745; color: white; }
.badge-danger { background-color: #dc3545; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
</style>

<?php include '../includes/footer.php'; ?>
