<?php
session_start();
require_once 'bd.php';
require_once 'stripe.php';
require_once 'token.php';

$bdd = getBD();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo 'Invalid request';
    exit;
}

// Vérification du token CSRF
if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    echo 'Invalid CSRF token';
    exit;
}

// Récupérer le panier
$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];
if (empty($panier)) {
    echo 'Panier vide';
    exit;
}

// Récupérer l'ID Stripe du client connecté
$client = $_SESSION['client'];
$id_stripe_client = "";
// Récupérez l'ID Stripe du client depuis la BDD :
$stmt = $bdd->prepare("SELECT ID_STRIPE FROM clients WHERE id_client=?");
$stmt->execute([$client['id']]);
$id_stripe_client = $stmt->fetchColumn();

// Construire $line_items, le tableau des line_items
$line_items = [];
foreach ($panier as $article) {
    $id_art = $article['id_art'];
    // Récupérer l'ID_STRIPE du produit:
    $stmtArt = $bdd->prepare("SELECT ID_STRIPE FROM articles WHERE id_art=?");
    $stmtArt->execute([$id_art]);
    $price_id = $stmtArt->fetchColumn();

    $line_items[] = [
        'price' => $price_id,
        'quantity' => $article['quantite']
    ];
}

try {
    $checkout_session = $stripe->checkout->sessions->create([
        'customer' => $id_stripe_client, // L'ID du client Stripe
        'success_url' => 'http://votre-site/acheter.php?session_id={CHECKOUT_SESSION_ID}', // URL si OK
        'cancel_url' => 'http://votre-site/commande.php', // URL si annulation
        'mode' => 'payment', // mode paiement unique
        'automatic_tax' => ['enabled' => false],
        'line_items' => $line_items,
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
    exit;
} catch (Exception $e) {
    // Gestion des erreurs Stripe ou autres
    echo 'Erreur lors de la création de la session de paiement : ' . htmlspecialchars($e->getMessage());
    exit;
}
?>
