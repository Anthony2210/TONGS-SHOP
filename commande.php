<?php
/**
 * commande.php
 *
 * Affiche le récapitulatif de la commande et gère la création de la session de paiement avec Stripe.
 */

session_start();
require_once 'bd.php';
require_once 'token.php';
require_once(__DIR__ . '/vendor/autoload.php'); // Composer autoload for Stripe
require_once('stripe.php'); // Contient les clés Stripe et l'objet $stripe

$bdd = getBD();

// Vérification si le client est connecté
if (!isset($_SESSION['client'])) {
    header('Location: connexion.php');
    exit();
}

// Récupérer le panier depuis la session
$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];

// Vérifier si le panier est vide
if (empty($panier)) {
    // Panier vide
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Panier vide</title>
        <link rel="stylesheet" href="styles/style.css">
        <link rel="stylesheet" href="styles/panier.css">
        <link rel="stylesheet" href="styles/bandeau.css">
        <link rel="stylesheet" href="styles/pied_page.css">
        <script src="scripts/bandeau.js"></script>
    </head>
    <body>
    <?php include 'bandeau.php'; ?>
    <div class='panier-vide'>
        <h2>Votre panier est vide.</h2>
        <p>Il semble que vous n'ayez pas encore ajouté d'articles à votre panier.</p>
        <a href='index.php' class='btn-retour'>Retour à la boutique</a>
    </div>
    <?php include 'pied_page.php'; ?>
    </body>
    </html>
    <?php
    exit();
}

// Récupérer les informations des articles dans le panier
$articles = [];
$total = 0;

foreach ($panier as $item) {
    $id_art = $item['id_art'];
    $quantite = $item['quantite'];

    // Récupérer les détails de l'article depuis la base de données
    $stmt = $bdd->prepare("SELECT nom, prix, ID_STRIPE, url_photo FROM articles WHERE id_art = ?");
    $stmt->execute([$id_art]);
    $article = $stmt->fetch();

    if ($article) {
        $nom = htmlspecialchars($article['nom']);
        $prix_unitaire = $article['prix'];
        $ID_STRIPE = $article['ID_STRIPE'];
        $url_photo = htmlspecialchars($article['url_photo']);
        $prix_total_article = $prix_unitaire * $quantite;
        $total += $prix_total_article;

        $articles[] = [
            'id_art' => $id_art,
            'nom' => $nom,
            'prix_unitaire' => $prix_unitaire,
            'quantite' => $quantite,
            'prix_total' => $prix_total_article,
            'ID_STRIPE' => $ID_STRIPE,
            'url_photo' => $url_photo
        ];
    }
}

// Préparer les line_items pour Stripe
$line_items = [];
foreach ($articles as $article) {
    $line_items[] = [
        'price' => $article['ID_STRIPE'],
        'quantity' => $article['quantite']
    ];
}

// Récupérer l'ID_STRIPE du client (customer ID)
$sql_client = $bdd->prepare("SELECT ID_STRIPE FROM clients WHERE id_client = ?");
$sql_client->execute([$_SESSION['client']['id']]);
$id_stripe_client = $sql_client->fetchColumn();

// Préparer les paramètres pour la session Stripe
if (empty($id_stripe_client)) {
    // Pas d'ID client Stripe, utilisation de customer_email
    $checkout_params = [
        'success_url' => 'http://localhost/TONGS%20SHOP/acheter.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/TONGS%20SHOP/commande.php',
        'mode' => 'payment',
        'line_items' => $line_items,
        'customer_email' => $_SESSION['client']['mail'],
        'automatic_tax' => ['enabled' => false],
    ];
} else {
    // ID Stripe client déjà connu
    $checkout_params = [
        'customer' => $id_stripe_client,
        'success_url' => 'http://localhost/TONGS%20SHOP/acheter.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/TONGS%20SHOP/commande.php',
        'mode' => 'payment',
        'line_items' => $line_items,
        'automatic_tax' => ['enabled' => false],
    ];
}

/**
 * Gère la validation de la commande et la création de la session Stripe.
 *
 * @return void
 */
function handleCheckout() {
    global $stripe, $checkout_params;

    // Vérifier si le formulaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Valider le token CSRF
        if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
            echo "<p>Token CSRF invalide.</p>";
            echo "<a class='btn-retour' href='panier.php'>Retour au panier</a>";
            include 'pied_page.php';
            exit();
        }

        try {
            // Créer la session de paiement avec Stripe
            $checkout_session = $stripe->checkout->sessions->create($checkout_params);
            header("HTTP/1.1 303 See Other");
            header("Location: " . $checkout_session->url);
            exit();
        } catch (Exception $e) {
            // Gestion des erreurs lors de la création de la session
            echo "<p>Erreur lors de la création de la session de paiement : " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<a class='btn-retour' href='panier.php'>Retour au panier</a>";
            include 'pied_page.php';
            exit();
        }
    }
}

// Appeler la fonction pour gérer la validation de la commande
handleCheckout();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récapitulatif de la commande</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/panier.css">
    <link rel="stylesheet" href="styles/bandeau.css">
    <link rel="stylesheet" href="styles/pied_page.css">
    <script src="scripts/bandeau.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Ajout du meta tag pour le token CSRF -->
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
</head>
<body>
<?php include 'bandeau.php'; ?>

<h1>Récapitulatif de votre commande</h1>

<div class="commande-container">
    <?php
    foreach ($articles as $article) {
        ?>
        <div class="commande-item">
            <img src="<?php echo $article['url_photo']; ?>" alt="Image de <?php echo $article['nom']; ?>" class="commande-image">
            <div class="commande-details">
                <h3><?php echo $article['nom']; ?></h3>
                <p>Quantité : <?php echo $article['quantite']; ?></p>
                <p>Prix unitaire : <?php echo number_format($article['prix_unitaire'], 2); ?> €</p>
                <p>Prix total : <?php echo number_format($article['prix_total'], 2); ?> €</p>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<div class="commande-total">
    <h2>Montant total de votre commande : <?php echo number_format($total, 2); ?> €</h2>
</div>

<div class="commande-adresse">
    <h3>La commande sera expédiée à l'adresse suivante :</h3>
    <p><?php echo htmlspecialchars($_SESSION['client']['prenom'] . " " . $_SESSION['client']['nom']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($_SESSION['client']['adresse'])); ?></p>
</div>

<div class="commande-actions">
    <div class="btn-group">
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <button type="submit" class="btn-valider-commande">
                <i class="fas fa-check"></i> Valider la commande (Paiement)
            </button>
        </form>
        <a href="index.php" class="btn-retour">
            <i class="fas fa-home"></i> Retour à l'accueil
        </a>
    </div>
</div>

<?php include 'pied_page.php'; ?>
<script src="scripts/bandeau.js"></script>
</body>
</html>

<?php
$bdd = null;
?>
