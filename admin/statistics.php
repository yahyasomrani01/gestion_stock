<?php
// admin/statistics.php
require_once '../config/database.php';
require_once '../includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Total Revenue
$query = "SELECT SUM(total) as revenue FROM Commande WHERE statut != 'annule'";
$stmt = $db->prepare($query);
$stmt->execute();
$revenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];

// Orders by Status
$query = "SELECT statut, COUNT(*) as count FROM Commande GROUP BY statut";
$stmt = $db->prepare($query);
$stmt->execute();
$ordersByStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Top Selling Products
$query = "SELECT p.nom_pdt, SUM(lc.quantite) as total_sold 
          FROM Ligne_commande lc 
          JOIN Produit p ON lc.id_pdt = p.id_pdt 
          JOIN Commande c ON lc.id_cmd = c.id_cmd 
          WHERE c.statut != 'annule'
          GROUP BY p.id_pdt 
          ORDER BY total_sold DESC 
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="admin-container">
    <div class="sidebar-admin">
        <h3>Menu Admin</h3>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Produits</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Commandes</a></li>
            <li><a href="statistics.php" class="active"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            <li><a href="../index.php"><i class="fas fa-home"></i> Retour au site</a></li>
        </ul>
    </div>

    <div class="main-admin">
        <h1>Statistiques</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Chiffre d'Affaires Total</h3>
                <p class="number text-success"><?php echo number_format($revenue, 2); ?> €</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6" style="width: 48%; display: inline-block; vertical-align: top;">
                <div class="section">
                    <h3>Commandes par Statut</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ordersByStatus as $stat): ?>
                                <tr>
                                    <td><?php echo ucfirst($stat['statut']); ?></td>
                                    <td><?php echo $stat['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-md-6" style="width: 48%; display: inline-block; vertical-align: top;">
                <div class="section">
                    <h3>Meilleures Ventes</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Ventes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProducts as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['nom_pdt']); ?></td>
                                    <td><?php echo $p['total_sold']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-success { color: #28a745; }
</style>

<?php include '../includes/footer.php'; ?>
