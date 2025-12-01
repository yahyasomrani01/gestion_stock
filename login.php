<?php
// login.php
require_once 'config/database.php';
require_once 'includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($auth->login($email, $password)) {
            if ($auth->isAdmin()) {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Session invalide, veuillez réessayer.";
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Connexion</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>
        
        <p class="auth-link">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>