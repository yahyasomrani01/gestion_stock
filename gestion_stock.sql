-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 01, 2025 at 09:02 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_stock`
--

-- --------------------------------------------------------

--
-- Table structure for table `Alerte_stock`
--

CREATE TABLE `Alerte_stock` (
  `id_alerte` int(11) NOT NULL,
  `id_pdt` int(11) NOT NULL,
  `quantite_actuelle` int(11) DEFAULT NULL,
  `seuil` int(11) DEFAULT NULL,
  `date_alerte` timestamp NOT NULL DEFAULT current_timestamp(),
  `traitee` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Commande`
--

CREATE TABLE `Commande` (
  `id_cmd` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `date_cmd` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `statut` enum('en attente','confirme','expedie','livre','annule') DEFAULT 'en attente',
  `adresse_livraison` text DEFAULT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Fournisseur`
--

CREATE TABLE `Fournisseur` (
  `id_fournisseur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Ligne_commande`
--

CREATE TABLE `Ligne_commande` (
  `id` int(11) NOT NULL,
  `id_cmd` int(11) NOT NULL,
  `id_pdt` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Produit`
--

CREATE TABLE `Produit` (
  `id_pdt` int(11) NOT NULL,
  `nom_pdt` varchar(200) NOT NULL,
  `description_pdt` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `quantite` int(11) DEFAULT 0,
  `categorie` varchar(100) DEFAULT NULL,
  `seuil_alerte` int(11) DEFAULT 5,
  `image_url` varchar(255) DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` enum('actif','inactif') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Produit`
--

INSERT INTO `Produit` (`id_pdt`, `nom_pdt`, `description_pdt`, `prix`, `quantite`, `categorie`, `seuil_alerte`, `image_url`, `date_ajout`, `statut`) VALUES
(1, 'Ordinateur Portable', 'PC portable 15.6 pouces, 8GB RAM, 512GB SSD', 899.99, 15, 'Informatique', 3, 'https://www.tunisianet.com.tn/452522-large/pc-portable-hp-15-fd0421nk-n100-4-go-256-go-ssd-noir.jpg', '2025-12-01 19:27:47', 'actif'),
(2, 'Smartphone', 'Smartphone 128GB, 6.5 pouces, double caméra', 499.99, 25, 'Téléphonie', 5, 'https://www.tunisianet.com.tn/133271-large/film-de-protection-nano-glass-9h-pour-tecno-pop-2-power.jpg', '2025-12-01 19:27:47', 'actif'),
(3, 'Casque Audio', 'Casque sans fil Bluetooth, réduction de bruit', 129.99, 8, 'Audio', 2, 'https://www.tunisianet.com.tn/394416-large/casque-micro-gaming-spirit-of-gamer-pro-h6-artic-rgb-blanc.jpg', '2025-12-01 19:27:47', 'actif'),
(4, 'Souris Gaming', 'Souris RGB 6 boutons, 16000 DPI', 59.99, 30, 'Informatique', 10, 'https://www.tunisianet.com.tn/441069-large/souris-6d-gaming-rgb-led-light-r8-1633-3-600-dpi-black.jpg', '2025-12-01 19:27:47', 'actif');

-- --------------------------------------------------------

--
-- Table structure for table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_pass` varchar(255) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `statut` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Utilisateur`
--

INSERT INTO `Utilisateur` (`id`, `nom`, `email`, `mot_de_pass`, `role`, `date_inscription`, `adresse`, `telephone`, `statut`) VALUES
(1, 'Administrateur', 'admin@store.com', '$2y$10$LBfS/L5hVmYUTVMSj.VV8OEiHyiCvma5036ezw7VNnnQDbzjsO.Ie', 'admin', '2025-12-01 19:27:47', NULL, NULL, 1),
(2, 'Yahya Somrani', 'yahyasomrani999@gamil.com', '$2y$12$VlqeNp2NTK4GqpxwWFmwqeTN6J/7lYamD0cx6G9ghVye7/xbk346q', 'client', '2025-12-01 19:31:27', 'Bou Salem', '21901929', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Alerte_stock`
--
ALTER TABLE `Alerte_stock`
  ADD PRIMARY KEY (`id_alerte`),
  ADD KEY `id_pdt` (`id_pdt`);

--
-- Indexes for table `Commande`
--
ALTER TABLE `Commande`
  ADD PRIMARY KEY (`id_cmd`),
  ADD KEY `id_client` (`id_client`);

--
-- Indexes for table `Fournisseur`
--
ALTER TABLE `Fournisseur`
  ADD PRIMARY KEY (`id_fournisseur`);

--
-- Indexes for table `Ligne_commande`
--
ALTER TABLE `Ligne_commande`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cmd` (`id_cmd`),
  ADD KEY `id_pdt` (`id_pdt`);

--
-- Indexes for table `Produit`
--
ALTER TABLE `Produit`
  ADD PRIMARY KEY (`id_pdt`);

--
-- Indexes for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Alerte_stock`
--
ALTER TABLE `Alerte_stock`
  MODIFY `id_alerte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Commande`
--
ALTER TABLE `Commande`
  MODIFY `id_cmd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Fournisseur`
--
ALTER TABLE `Fournisseur`
  MODIFY `id_fournisseur` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ligne_commande`
--
ALTER TABLE `Ligne_commande`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Produit`
--
ALTER TABLE `Produit`
  MODIFY `id_pdt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Alerte_stock`
--
ALTER TABLE `Alerte_stock`
  ADD CONSTRAINT `alerte_stock_ibfk_1` FOREIGN KEY (`id_pdt`) REFERENCES `Produit` (`id_pdt`);

--
-- Constraints for table `Commande`
--
ALTER TABLE `Commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `Utilisateur` (`id`);

--
-- Constraints for table `Ligne_commande`
--
ALTER TABLE `Ligne_commande`
  ADD CONSTRAINT `ligne_commande_ibfk_1` FOREIGN KEY (`id_cmd`) REFERENCES `Commande` (`id_cmd`),
  ADD CONSTRAINT `ligne_commande_ibfk_2` FOREIGN KEY (`id_pdt`) REFERENCES `Produit` (`id_pdt`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
