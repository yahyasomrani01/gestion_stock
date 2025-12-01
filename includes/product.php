<?php
// includes/product.php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;
    private $table = 'Produit';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create product
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  SET nom_pdt = :nom, description_pdt = :description, 
                      prix = :prix, quantite = :quantite, 
                      categorie = :categorie, seuil_alerte = :seuil, 
                      image_url = :image";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nom", $data['nom']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":prix", $data['prix']);
        $stmt->bindParam(":quantite", $data['quantite']);
        $stmt->bindParam(":categorie", $data['categorie']);
        $stmt->bindParam(":seuil", $data['seuil']);
        $stmt->bindParam(":image", $data['image']);

        if ($stmt->execute()) {
            $this->checkStockAlert($this->conn->lastInsertId(), $data['quantite'], $data['seuil']);
            return true;
        }
        return false;
    }

    // Get all products
    public function getAll($filters = []) {
        $query = "SELECT * FROM " . $this->table . " WHERE statut = 'actif'";
        
        if (!empty($filters['categorie'])) {
            $query .= " AND categorie = :categorie";
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (nom_pdt LIKE :search OR description_pdt LIKE :search)";
        }
        
        $query .= " ORDER BY date_ajout DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filters['categorie'])) {
            $stmt->bindParam(":categorie", $filters['categorie']);
        }
        
        if (!empty($filters['search'])) {
            $searchTerm = "%" . $filters['search'] . "%";
            $stmt->bindParam(":search", $searchTerm);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get product by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_pdt = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update product
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET nom_pdt = :nom, description_pdt = :description, 
                      prix = :prix, quantite = :quantite, 
                      categorie = :categorie, seuil_alerte = :seuil, 
                      image_url = :image 
                  WHERE id_pdt = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nom", $data['nom']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":prix", $data['prix']);
        $stmt->bindParam(":quantite", $data['quantite']);
        $stmt->bindParam(":categorie", $data['categorie']);
        $stmt->bindParam(":seuil", $data['seuil']);
        $stmt->bindParam(":image", $data['image']);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            $this->checkStockAlert($id, $data['quantite'], $data['seuil']);
            return true;
        }
        return false;
    }

    // Delete product (soft delete)
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET statut = 'inactif' WHERE id_pdt = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Update stock quantity
    public function updateStock($productId, $quantityChange) {
        $query = "UPDATE " . $this->table . " 
                  SET quantite = quantite + :change 
                  WHERE id_pdt = :id AND quantite + :change >= 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":change", $quantityChange, PDO::PARAM_INT);
        $stmt->bindParam(":id", $productId);
        
        if ($stmt->execute()) {
            // Get current quantity for alert check
            $product = $this->getById($productId);
            $this->checkStockAlert($productId, $product['quantite'], $product['seuil_alerte']);
            return true;
        }
        return false;
    }

    // Check stock and create alerts
    private function checkStockAlert($productId, $currentQty, $threshold) {
        if ($currentQty <= $threshold) {
            $query = "INSERT INTO Alerte_stock (id_pdt, quantite_actuelle, seuil) 
                      VALUES (:id_pdt, :quantite, :seuil)
                      ON DUPLICATE KEY UPDATE 
                      quantite_actuelle = :quantite, date_alerte = NOW(), traitee = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_pdt", $productId);
            $stmt->bindParam(":quantite", $currentQty);
            $stmt->bindParam(":seuil", $threshold);
            $stmt->execute();
        }
    }

    // Get low stock products
    public function getLowStock() {
        $query = "SELECT p.* FROM " . $this->table . " p
                  WHERE p.quantite <= p.seuil_alerte 
                  AND p.statut = 'actif'
                  ORDER BY p.quantite ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get categories
    public function getCategories() {
        $query = "SELECT DISTINCT categorie FROM " . $this->table . " 
                  WHERE categorie IS NOT NULL AND statut = 'actif'
                  ORDER BY categorie";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // Get statistics
    public function getStatistics() {
        $stats = [];
        
        // Total products
        $query = "SELECT COUNT(*) as total, SUM(quantite) as total_stock FROM " . $this->table . " WHERE statut = 'actif'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['products'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Low stock count
        $query = "SELECT COUNT(*) as low_stock FROM " . $this->table . " 
                  WHERE quantite <= seuil_alerte AND statut = 'actif'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['low_stock'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Products by category
        $query = "SELECT categorie, COUNT(*) as count FROM " . $this->table . " 
                  WHERE statut = 'actif' AND categorie IS NOT NULL
                  GROUP BY categorie";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
}
?>