-- database.sql
-- Create the database and tables

CREATE DATABASE IF NOT EXISTS gestion_stock;
USE gestion_stock;

-- Users table
CREATE TABLE Utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_pass VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    adresse TEXT,
    telephone VARCHAR(20),
    statut TINYINT DEFAULT 1
);

-- Products table
CREATE TABLE Produit (
    id_pdt INT PRIMARY KEY AUTO_INCREMENT,
    nom_pdt VARCHAR(200) NOT NULL,
    description_pdt TEXT,
    prix DECIMAL(10,2) NOT NULL,
    quantite INT DEFAULT 0,
    categorie VARCHAR(100),
    seuil_alerte INT DEFAULT 5,
    image_url VARCHAR(255),
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif'
);

-- Orders table
CREATE TABLE Commande (
    id_cmd INT PRIMARY KEY AUTO_INCREMENT,
    id_client INT NOT NULL,
    date_cmd TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    statut ENUM('en attente', 'confirme', 'expedie', 'livre', 'annule') DEFAULT 'en attente',
    adresse_livraison TEXT,
    mode_paiement VARCHAR(50),
    FOREIGN KEY (id_client) REFERENCES Utilisateur(id)
);

-- Order items table
CREATE TABLE Ligne_commande (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cmd INT NOT NULL,
    id_pdt INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_cmd) REFERENCES Commande(id_cmd),
    FOREIGN KEY (id_pdt) REFERENCES Produit(id_pdt)
);

-- Suppliers table (optional enhancement)
CREATE TABLE Fournisseur (
    id_fournisseur INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telephone VARCHAR(20),
    adresse TEXT
);

-- Product alerts table
CREATE TABLE Alerte_stock (
    id_alerte INT PRIMARY KEY AUTO_INCREMENT,
    id_pdt INT NOT NULL,
    quantite_actuelle INT,
    seuil INT,
    date_alerte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    traitee BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_pdt) REFERENCES Produit(id_pdt)
);

-- Insert default admin user (password: admin123)
INSERT INTO Utilisateur (nom, email, mot_de_pass, role) 
VALUES ('Administrateur', 'admin@store.com', '$2y$10$YourHashedPasswordHere', 'admin');

-- Insert sample products
INSERT INTO Produit (nom_pdt, description_pdt, prix, quantite, categorie, seuil_alerte) VALUES
('Ordinateur Portable', 'PC portable 15.6 pouces, 8GB RAM, 512GB SSD', 899.99, 15, 'Informatique', 3),
('Smartphone', 'Smartphone 128GB, 6.5 pouces, double caméra', 499.99, 25, 'Téléphonie', 5),
('Casque Audio', 'Casque sans fil Bluetooth, réduction de bruit', 129.99, 8, 'Audio', 2),
('Souris Gaming', 'Souris RGB 6 boutons, 16000 DPI', 59.99, 30, 'Informatique', 10);