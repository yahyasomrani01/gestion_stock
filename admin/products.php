<?php
// admin/products.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/product.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$product = new Product();

if (!$auth->isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $data = [
            'nom' => $_POST['nom'],
            'description' => $_POST['description'],
            'prix' => $_POST['prix'],
            'quantite' => $_POST['quantite'],
            'categorie' => $_POST['categorie'],
            'seuil' => $_POST['seuil'],
            'image' => $_POST['image_url']
        ];

        if (isset($_POST['id_pdt']) && !empty($_POST['id_pdt'])) {
            // Update
            if ($product->update($_POST['id_pdt'], $data)) {
                $success = "Produit mis à jour avec succès.";
                $action = 'list';
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        } else {
            // Create
            if ($product->create($data)) {
                $success = "Produit créé avec succès.";
                $action = 'list';
            } else {
                $error = "Erreur lors de la création.";
            }
        }
    }
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    if ($product->delete($_GET['id'])) {
        $success = "Produit supprimé.";
    } else {
        $error = "Erreur lors de la suppression.";
    }
    $action = 'list';
}

include '../includes/header.php';
?>

<div class="admin-container">
    <div class="sidebar-admin">
        <h3>Menu Admin</h3>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="products.php" class="active"><i class="fas fa-box"></i> Produits</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Commandes</a></li>
            <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            <li><a href="../index.php"><i class="fas fa-home"></i> Retour au site</a></li>
        </ul>
    </div>

    <div class="main-admin">
        <?php if ($action == 'list'): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Gestion des Produits</h1>
                <a href="products.php?action=add" class="btn btn-success"><i class="fas fa-plus"></i> Nouveau Produit</a>
            </div>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $products = $product->getAll();
                        foreach ($products as $p): 
                        ?>
                            <tr>
                                <td><?php echo $p['id_pdt']; ?></td>
                                <td>
                                    <?php if($p['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['nom_pdt']); ?></td>
                                <td><?php echo number_format($p['prix'], 2); ?> €</td>
                                <td>
                                    <span class="<?php echo $p['quantite'] <= $p['seuil_alerte'] ? 'text-danger font-weight-bold' : ''; ?>">
                                        <?php echo $p['quantite']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($p['categorie']); ?></td>
                                <td>
                                    <a href="products.php?action=edit&id=<?php echo $p['id_pdt']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <a href="products.php?action=delete&id=<?php echo $p['id_pdt']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr ?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($action == 'add' || $action == 'edit'): ?>
            <?php
            $p = null;
            if ($action == 'edit' && isset($_GET['id'])) {
                $p = $product->getById($_GET['id']);
            }
            ?>
            
            <h1><?php echo $action == 'add' ? 'Nouveau Produit' : 'Modifier Produit'; ?></h1>
            
            <div class="section">
                <form action="products.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <?php if($p): ?>
                        <input type="hidden" name="id_pdt" value="<?php echo $p['id_pdt']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Nom du produit</label>
                        <input type="text" name="nom" class="form-control" required value="<?php echo $p ? htmlspecialchars($p['nom_pdt']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $p ? htmlspecialchars($p['description_pdt']) : ''; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6" style="width: 48%; display: inline-block;">
                            <div class="form-group">
                                <label>Prix</label>
                                <input type="number" step="0.01" name="prix" class="form-control" required value="<?php echo $p ? $p['prix'] : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6" style="width: 48%; display: inline-block;">
                            <div class="form-group">
                                <label>Quantité</label>
                                <input type="number" name="quantite" class="form-control" required value="<?php echo $p ? $p['quantite'] : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" style="width: 48%; display: inline-block;">
                            <div class="form-group">
                                <label>Catégorie</label>
                                <input type="text" name="categorie" class="form-control" required value="<?php echo $p ? htmlspecialchars($p['categorie']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6" style="width: 48%; display: inline-block;">
                            <div class="form-group">
                                <label>Seuil d'alerte</label>
                                <input type="number" name="seuil" class="form-control" value="<?php echo $p ? $p['seuil_alerte'] : '5'; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>URL Image</label>
                        <input type="text" name="image_url" class="form-control" value="<?php echo $p ? htmlspecialchars($p['image_url']) : ''; ?>">
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                        <a href="products.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.d-flex { display: flex; }
.justify-content-between { justify-content: space-between; }
.align-items-center { align-items: center; }
.mb-3 { margin-bottom: 1rem; }
</style>

<?php include '../includes/footer.php'; ?>
