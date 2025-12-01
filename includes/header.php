<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('SITE_NAME') ? SITE_NAME : 'Gestion Stock'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>index.php" class="navbar-brand">
                <i class="fas fa-store"></i> <?php echo defined('SITE_NAME') ? SITE_NAME : 'Gestion Stock'; ?>
            </a>
            
            <div class="navbar-nav">
                <a href="<?php echo BASE_URL; ?>index.php" class="nav-link">Accueil</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="nav-link">Admin</a>
                    <?php endif; ?>
                    
                    <a href="<?php echo BASE_URL; ?>cart.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i> Panier
                        <span class="badge" id="cart-count">0</span>
                    </a>
                    
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <div class="dropdown-menu">
                            <a href="<?php echo BASE_URL; ?>profile.php" class="dropdown-item">Mon Profil</a>
                            <a href="<?php echo BASE_URL; ?>logout.php" class="dropdown-item">Déconnexion</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login.php" class="nav-link">Connexion</a>
                    <a href="<?php echo BASE_URL; ?>register.php" class="nav-link btn btn-outline">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container main-content">
