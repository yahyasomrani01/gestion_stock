    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>À propos</h3>
                    <p>Votre boutique en ligne de confiance pour tous vos besoins technologiques.</p>
                </div>
                <div class="footer-section">
                    <h3>Liens rapides</h3>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>index.php">Accueil</a></li>
                        <li><a href="<?php echo BASE_URL; ?>cart.php">Panier</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p><i class="fas fa-envelope"></i> contact@gestionstock.com</p>
                    <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo defined('SITE_NAME') ? SITE_NAME : 'Gestion Stock'; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
    <?php if(basename($_SERVER['PHP_SELF']) == 'login.php' || basename($_SERVER['PHP_SELF']) == 'register.php'): ?>
        <script src="<?php echo BASE_URL; ?>assets/js/auth.js"></script>
    <?php endif; ?>
</body>
</html>
