<?php
/**
 * panier.php
 *
 * Affiche les articles du panier de l'utilisateur et permet de modifier les quantités via AJAX.
 */

session_start();
require_once 'token.php';
require_once 'bd.php';

$bdd = getBD();

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['client'])) {
    header('Location: connexion.php');
    exit();
}

// Récupérer le panier depuis la session
$panier = &$_SESSION['panier'];

// Vérifier si le panier est vide avant l'affichage du HTML
if (empty($panier)) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Votre Panier</title>
        <link rel="stylesheet" href="styles/panier.css">
        <link rel="stylesheet" href="styles/style.css">
        <link rel="stylesheet" href="styles/pied_page.css">
        <link rel="stylesheet" href="styles/bandeau.css">
    </head>
    <body>
    <?php include 'bandeau.php'; ?>
    <div class='panier-vide'>
        <h2>Votre panier est vide !</h2>
        <p>Il semble que vous n'ayez pas encore ajouté d'articles à votre panier.</p>
        <a href='index.php' class='btn-commencer'>Commencez vos achats</a>
        <a href='historique.php' class="btn-commencer">Consulter votre historique de commande</a>
    </div>
    <?php include 'pied_page.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

// Calculer le montant total de la commande
$total_commande = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="styles/panier.css">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/pied_page.css">
    <link rel="stylesheet" href="styles/bandeau.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/panier.js"></script>
    <script src="scripts/bandeau.js"></script>
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
</head>
<body>
<?php include 'bandeau.php'; ?>
<h1 class="panier-title">Votre Panier</h1>

<div class="panier-container">
    <?php
    // Parcourir chaque article du panier
    foreach ($panier as $article_panier) {
        $id_art = $article_panier['id_art'];
        $quantite = $article_panier['quantite'];

        // Récupérer les informations sur l'article
        $sql = $bdd->prepare("SELECT nom, prix, quantite, url_photo FROM articles WHERE id_art = ?");
        $sql->execute([$id_art]);
        $article = $sql->fetch();

        if ($article) {
            $nom = htmlspecialchars($article['nom']);
            $prix_unitaire = $article['prix'];

            // Récupérer la réservation actuelle pour cet utilisateur et cet article
            if (isset($_SESSION['client'])) {
                $id_client = $_SESSION['client']['id'];
                $sql_reservation = $bdd->prepare("SELECT quantite_reservee FROM reservations WHERE id_art = ? AND id_client = ?");
                $sql_reservation->execute([$id_art, $id_client]);
                $reservation = $sql_reservation->fetch();
                $quantite_reservee = $reservation ? (int)$reservation['quantite_reservee'] : 0;
            } else {
                $quantite_reservee = 0;
            }

            // Récupérer le total réservé pour cet article
            $sql_total_reserved = $bdd->prepare("SELECT SUM(quantite_reservee) AS total_reserved FROM reservations WHERE id_art = ?");
            $sql_total_reserved->execute([$id_art]);
            $result_total_reserved = $sql_total_reserved->fetch();
            $total_reserved = $result_total_reserved['total_reserved'] ? (int)$result_total_reserved['total_reserved'] : 0;

            // Calculer la quantité disponible pour cet utilisateur
            $quantite_disponible = $article['quantite'] - $total_reserved + $quantite_reservee;

            $prix_total = $prix_unitaire * $quantite;
            $total_commande += $prix_total;

            echo "<div class='panier-item' data-id-art='$id_art'>";
            echo "<img src='./images/soleil.png' class='soleil-icon' alt='soleil' />";
            echo "<div class='panier-image'><img src='./" . htmlspecialchars($article['url_photo']) . "' alt='$nom'></div>";
            echo "<div class='panier-details'>";
            echo "<h3><a href='article.php?id_art=$id_art'>$nom</a></h3>";
            echo "<p>Prix unitaire : " . number_format($prix_unitaire, 2) . " €</p>";
            echo "</div>";

            echo "<div class='panier-quantite-actions'>";
            echo "<div class='quantite-controller form-quantite-speciale'>";
            // Ajout d'attributs supplémentaires pour la validation
            echo "<input type='hidden' name='id_art' value='$id_art'>";
            echo "<input type='number' name='quantite' value='$quantite' min='0' max='$quantite_disponible' class='quantite-field' step='1'>";
            echo "</div>";

            echo "<div class='panier-total-article'>";
            echo "<p class='prix-total-article'>Prix total : " . number_format($prix_total, 2) . " €</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }
    ?>
</div>

<div class="panier-total">
    <p><strong>Montant total de la commande :</strong> <span id="total-commande"><?php echo number_format($total_commande, 2); ?> €</span></p>
</div>

<div class="panier-actions">
    <p class="panier-retour">
        <a href="commande.php" class="panier-retour">Passer la commande</a>
    </p>

    <p class="panier-retour">
        <a href="historique.php" class="panier-retour">Suivi des commandes</a>
    </p>

    <p class="panier-retour"><a href="index.php">Retour aux achats</a></p>
</div>

<!-- Notification Container -->
<div id="notification" class="hidden"></div>


<?php include 'pied_page.php'; ?>
</body>
</html>

<?php
$bdd = null;
?>
