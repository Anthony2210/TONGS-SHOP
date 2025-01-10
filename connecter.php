<?php
/**
 * Page de traitement de la connexion d'un client.
 * Vérifie les informations fournies et initialise la session si les identifiants sont corrects.
 */

session_start();
require_once 'token.php';
include 'bandeau.php';

require_once 'bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider le token CSRF
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "Token CSRF invalide.";
        header('Location: connexion.php');
        exit;
    }

    $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
    $mdp = isset($_POST['mdp']) ? trim($_POST['mdp']) : '';

    // Connexion à la base de données
    $bdd = getBD();

    // Préparation de la requête
    $sql = "SELECT * FROM clients WHERE mail = :mail";
    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
    $stmt->execute();

    // Vérification si le client existe
    if ($stmt->rowCount() > 0) {
        $client = $stmt->fetch();

        // Vérifier le mot de passe
        if (password_verify($mdp, $client['mdp'])) {
            // Stocker les informations du client dans la session
            $_SESSION['client'] = array(
                'id' => $client['id_client'],
                'nom' => $client['nom'],
                'prenom' => $client['prenom'],
                'mail' => $client['mail'],
                'adresse' => $client['adresse']
            );

            // Redirection vers la page d'accueil
            header('Location: index.php');
            exit;
        } else {
            // Mot de passe incorrect
            $_SESSION['error'] = "Mot de passe incorrect.";
            header('Location: connexion.php');
            exit;
        }
    } else {
        // E-mail non trouvé
        $_SESSION['error'] = "Aucun compte trouvé avec cette adresse e-mail.";
        header('Location: connexion.php');
        exit;
    }
} else {
    // Rediriger si l'accès n'est pas via POST
    header('Location: connexion.php');
    exit;
}
?>
