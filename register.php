<?php
// register.php
require_once 'config/database.php';
require_once 'includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $adresse = $_POST['adresse'];
        $telephone = $_POST['telephone'];

        if ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            if ($auth->register($nom, $email, $password, $adresse, $telephone)) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Une erreur est survenue lors de l'inscription. L'email est peut-être déjà utilisé.";
            }
        }
    } else {
        $error = "Session invalide.";
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Inscription</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <br>
                <a href="login.php">Se connecter</a>
            </div>
        <?php else: ?>
        
        <form action="register.php" method="POST" id="registerForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
        </form>
        <?php endif; ?>
        
        <p class="auth-link">Déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
