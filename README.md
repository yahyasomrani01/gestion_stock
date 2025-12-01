# Gestion Stock Pro

Bienvenue dans **Gestion Stock Pro**, une application web complète de gestion de stock et de commerce électronique. Ce projet permet aux utilisateurs de parcourir des produits, de passer des commandes et aux administrateurs de gérer le catalogue et les stocks.

## � Technologies Utilisées

Ce projet a été construit avec des technologies web standards et robustes :

- **Backend :** PHP 7.4+ (Compatible PHP 8)
- **Base de données :** MySQL / MariaDB
- **Frontend :** HTML5, CSS3, JavaScript (Vanilla)
- **Serveur :** Apache (via XAMPP/WAMP/MAMP)

## �📋 Fonctionnalités

- **Interface Client :**
  - Navigation par catégories de produits.
  - Recherche de produits en temps réel.
  - Ajout au panier et gestion des commandes.
  - Inscription et connexion sécurisées.
  - Suivi de l'historique des commandes.

- **Interface Administrateur :**
  - Tableau de bord avec statistiques clés.
  - Gestion des produits (CRUD complet).
  - Suivi des stocks et alertes de niveau bas.
  - Gestion des commandes clients (changement de statut).

## 🛠️ Prérequis

Pour faire fonctionner ce projet localement, vous avez besoin de :

- **XAMPP** (ou tout autre environnement serveur local incluant Apache et MySQL).
- Un navigateur web moderne.

## 🚀 Installation

Suivez ces étapes pour installer et configurer le projet avec XAMPP :

1. **Téléchargement :**
    - Téléchargez ou clonez ce projet.
    - Placez le dossier du projet dans le répertoire `htdocs` de votre installation XAMPP (généralement `/Applications/XAMPP/xamppfiles/htdocs/` sur macOS ou `C:\xampp\htdocs\` sur Windows).
    - Renommez le dossier en `gestion_stock` si ce n'est pas déjà fait.

2. **Démarrage des Services :**
    - Ouvrez le panneau de contrôle XAMPP.
    - Démarrez les modules **Apache** et **MySQL**.

3. **Configuration de la Base de Données :**
    - Ouvrez votre navigateur et allez sur [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
    - Créez une nouvelle base de données nommée `gestion_stock`.
    - Cliquez sur l'onglet **Importer**.
    - Sélectionnez le fichier `database.sql` situé à la racine du projet.
    - Cliquez sur **Exécuter** pour importer la structure et les données initiales.

4. **Configuration de l'Application (Optionnel) :**
    - Si vous avez défini un mot de passe pour votre utilisateur root MySQL, ouvrez le fichier `config/database.php`.
    - Modifiez les paramètres de connexion si nécessaire :

      ```php
      private $username = "root";
      private $password = "votre_mot_de_passe";
      ```

## 💻 Utilisation

### Accès à l'application

Ouvrez votre navigateur et accédez à :
[http://localhost/gestion_stock/](http://localhost/gestion_stock/)

### Connexion Administrateur

Pour accéder au panneau d'administration et gérer le stock :

- **Email :** `admin@store.com`
- **Mot de passe :** `admin123`

### Connexion Client

Vous pouvez créer un nouveau compte client depuis la page d'inscription ou utiliser les fonctionnalités de base en tant qu'invité (selon la configuration).

## ❓ Dépannage

### Erreur HTTP 500

Si vous rencontrez une "Internal Server Error", vérifiez les logs d'erreur Apache. Assurez-vous que les chemins d'inclusion dans les fichiers PHP sont corrects (utilisation de `__DIR__`).

### Problème de connexion à la base de données

Vérifiez que le service MySQL est démarré dans XAMPP et que les identifiants dans `config/database.php` correspondent à votre configuration locale.

### Liens ou styles cassés

Assurez-vous que la constante `BASE_URL` dans `config/config.php` (ou définie dynamiquement) pointe bien vers la racine de votre projet (ex: `http://localhost/gestion_stock/`).

## 🏗️ Architecture du Projet

Ce projet suit une architecture modulaire simple basée sur PHP et MySQL :

- **`config/`** : Contient la configuration de la base de données (`database.php`) et les constantes globales.
- **`includes/`** : Contient les classes PHP (Modèles) comme `auth.php`, `product.php` et les éléments d'interface réutilisables (`header.php`, `footer.php`).
- **`admin/`** : Contient les fichiers spécifiques à l'interface d'administration.
- **`assets/`** : Stocke les fichiers CSS, JS et les images.
- **Racine** : Contient les contrôleurs principaux et les vues publiques (`index.php`, `login.php`, `cart.php`, etc.).

## 👤 Yahya Somrani

Ce projet a été conçu et développé pour offrir une solution simple et efficace de gestion de stock.

---
*Créé avec ❤️ pour la gestion efficace de votre commerce.*
