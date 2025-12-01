<?php
// includes/auth.php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $conn;
    private $table_name = "Utilisateur";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register($nom, $email, $password, $adresse, $telephone) {
        $query = "INSERT INTO " . $this->table_name . "
                SET nom = :nom,
                    email = :email,
                    mot_de_pass = :password,
                    adresse = :adresse,
                    telephone = :telephone,
                    role = 'client'";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $nom = sanitize($nom);
        $email = sanitize($email);
        $adresse = sanitize($adresse);
        $telephone = sanitize($telephone);

        // Hash password
        $password_hash = hashPassword($password);

        // Bind values
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":adresse", $adresse);
        $stmt->bindParam(":telephone", $telephone);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login user
    public function login($email, $password) {
        $query = "SELECT id, nom, email, mot_de_pass, role FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $email = sanitize($email);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(verifyPassword($password, $row['mot_de_pass'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['nom'];
                $_SESSION['user_role'] = $row['role'];
                return true;
            }
        }
        return false;
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Check if user is admin
    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    // Logout
    public function logout() {
        session_destroy();
        return true;
    }
    
    // Get current user details
    public function getUser($id) {
        $query = "SELECT id, nom, email, adresse, telephone, role FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>