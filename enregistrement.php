<?php
/**
 * enregistrement.php
 *
 * Traite l'inscription des nouveaux clients en validant les données et en les enregistrant dans la base de données.
 */

session_start();
require_once 'bd.php';
require_once 'token.php';

/**
 * Enregistre un nouveau client dans la base de données.
 *
 * @return void
 */
function registerClient() {
    $bdd = getBD();
    header('Content-Type: application/json');

    // Activer l'affichage des erreurs pour le débogage (à enlever en production)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Vérifier la méthode de requête
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
        exit();
    }

    // Valider le token CSRF
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.']);
        exit();
    }

    // Récupérer et sécuriser les données du formulaire
    $nom = trim($_POST['n'] ?? '');
    $prenom = trim($_POST['p'] ?? '');
    $adresse = trim($_POST['adr'] ?? '');
    $numero = trim($_POST['num'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $mdp1 = $_POST['mdp1'] ?? '';
    $mdp2 = $_POST['mdp2'] ?? '';

    // Valider les champs
    if (empty($nom) || empty($prenom) || empty($adresse) || empty($numero) || empty($mail) || empty($mdp1) || empty($mdp2)) {
        echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs.']);
        exit();
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Adresse e-mail invalide.']);
        exit();
    }

    if ($mdp1 !== $mdp2) {
        echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas.']);
        exit();
    }

    // Vérifier si l'e-mail existe déjà
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM clients WHERE mail = ?");
    $stmt->execute([$mail]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Cette adresse e-mail est déjà utilisée.']);
        exit();
    }

    // Hash du mot de passe
    $hashed_password = password_hash($mdp1, PASSWORD_BCRYPT);

    // Insérer le nouveau client dans la base de données
    $stmt = $bdd->prepare("INSERT INTO clients (nom, prenom, adresse, numero, mail, mdp, date_inscription) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $success = $stmt->execute([$nom, $prenom, $adresse, $numero, $mail, $hashed_password]);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Inscription réussie ! Vous êtes maintenant connecté.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.']);
    }
}

// Appeler la fonction pour enregistrer le client
registerClient();
?>
