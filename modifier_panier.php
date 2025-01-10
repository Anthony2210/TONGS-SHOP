<?php
// modifier_panier.php

session_start();
require_once 'bd.php';
require_once 'token.php';

$bdd = getBD();

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour modifier le panier.']);
    exit;
}

// Lire l'entrée JSON
$input = json_decode(file_get_contents('php://input'), true);

// Vérifier les paramètres
if (!isset($input['id_art'], $input['quantite'], $input['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
    exit;
}

// Vérifier le token CSRF
if (!validate_csrf_token($input['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.']);
    exit;
}

$id_art_modifier = filter_var($input['id_art'], FILTER_VALIDATE_INT);
$nouvelle_quantite = filter_var($input['quantite'], FILTER_VALIDATE_INT);

// Validation des données
if ($id_art_modifier === false || $nouvelle_quantite === false || $nouvelle_quantite < 0) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

$panier = &$_SESSION['panier'];

// Vérifier si l'article existe dans le panier
$ligne_index = null;
foreach ($panier as $key => $article) {
    if ($article['id_art'] == $id_art_modifier) {
        $ligne_index = $key;
        break;
    }
}

if ($ligne_index === null) {
    echo json_encode(['success' => false, 'message' => 'Article non présent dans le panier.']);
    exit;
}

// Récupérer l'article dans la BDD
$sql = $bdd->prepare("SELECT quantite, prix FROM articles WHERE id_art = ?");
$sql->execute([$id_art_modifier]);
$article_db = $sql->fetch();

if (!$article_db) {
    echo json_encode(['success' => false, 'message' => 'Article introuvable.']);
    exit;
}

$stock_total = (int)$article_db['quantite'];
$prix_unitaire = (float)$article_db['prix'];

// Récupérer les réservations actuelles pour cet article
$sql_total_reserved = $bdd->prepare("SELECT SUM(quantite_reservee) AS total_reserved FROM reservations WHERE id_art = ?");
$sql_total_reserved->execute([$id_art_modifier]);
$result_total_reserved = $sql_total_reserved->fetch();
$total_reserved = $result_total_reserved['total_reserved'] ? (int)$result_total_reserved['total_reserved'] : 0;

// Récupérer la réservation actuelle de l'utilisateur pour cet article
$id_client = $_SESSION['client']['id'];
$sql_reservation = $bdd->prepare("SELECT quantite_reservee FROM reservations WHERE id_art = ? AND id_client = ?");
$sql_reservation->execute([$id_art_modifier, $id_client]);
$reservation = $sql_reservation->fetch();
$quantite_reservee = $reservation ? (int)$reservation['quantite_reservee'] : 0;

// Calculer la quantité disponible pour cet utilisateur
$quantite_disponible = $stock_total - $total_reserved + $quantite_reservee;

// Vérifier que la nouvelle quantité ne dépasse pas la disponibilité
if ($nouvelle_quantite > $quantite_disponible) {
    echo json_encode(['success' => false, 'message' => 'Quantité demandée supérieure au stock disponible.']);
    exit;
}

// Calculer la différence entre la nouvelle quantité et l'ancienne quantité
$ancienne_quantite = $panier[$ligne_index]['quantite'];
$difference = $nouvelle_quantite - $ancienne_quantite;

// Mise à jour des réservations
if ($difference > 0) {
    // Augmenter la réservation
    if ($quantite_reservee > 0) {
        $new_reserve = $quantite_reservee + $difference;
        $update_reservation = $bdd->prepare("UPDATE reservations SET quantite_reservee = ? WHERE id_art = ? AND id_client = ?");
        $update_reservation->execute([$new_reserve, $id_art_modifier, $id_client]);
    } else {
        // Créer une nouvelle réservation
        $insert_reservation = $bdd->prepare("INSERT INTO reservations (id_art, id_client, quantite_reservee) VALUES (?, ?, ?)");
        $insert_reservation->execute([$id_art_modifier, $id_client, $difference]);
    }
} elseif ($difference < 0) {
    // Diminuer la réservation
    $difference = abs($difference);
    if ($quantite_reservee >= $difference) {
        $new_reserve = $quantite_reservee - $difference;
        if ($new_reserve > 0) {
            $update_reservation = $bdd->prepare("UPDATE reservations SET quantite_reservee = ? WHERE id_art = ? AND id_client = ?");
            $update_reservation->execute([$new_reserve, $id_art_modifier, $id_client]);
        } else {
            // Supprimer la réservation si elle atteint 0
            $delete_reservation = $bdd->prepare("DELETE FROM reservations WHERE id_art = ? AND id_client = ?");
            $delete_reservation->execute([$id_art_modifier, $id_client]);
        }
    } else {
        // Erreur de réservation
        echo json_encode(['success' => false, 'message' => 'Erreur de réservation.']);
        exit;
    }
}

// Mettre à jour le panier
if ($nouvelle_quantite == 0) {
    // Retirer l'article du panier
    unset($panier[$ligne_index]);
    $panier = array_values($panier); // Réindexer le panier
} else {
    // Mettre à jour la quantité
    $panier[$ligne_index]['quantite'] = $nouvelle_quantite;
}

// Calculer le nouveau total de la commande
$total_commande = 0;
foreach ($panier as $item) {
    // Récupérer le prix unitaire depuis la BDD
    $sql_price = $bdd->prepare("SELECT prix FROM articles WHERE id_art = ?");
    $sql_price->execute([$item['id_art']]);
    $item_price = $sql_price->fetchColumn();
    $prix_unitaire_item = $item_price ? (float)$item_price : 0;
    $total_commande += $item['quantite'] * $prix_unitaire_item;
}

// Mettre à jour le total_commande avec deux décimales
$total_commande = number_format($total_commande, 2, '.', '');

// Renvoyer la réponse JSON
echo json_encode([
    'success' => true,
    'message' => 'Panier mis à jour avec succès.',
    'prix_unitaire' => number_format($prix_unitaire, 2, '.', ''),
    'total_commande' => $total_commande
]);
exit;
?>
