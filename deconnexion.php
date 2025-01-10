<?php
/**
 * deconnexion.php
 *
 * Gère la déconnexion des utilisateurs en détruisant la session.
 */

session_start();
require_once 'token.php';

/**
 * Déconnecte l'utilisateur en détruisant la session.
 *
 * @return void
 */
function logout() {
    // Vérifier si la requête est POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lire les données JSON de la requête
        $input = json_decode(file_get_contents('php://input'), true);

        // Valider le token CSRF
        if (!isset($input['csrf_token']) || !validate_csrf_token($input['csrf_token'])) {
            echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.']);
            exit();
        }

        // Détruire la session
        session_destroy();

        // Répondre avec succès
        echo json_encode(['success' => true, 'message' => 'Déconnexion réussie.']);
        exit();
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page d'accueil
        header('Location: index.php');
        exit();
    }
}

// Appeler la fonction de déconnexion
logout();
?>
