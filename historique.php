<?php
/**
 * historique.php
 *
 * Page affichant l'historique des commandes du client connecté.
 * Affiche les détails des commandes passées.
 */

session_start();
require_once 'token.php';

if (!isset($_SESSION['client'])) {
    header('Location: connexion.php');
    exit();
}

require_once 'bd.php';
$bdd = getBD();

// Récupérer les informations du client connecté
$client = $_SESSION['client'];
$id_client = $client['id'];

// Requête SQL pour récupérer les commandes du client avec leurs éléments
$sql = "
SELECT c.id_commande, c.date_commande, c.total_commande, c.envoi, 
       ci.id_art, a.nom AS nom_art, a.prix, ci.quantite
FROM commandes c
JOIN commande_items ci ON c.id_commande = ci.id_commande
JOIN articles a ON ci.id_art = a.id_art
WHERE c.id_client = :id_client
ORDER BY c.date_commande DESC, c.id_commande DESC
";
$stmt = $bdd->prepare($sql);
$stmt->execute(['id_client' => $id_client]);

$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les commandes par id_commande
$historique = [];
foreach ($commandes as $commande) {
    $id_commande = $commande['id_commande'];
    if (!isset($historique[$id_commande])) {
        $historique[$id_commande] = [
            'date_commande' => $commande['date_commande'],
            'total_commande' => $commande['total_commande'],
            'envoi' => $commande['envoi'],
            'articles' => []
        ];
    }
    $historique[$id_commande]['articles'][] = [
        'id_art' => $commande['id_art'],
        'nom_art' => $commande['nom_art'],
        'prix' => $commande['prix'],
        'quantite' => $commande['quantite']
    ];
}

$bdd = null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des commandes</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/bandeau.css">
    <link rel="stylesheet" href="styles/pied_page.css">
    <link rel="stylesheet" href="styles/panier.css">
    <script src="scripts/bandeau.js"></script>
</head>
<body>
<?php include 'bandeau.php'; ?>

<?php if (empty($historique)): ?>
    <div class='panier-vide'>
        <h2>Votre historique est vide !</h2>
        <p>Il semble que vous n'ayez pas encore commandé d'articles sur notre site.</p>
        <a href='index.php' class='btn-commencer'>Commencez vos achats</a>
        <a href='panier.php' class="btn-commencer">Consulter votre panier de commande</a>
    </div>
<?php else: ?>
    <h1>Historique de vos commandes</h1>
    <?php foreach ($historique as $id_commande => $commande): ?>
        <div class="commande-section">
            <h2>Commande #<?php echo htmlspecialchars($id_commande); ?></h2>
            <p><strong>Date de commande :</strong> <?php echo htmlspecialchars($commande['date_commande']); ?></p>
            <p><strong>Statut :</strong> <?php echo $commande['envoi'] ? 'Envoyée' : 'Non envoyée'; ?></p>
            <p><strong>Montant total :</strong> <?php echo number_format($commande['total_commande'], 2); ?> €</p>

            <table border="1" class="commande-table">
                <thead>
                <tr>
                    <th>ID Article</th>
                    <th>Nom Article</th>
                    <th>Prix Unitaire</th>
                    <th>Quantité</th>
                    <th>Prix Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($commande['articles'] as $article): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($article['id_art']); ?></td>
                        <td><?php echo htmlspecialchars($article['nom_art']); ?></td>
                        <td><?php echo number_format($article['prix'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($article['quantite']); ?></td>
                        <td><?php echo number_format($article['prix'] * $article['quantite'], 2); ?> €</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <hr>
    <?php endforeach; ?>
    <p class="btn-commencer"><a href="index.php">Retour à l'accueil</a></p>
<?php endif; ?>

<?php include 'pied_page.php'; ?>
</body>
</html>
