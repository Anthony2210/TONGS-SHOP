<?php
/**
 * nouveau.php
 *
 * Formulaire d'inscription pour les nouveaux clients et vérification AJAX des e-mails.
 */

session_start();
require_once 'token.php';
require_once 'bd.php';

/**
 * Vérifie si une adresse e-mail existe déjà dans la base de données.
 *
 * @param string $mail L'adresse e-mail à vérifier.
 * @return bool True si l'e-mail existe, false sinon.
 */
function checkEmailExists($mail) {
    $bdd = getBD();
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM clients WHERE mail = ?");
    $stmt->execute([$mail]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}

// Traitement AJAX pour la vérification du mail
if (isset($_POST['mail_check']) && isset($_POST['mail'])) {
    header('Content-Type: application/json');
    $mail = trim($_POST['mail']);

    // Vérifier si l'e-mail existe déjà
    $exists = checkEmailExists($mail);

    echo json_encode(['exists' => $exists]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Nouveau Client</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/bandeau.css">
    <link rel="stylesheet" href="styles/pied_page.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/bandeau.js"></script>
    <script src="scripts/nouveau.js"></script>

</head>
<body>

<?php include 'bandeau.php'; ?>

<?php
// Récupérer les données du formulaire stockées en session s'il y en a
$nom = isset($_SESSION['form_data']['n']) ? htmlspecialchars($_SESSION['form_data']['n']) : '';
$prenom = isset($_SESSION['form_data']['p']) ? htmlspecialchars($_SESSION['form_data']['p']) : '';
$adresse = isset($_SESSION['form_data']['adr']) ? htmlspecialchars($_SESSION['form_data']['adr']) : '';
$numero = isset($_SESSION['form_data']['num']) ? htmlspecialchars($_SESSION['form_data']['num']) : '';
$mail = isset($_SESSION['form_data']['mail']) ? htmlspecialchars($_SESSION['form_data']['mail']) : '';
?>

<h1>Formulaire d'inscription - Nouveau Client</h1>

<div id="message"></div>

<form id="registration-form">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

    <label for="n">Nom :</label>
    <input type="text" id="n" name="n" class="input-field" value="<?php echo $nom; ?>" required>
    <span class="error-message" id="n-error"></span>

    <label for="p">Prénom :</label>
    <input type="text" id="p" name="p" class="input-field" value="<?php echo $prenom; ?>" required>
    <span class="error-message" id="p-error"></span>

    <label for="adr">Adresse :</label>
    <input type="text" id="adr" name="adr" class="input-field" value="<?php echo $adresse; ?>" required>
    <span class="error-message" id="adr-error"></span>

    <label for="num">Numéro de téléphone :</label>
    <input type="tel" id="num" name="num" class="input-field" value="<?php echo $numero; ?>" required>
    <span class="error-message" id="num-error"></span>

    <label for="mail">Adresse e-mail :</label>
    <input type="email" id="mail" name="mail" class="input-field" value="<?php echo $mail; ?>" required>
    <span class="error-message" id="mail-error"></span>

    <label for="mdp1">Mot de passe :</label>
    <input type="password" id="mdp1" name="mdp1" class="input-field" required>
    <span class="error-message" id="mdp1-error"></span>

    <label for="mdp2">Confirmer votre mot de passe :</label>
    <input type="password" id="mdp2" name="mdp2" class="input-field" required>
    <span class="error-message" id="mdp2-error"></span>

    <br>
    <input type="submit" class="btn-inscrire" value="S'inscrire" disabled>
</form>

<?php include 'pied_page.php'; ?>

</body>
</html>
