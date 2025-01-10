<?php
/**
 * bd.php
 *
 * Contient la fonction pour établir une connexion à la base de données.
 */

/**
 * Établit une connexion à la base de données en utilisant PDO.
 *
 * @return PDO Instance de la connexion PDO.
 * @throws PDOException Si la connexion échoue.
 */
function getBD() {
    try {
        // Connexion à la base de données avec PDO
        $bdd = new PDO('mysql:host=localhost;dbname=tongsshop;charset=utf8', 'root', '');

        // Définir le mode d'erreur de PDO sur Exception
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $bdd;
    } catch (PDOException $e) {
        // Arrêter le script et afficher l'erreur en cas d'échec de connexion
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }
}
?>
