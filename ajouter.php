<?php
/**
 * ajouter.php
 *
 * Ajoute un article au panier en session.
 */

session_start();
require_once 'bd.php';
require_once 'token.php';

$bdd = getBD();

/**
 * Fonction pour ajouter un article au panier en session.
 *
 * @param int $id_art L'ID de l'article.
 * @param int $quantite La quantité à ajouter.
 * @param string $nom Le nom de l'article.
 * @param float $prix Le prix de l'article.
 */
function ajouterAuPanier($id_art, $quantite, $nom, $prix) {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $item_found = false;
    foreach ($_SESSION['panier'] as &$article) {
        if ($article['id_art'] == $id_art) {
            $article['quantite'] += $quantite;
            $item_found = true;
            break;
        }
    }

    if (!$item_found) {
        $_SESSION['panier'][] = [
            'id_art' => $id_art,
            'quantite' => $quantite,
            'nom' => $nom,
            'prix' => $prix
        ];
    }
}

header('Content-Type: application/json');

// Vérification des données POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_art'], $_POST['quantite'], $_POST['csrf_token'])) {
    // Valider le token CSRF
    if (!validate_csrf_token($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.']);
        exit;
    }

    $id_art = filter_var($_POST['id_art'], FILTER_VALIDATE_INT);
    $quantite = filter_var($_POST['quantite'], FILTER_VALIDATE_INT);

    if ($id_art === false || $quantite === false || $quantite <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantité ou identifiant invalide.']);
        exit;
    }

    // Récupérer l'article et son stock
    $sql = $bdd->prepare("SELECT nom, prix, quantite FROM articles WHERE id_art = ?");
    $sql->execute([$id_art]);
    $article = $sql->fetch();

    if (!$article) {
        echo json_encode(['success' => false, 'message' => 'Article non trouvé.']);
        exit;
    }

    $stock = (int)$article['quantite'];

    // Récupérer le nombre total réservé pour cet article
    $sql_reserved = $bdd->prepare("SELECT SUM(quantite_reservee) AS total_reserved FROM reservations WHERE id_art = ?");
    $sql_reserved->execute([$id_art]);
    $result_reserved = $sql_reserved->fetch();
    $total_reserved = (int)$result_reserved['total_reserved'];

    // Calcul du stock disponible
    $available = $stock - $total_reserved;

    // Quantité déjà dans le panier pour cet utilisateur
    $quantite_deja_panier = 0;
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $item) {
            if ($item['id_art'] == $id_art) {
                $quantite_deja_panier = $item['quantite'];
                break;
            }
        }
    }

    // Quantité totale après ajout
    $quantite_totale_apres_ajout = $quantite_deja_panier + $quantite;

    if ($quantite_totale_apres_ajout > $available) {
        // Trop de réservations déjà faites par tous les utilisateurs
        echo json_encode(['success' => false, 'message' => 'Quantité demandée supérieure au stock disponible.']);
        exit;
    }

    // Récupérer l'id_client
    if (!isset($_SESSION['client'])) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter des articles au panier.']);
        exit;
    }

    $id_client = $_SESSION['client']['id'];

    // Vérifier si une réservation existe déjà pour cet utilisateur et cet article
    $sql_reservation = $bdd->prepare("SELECT quantite_reservee FROM reservations WHERE id_art = ? AND id_client = ?");
    $sql_reservation->execute([$id_art, $id_client]);
    $reservation = $sql_reservation->fetch();

    if ($reservation) {
        // Mettre à jour la réservation existante
        $new_reserve = $reservation['quantite_reservee'] + $quantite;
        $update_reservation = $bdd->prepare("UPDATE reservations SET quantite_reservee = ? WHERE id_art = ? AND id_client = ?");
        $update_reservation->execute([$new_reserve, $id_art, $id_client]);
    } else {
        // Créer une nouvelle réservation
        $insert_reservation = $bdd->prepare("INSERT INTO reservations (id_art, id_client, quantite_reservee) VALUES (?, ?, ?)");
        $insert_reservation->execute([$id_art, $id_client, $quantite]);
    }

    // Ajouter au panier en session
    ajouterAuPanier($id_art, $quantite, $article['nom'], $article['prix']);

    echo json_encode(['success' => true, 'message' => 'Article ajouté au panier.']);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide ou token CSRF manquant.']);
    exit;
}
?>
