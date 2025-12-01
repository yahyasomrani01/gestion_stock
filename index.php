<?php
// index.php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product();

$filters = [];
if (isset($_GET['category'])) {
    $filters['categorie'] = $_GET['category'];
}
if (isset($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

$products = $product->getAll($filters);
$categories = $product->getCategories();

include 'includes/header.php';
?>

<div class="hero-section">
    <h1>Bienvenue sur <?php echo defined('SITE_NAME') ? SITE_NAME : 'Gestion Stock'; ?></h1>
    <p>Découvrez nos meilleurs produits aux meilleurs prix.</p>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="sidebar">
            <h3>Catégories</h3>
            <ul class="category-list">
                <li><a href="index.php" class="<?php echo !isset($_GET['category']) ? 'active' : ''; ?>">Toutes</a></li>
                <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="index.php?category=<?php echo urlencode($cat); ?>" 
                           class="<?php echo (isset($_GET['category']) && $_GET['category'] == $cat) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="search-bar">
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Rechercher un produit..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="product-grid">
            <?php if (empty($products)): ?>
                <p>Aucun produit trouvé.</p>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <div class="product-card">
                        <?php if ($p['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['nom_pdt']); ?>" class="product-image">
                        <?php else: ?>
                            <div class="no-image">Pas d'image</div>
                        <?php endif; ?>
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($p['nom_pdt']); ?></h3>
                            <p class="price"><?php echo number_format($p['prix'], 2); ?> €</p>
                            
                            <?php if ($p['quantite'] > 0): ?>
                                <button class="btn btn-primary add-to-cart" data-id="<?php echo $p['id_pdt']; ?>">
                                    Ajouter au panier
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Rupture de stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Additional styles for index.php */
.hero-section {
    background-color: var(--primary-color);
    color: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    text-align: center;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.col-md-3 {
    width: 25%;
    padding: 0 15px;
}

.col-md-9 {
    width: 75%;
    padding: 0 15px;
}

.sidebar {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.category-list {
    list-style: none;
    margin-top: 1rem;
}

.category-list li {
    margin-bottom: 0.5rem;
}

.category-list a {
    color: var(--dark-color);
    text-decoration: none;
    display: block;
    padding: 0.5rem;
    border-radius: 4px;
}

.category-list a:hover, .category-list a.active {
    background-color: var(--light-color);
    color: var(--primary-color);
    font-weight: bold;
}

.search-bar {
    margin-bottom: 2rem;
}

.search-form {
    display: flex;
    gap: 0.5rem;
}

.search-form input {
    flex: 1;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
}

.product-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 200px;
    background-color: #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888;
}

.product-info {
    padding: 1rem;
}

.product-info h3 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    height: 2.4em;
    overflow: hidden;
}

.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.product-info .btn {
    width: 100%;
}

@media (max-width: 768px) {
    .col-md-3, .col-md-9 {
        width: 100%;
    }
    .col-md-3 {
        margin-bottom: 2rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>