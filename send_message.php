<?php
/**
 * send_message.php
 *
 * Traite l'envoi de messages dans le chat.
 */

session_start();
require_once 'token.php';
require_once 'bd.php';

/**
 * Envoie un message dans le chat pour l'utilisateur connecté.
 *
 * @return void
 */
function sendMessage() {
    $bdd = getBD();
    header('Content-Type: application/json');

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['client'])) {
        echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
        exit();
    }

    // Récupérer les données POST
    $message = trim($_POST['message'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Valider le token CSRF
    if (!validate_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'error' => 'Token CSRF invalide']);
        exit();
    }

    // Valider le contenu du message
    if (strlen($message) === 0 || strlen($message) > 256) {
        echo json_encode(['success' => false, 'error' => 'Message invalide']);
        exit();
    }

    $id_user = $_SESSION['client']['id'];

    // Préparer et exécuter la requête d'insertion du message
    $sql = "INSERT INTO messages (id_user, message, date_envoi) VALUES (?, ?, NOW())";
    $stmt = $bdd->prepare($sql);
    $success = $stmt->execute([$id_user, $message]);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur interne lors de l\'insertion du message']);
    }
}

// Appeler la fonction pour traiter l'envoi du message
sendMessage();
?>
