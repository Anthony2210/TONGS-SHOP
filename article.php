<?php
session_start();
require_once 'bd.php';
require_once 'token.php';

$bdd = getBD();

// Récupérer l'ID de l'article et valider
$id_art = filter_input(INPUT_GET, 'id_art', FILTER_VALIDATE_INT);

// Initialiser l'article à null
$article = null;

if ($id_art) {
    // Récupération de l'article dans la BDD
    $sql = $bdd->prepare("SELECT nom, description, quantite, prix, url_photo FROM articles WHERE id_art = ?");
    $sql->execute([$id_art]);
    $article = $sql->fetch();
}
// Calculer le total réservé par tous les utilisateurs
$sql_total_reserved = $bdd->prepare("SELECT SUM(quantite_reservee) AS total_reserved FROM reservations WHERE id_art = ?");
$sql_total_reserved->execute([$id_art]);
$result_total_reserved = $sql_total_reserved->fetch();
$total_reserved = $result_total_reserved['total_reserved'] ? (int)$result_total_reserved['total_reserved'] : 0;
$quantite_disponible = $article['quantite'] - $total_reserved;

// Définir le titre de la page en fonction de l'existence de l'article
$page_title = $article ? htmlspecialchars($article['nom']) : 'Article Introuvable';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/pied_page.css">
    <link rel="stylesheet" href="styles/bandeau.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pE6Vg+..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
</head>
<body>
<?php include 'bandeau.php'; ?>

<?php if ($article): ?>
    <!-- Affichage des détails de l'article -->
    <h1><?php echo htmlspecialchars($article['nom']); ?></h1>
    <div class="article-container">
        <div class="article-left">
            <img src='<?php echo htmlspecialchars($article['url_photo']); ?>' alt='Image de <?php echo htmlspecialchars($article['nom']); ?>'><br>
            <div class='product-details'>
                <p class='description'><strong>Description :</strong> <?php echo htmlspecialchars($article['description']); ?></p>
                <p class='description'><strong>Quantité en stock :</strong> <?php echo (int)$article['quantite']; ?></p>
                <p class='description'><strong>Prix :</strong> <?php echo number_format($article['prix'], 2, ',', ' '); ?> €</p>
            </div>
        </div>
        <div class="article-right">
            <?php
            // Vérifier si l'utilisateur est connecté
            if (isset($_SESSION['client'])) {
                // Calculer la quantité réservée par cet utilisateur
                $quantite_reservee_user = 0;
                $id_client = $_SESSION['client']['id'];
                $sql_reservation_user = $bdd->prepare("SELECT quantite_reservee FROM reservations WHERE id_art = ? AND id_client = ?");
                $sql_reservation_user->execute([$id_art, $id_client]);
                $reservation_user = $sql_reservation_user->fetch();
                if ($reservation_user) {
                    $quantite_reservee_user = (int)$reservation_user['quantite_reservee'];
                }

                // Vérifier si stock disponible > 0 ou si l'utilisateur a déjà réservé
                if ($quantite_disponible > 0 || $quantite_reservee_user > 0) {
                    ?>
                    <form id="form-ajout-panier" action="ajouter.php" method="post" class="form-panier">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>"> 
                        <input type="hidden" name="id_art" value="<?php echo htmlspecialchars($id_art); ?>">
                        <label for="quantite">Nombre d'exemplaires :</label>
                        <input type="number" name="quantite" id="quantite" value="1" min="1" max="<?php echo ($quantite_disponible + $quantite_reservee_user); ?>" required><br><br>
                        <input type="submit" value="Ajouter au panier" class="btn-ajouter">
                    </form>
                    <script>
                        document.getElementById('form-ajout-panier').addEventListener('submit', function(event) {
                            event.preventDefault(); // Empêche le rechargement de la page
                            const formData = new FormData(this);
                            // Ajout du token CSRF depuis le meta tag
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            formData.append('csrf_token', csrfToken);

                            const quantite = parseInt(formData.get('quantite'), 10);

                            if (isNaN(quantite) || quantite < 1) {
                                alert('Quantité invalide.');
                                return;
                            }

                            const maxStock = <?php echo ($quantite_disponible + $quantite_reservee_user); ?>;
                            if (quantite > maxStock) {
                                alert('La quantité demandée dépasse le stock disponible.');
                                return;
                            }

                            fetch('ajouter.php', {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('Article ajouté au panier avec succès !');
                                    } else {
                                        alert('Erreur : ' + data.message);
                                    }
                                })
                                .catch(error => console.error('Erreur:', error));
                        });
                    </script>
                    <?php
                } else {
                    echo "<p>Il n'y a plus de stock disponible pour cet article.</p>";
                }
            } else {
                echo "<p><a class='lienpage' href='connexion.php'>Connectez-vous</a> pour ajouter cet article à votre panier.</p>";
            }
            ?>
        </div>
    </div>
    <p><a href="index.php" class="btn-retour">Retour à la liste des articles</a></p>

    <!-- Carousel des articles -->
    <section class="articles-carousel">
        <h2 class="section-title">Nos autres articles en stock</h2>
        <div class="carousel-container">
            <button class="carousel-btn left" id="carousel-left-btn"><i class="fas fa-chevron-left"></i></button>
            <div class="carousel" id="carousel">
                <?php
                $sql = "SELECT id_art, nom, description, quantite, prix, url_photo FROM articles WHERE quantite > 0 ORDER BY date_ajout DESC LIMIT 10";
                $stmt = $bdd->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch()):
                    ?>
                    <div class="article-card">
                        <h3><?= htmlspecialchars($row["nom"]); ?></h3>
                        <img src="<?= htmlspecialchars($row["url_photo"]); ?>" alt="Image de <?= htmlspecialchars($row["nom"]); ?>">
                        <p>Prix : <?= htmlspecialchars($row["prix"]); ?> €</p>
                        <p>Stock : <?= htmlspecialchars($row["quantite"]); ?></p>
                        <a href="article.php?id_art=<?= htmlspecialchars($row["id_art"]); ?>" class="btn-voir-article">Voir cet article</a>
                    </div>
                <?php endwhile; ?>
            </div>
            <button class="carousel-btn right" id="carousel-right-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

<?php else: ?>
    <!-- Affichage du message "Article introuvable" -->
    <div class='article-introuvable'>
        <h2>Article introuvable</h2>
        <p>L'article que vous recherchez n'existe pas ou n'est plus disponible.</p>
        <a href='index.php' class='btn-retour'>Retour à la liste des articles</a>
    </div>

    <!-- Carousel des articles -->
    <section class="articles-carousel">
        <h2 class="section-title">Notre catalogue</h2>
        <div class="carousel-container">
            <button class="carousel-btn left" id="carousel-left-btn"><i class="fas fa-chevron-left"></i></button>
            <div class="carousel" id="carousel">
                <?php
                $sql = "SELECT id_art, nom, description, quantite, prix, url_photo FROM articles WHERE quantite > 0 ORDER BY date_ajout DESC LIMIT 10";
                $stmt = $bdd->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch()):
                    ?>
                    <div class="article-card">
                        <h3><?= htmlspecialchars($row["nom"]); ?></h3>
                        <img src="<?= htmlspecialchars($row["url_photo"]); ?>" alt="Image de <?= htmlspecialchars($row["nom"]); ?>">
                        <p>Prix : <?= htmlspecialchars($row["prix"]); ?> €</p>
                        <p>Stock : <?= htmlspecialchars($row["quantite"]); ?></p>
                        <a href="article.php?id_art=<?= htmlspecialchars($row["id_art"]); ?>" class="btn-voir-article">Voir cet article</a>
                    </div>
                <?php endwhile; ?>
            </div>
            <button class="carousel-btn right" id="carousel-right-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>
<?php endif; ?>

<?php
// Requête pour récupérer les autres articles, excluant l'article actuel
$sql_carousel = $bdd->prepare("SELECT id_art, nom, description, quantite, prix, url_photo FROM articles WHERE quantite > 0 AND id_art != ? ORDER BY date_ajout DESC LIMIT 10");
$sql_carousel->execute([$id_art]);
$articles_carousel = $sql_carousel->fetchAll();
?>
<script src="scripts/carousel.js"></script>
<script src="scripts/bandeau.js"></script>
<?php include 'pied_page.php'; ?>
</body>
</html>

<?php
$bdd = null; // Fermer la connexion à la base de données
?>
