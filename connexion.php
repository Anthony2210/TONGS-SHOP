<?php
/**
 * Page de connexion pour les clients existants.
 * Permet à un utilisateur de se connecter en fournissant son adresse e-mail et son mot de passe.
 */

session_start();
require_once 'token.php';
include 'bandeau.php';

// Récupérer les messages d'erreur s'il y en a
$error_message = '';
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - TONGS SHOP</title>
    <link rel="stylesheet" href="styles/bandeau.css">
    <link rel="stylesheet" href="styles/pied_page.css">
    <link rel="stylesheet" href="styles/style.css">
    <script src="scripts/bandeau.js"></script>
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
</head>
<body>

<h1>Connexion à votre compte</h1>

<!-- Affichage du message d'erreur -->
<?php if (!empty($error_message)): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<!-- Formulaire de connexion -->
<form action="connecter.php" method="post">
    <fieldset>
        <legend>Identifiez-vous</legend>
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="form-group">
            <label for="mail">Adresse e-mail :</label>
            <input type="email" id="mail" name="mail" required>
        </div>
        <div class="form-group">
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>
        </div>
        <button type="submit">Se connecter</button>
    </fieldset>
</form>

<!-- Lien vers la création de compte -->
<p>Pas encore de compte ? <a class="lienpage" href="nouveau.php">Créer un compte</a></p>

<script src="scripts/bandeau.js"></script>
<?php
include 'pied_page.php';
?>

</body>
</html>
