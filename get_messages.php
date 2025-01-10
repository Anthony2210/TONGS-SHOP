<?php
/**
 * get_messages.php
 *
 * Récupère les messages du chat depuis la base de données et supprime les messages anciens.
 */

session_start();
require_once 'bd.php';

/**
 * Supprime les messages plus anciens que 10 minutes.
 *
 * @param PDO $bdd Instance de la base de données.
 * @return void
 */
function deleteOldMessages($bdd) {
    $sql = "DELETE FROM messages WHERE date_envoi < (NOW() - INTERVAL 10 MINUTE)";
    $stmt = $bdd->prepare($sql);
    $stmt->execute();
}

/**
 * Récupère tous les messages du chat.
 *
 * @return void
 */
function getMessages() {
    $bdd = getBD();

    // Supprimer les messages anciens
    deleteOldMessages($bdd);

    header('Content-Type: application/json');

    // Récupérer tous les messages avec les informations de l'utilisateur
    $sql = "SELECT 
                m.message, 
                m.date_envoi, 
                c.nom, 
                c.prenom AS prenom_user, 
                CONCAT(c.prenom, ' ', c.nom) AS nom_user, 
                c.mail 
            FROM messages m 
            JOIN clients c ON m.id_user = c.id_client 
            ORDER BY m.date_envoi ASC";
    $stmt = $bdd->prepare($sql);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
}

// Appeler la fonction pour récupérer les messages
getMessages();
?>
