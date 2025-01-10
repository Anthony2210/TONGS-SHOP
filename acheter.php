<?php
/**
 * acheter.php
 *
 * Page de validation de la commande après paiement.
 * Insère une nouvelle commande dans la table commandes, puis insère les articles dans la table commande_items,
 * met à jour les stocks (quantite) et vide le panier.
 * Mise à jour ID_STRIPE du client après paiement.
 */

session_start();
require_once 'token.php';

// Vérifier si le client est connecté
if (!isset($_SESSION['client'])) {
    header('Location: connexion.php');
    exit();
}

require_once 'bd.php';
$bdd = getBD();

require_once 'stripe.php'; // Fichier contenant la config Stripe et l'objet $stripe

// Récupérer le session_id du Checkout Stripe (transmis dans success_url)
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;
$client = isset($_SESSION['client']) ? $_SESSION['client'] : null;

// Traitement de la session Stripe et mise à jour de l'ID_STRIPE si nécessaire
if ($session_id) {
    try {
        $checkout_session = $stripe->checkout->sessions->retrieve($session_id, []);
        $payment_status = $checkout_session->payment_status;
        $customer_id = $checkout_session->customer; // L'ID du customer Stripe

        // Vérifier si le paiement a été effectué avec succès
        if ($payment_status != 'paid') {
            throw new Exception("Le paiement n'a pas été effectué avec succès.");
        }

        // Si un customer a été créé et que le client n'a pas d'ID_STRIPE, on le met à jour
        if (!empty($customer_id)) {
            $id_client = $client['id'];
            $stmt = $bdd->prepare("SELECT ID_STRIPE FROM clients WHERE id_client = ?");
            $stmt->execute([$id_client]);
            $current_stripe_id = $stmt->fetchColumn();

            if (empty($current_stripe_id)) {
                // Mettre à jour l'ID_STRIPE
                $stmtUpdate = $bdd->prepare("UPDATE clients SET ID_STRIPE = ? WHERE id_client = ?");
                $stmtUpdate->execute([$customer_id, $id_client]);

                // Mettre à jour la session
                $_SESSION['client']['ID_STRIPE'] = $customer_id;
            }
        }
    } catch (Exception $e) {
        // Erreur Stripe ou paiement non réussi
        $error_message = htmlspecialchars($e->getMessage());
    }
}

// Commencer une transaction pour l'enregistrement de la commande
$bdd->beginTransaction();

try {
    // Récupérer les réservations pour ce client
    $sql_reservations = $bdd->prepare("SELECT id_art, quantite_reservee FROM reservations WHERE id_client = ?");
    $sql_reservations->execute([$client['id']]);
    $reservations = $sql_reservations->fetchAll();

    if (empty($reservations)) {
        throw new Exception("Aucune réservation trouvée pour ce client.");
    }

    // Calculer le total de la commande
    $total_commande = 0;
    foreach ($reservations as $reservation) {
        $id_art = $reservation['id_art'];
        $quantite = $reservation['quantite_reservee'];

        // Récupérer le prix de l'article
        $sql_price = $bdd->prepare("SELECT prix FROM articles WHERE id_art = ?");
        $sql_price->execute([$id_art]);
        $prix_unitaire = $sql_price->fetchColumn();

        if (!$prix_unitaire) {
            throw new Exception("Prix introuvable pour l'article ID $id_art.");
        }

        $total_commande += $prix_unitaire * $quantite;
    }

    // Insérer la nouvelle commande dans la table commandes
    $sql_insert_commande = "INSERT INTO commandes (id_client, date_commande, total_commande, envoi) VALUES (:id_client, NOW(), :total_commande, FALSE)";
    $stmt_insert_commande = $bdd->prepare($sql_insert_commande);
    $stmt_insert_commande->execute([
        ':id_client' => $client['id'],
        ':total_commande' => $total_commande
    ]);

    // Récupérer l'ID de la commande insérée
    $id_commande = $bdd->lastInsertId();

    // Pour chaque réservation, insérer dans commande_items et mettre à jour le stock
    foreach ($reservations as $reservation) {
        $id_art = $reservation['id_art'];
        $quantite = $reservation['quantite_reservee'];

        // Récupérer l'article dans la BDD
        $sql_stock = $bdd->prepare("SELECT quantite FROM articles WHERE id_art = ?");
        $sql_stock->execute([$id_art]);
        $article = $sql_stock->fetch();

        if (!$article) {
            throw new Exception("Article ID $id_art non trouvé.");
        }

        $stock = (int)$article['quantite'];

        if ($stock < $quantite) {
            throw new Exception("Stock insuffisant pour l'article ID $id_art.");
        }

        // Insérer l'élément de commande dans la table commande_items
        $sql_insert_item = "INSERT INTO commande_items (id_commande, id_art, quantite) VALUES (:id_commande, :id_art, :quantite)";
        $stmt_insert_item = $bdd->prepare($sql_insert_item);
        $stmt_insert_item->execute([
            ':id_commande' => $id_commande,
            ':id_art' => $id_art,
            ':quantite' => $quantite
        ]);

        // Mettre à jour la quantité en stock
        $sql_update_stock = "UPDATE articles SET quantite = quantite - :quantite WHERE id_art = :id_art";
        $stmt_update_stock = $bdd->prepare($sql_update_stock);
        $stmt_update_stock->execute([
            ':quantite' => $quantite,
            ':id_art' => $id_art
        ]);

        // Supprimer la réservation
        $sql_delete_reservation = "DELETE FROM reservations WHERE id_art = ? AND id_client = ?";
        $stmt_delete_reservation = $bdd->prepare($sql_delete_reservation);
        $stmt_delete_reservation->execute([$id_art, $client['id']]);
    }

    // Si tout se passe bien, valider la transaction
    $bdd->commit();

    // Vider le panier
    unset($_SESSION['panier']);

    // Préparer le message de confirmation
    $confirmation_message = "<h1>Confirmation de commande</h1>";
    $confirmation_message .= "<p>Votre commande a bien été enregistrée et le paiement est confirmé.</p>";
    $confirmation_message .= "<a class='btn-retour' href='index.php'>Retour à l'accueil</a>";
} catch (Exception $e) {
    // Si une erreur survient, annuler la transaction
    $bdd->rollBack();
    $error_message = htmlspecialchars($e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de commande</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/bandeau.css">
    <link rel="stylesheet" href="styles/pied_page.css">
</head>
<body>
<?php include 'bandeau.php'; ?>

<div class="content">
    <?php
    if (isset($confirmation_message)) {
        echo $confirmation_message;
    } elseif (isset($error_message)) {
        echo "<h1>Erreur de commande</h1>";
        echo "<p>" . $error_message . "</p>";
        echo "<a class='btn-retour' href='panier.php'>Retour au panier</a>";
    }
    ?>
</div>

<?php include 'pied_page.php'; ?>
</body>
</html>
