<?php
// admin/dashboard.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/product.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$product = new Product($db);

if (!$auth->isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$stats = $product->getStatistics();
$lowStock = $product->getLowStock();

// Get recent orders
$query = "SELECT c.*, u.nom as client_nom FROM Commande c 
          JOIN Utilisateur u ON c.id_client = u.id 
          ORDER BY c.date_cmd DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="admin-container">
    <div class="sidebar-admin">
        <h3>Menu Admin</h3>
        <ul>
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Commandes</a></li>
            <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            <li><a href="../index.php"><i class="fas fa-home"></i> Retour au site</a></li>
        </ul>
    </div>

    <div class="main-admin">
        <h1>Tableau de bord</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Produits Total</h3>
                <p class="number"><?php echo $stats['products']['total']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Stock Total</h3>
                <p class="number"><?php echo $stats['products']['total_stock']; ?></p>
            </div>
            <div class="stat-card alert-card">
                <h3>Stock Faible</h3>
                <p class="number"><?php echo $stats['low_stock']['low_stock']; ?></p>
            </div>
        </div>

        <?php if (!empty($lowStock)): ?>
            <div class="section">
                <h2><i class="fas fa-exclamation-triangle text-warning"></i> Alerte Stock Faible</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Stock Actuel</th>
                            <th>Seuil</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStock as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['nom_pdt']); ?></td>
                                <td class="text-danger font-weight-bold"><?php echo $p['quantite']; ?></td>
                                <td><?php echo $p['seuil_alerte']; ?></td>
                                <td><a href="products.php?action=edit&id=<?php echo $p['id_pdt']; ?>" class="btn btn-sm btn-primary">Réapprovisionner</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Commandes Récentes</h2>
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
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id_cmd']; ?></td>
                            <td><?php echo htmlspecialchars($order['client_nom']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['date_cmd'])); ?></td>
                            <td><?php echo number_format($order['total'], 2); ?> €</td>
                            <td><span class="badge badge-<?php echo getStatusColor($order['statut']); ?>"><?php echo ucfirst($order['statut']); ?></span></td>
                            <td><a href="orders.php?id=<?php echo $order['id_cmd']; ?>" class="btn btn-sm btn-secondary">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'en attente': return 'warning';
        case 'confirme': return 'info';
        case 'expedie': return 'primary';
        case 'livre': return 'success';
        case 'annule': return 'danger';
        default: return 'secondary';
    }
}
?>

<style>
.admin-container {
    display: flex;
    gap: 2rem;
}

.sidebar-admin {
    width: 250px;
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: fit-content;
}

.sidebar-admin ul {
    list-style: none;
    margin-top: 1rem;
}

.sidebar-admin li {
    margin-bottom: 0.5rem;
}

.sidebar-admin a {
    display: block;
    padding: 0.75rem;
    color: var(--dark-color);
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s;
}

.sidebar-admin a:hover, .sidebar-admin a.active {
    background-color: var(--primary-color);
    color: white;
}

.sidebar-admin i {
    width: 25px;
}

.main-admin {
    flex: 1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    font-size: 1rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.stat-card .number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.alert-card .number {
    color: var(--danger-color);
}

.section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.section h2 {
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 0.75rem;
    border-bottom: 1px solid #eee;
    text-align: left;
}

.badge-warning { background-color: #ffc107; color: #212529; }
.badge-info { background-color: #17a2b8; }
.badge-primary { background-color: #007bff; }
.badge-success { background-color: #28a745; }
.badge-danger { background-color: #dc3545; }
.badge-secondary { background-color: #6c757d; }

.text-warning { color: #ffc107; }
.text-danger { color: #dc3545; }
</style>

<?php include '../includes/footer.php'; ?>
