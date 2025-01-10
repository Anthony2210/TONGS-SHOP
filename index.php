<?php
/**
 * Page d'accueil du site TONGS SHOP.
 * Affiche les derniers articles disponibles et un message de bienvenue.
 */

session_start();
require_once 'bd.php';
require_once 'token.php';
$bdd = getBD();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TONGS SHOP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Liens vers les styles et polices -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/chat.css">
    <link rel="stylesheet" href="styles/pied_page.css">
    <link rel="stylesheet" href="styles/bandeau.css">
    <meta name="csrf-token" content="<?php echo generate_csrf_token(); ?>">
</head>
<body>
<?php include 'bandeau.php'; ?>
<script src="scripts/bandeau.js"></script>

<main>
    <h1>TONGS SHOP</h1>
    <!-- Message de bienvenue -->
    <?php if (isset($_SESSION['client'])): ?>
        <div class="welcome-message">
            <h2>Bienvenue sur TONGS SHOP, <?= htmlspecialchars($_SESSION['client']['prenom']); ?> ! ☀️</h2>
            <p>Profitez de nos dernières offres et bonnes affaires.</p>
        </div>
    <?php else: ?>
        <h2>Bienvenue sur Tongs Shop ! ☀️</h2>
        <h4>Connectez-vous pour profiter d'une expérience personnalisée et découvrir nos meilleurs articles !</h4>
    <?php endif; ?>

    <!-- Dernier article ajouté -->
    <section class="latest-article">
        <h2 class="section-title">Dernier article disponible</h2>
        <?php
        $sql = "SELECT id_art, nom, description, quantite, prix, url_photo FROM Articles ORDER BY date_ajout DESC LIMIT 1";
        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $lastArticle = $stmt->fetch();

        if ($lastArticle):
            ?>
            <div class="dernier-article">
                <!-- Soleils décoratifs -->
                <img src="images/soleil.png" alt="Soleil" class="soleil-icon-da soleil-left-top-da">
                <img src="images/soleil.png" alt="Soleil" class="soleil-icon-da soleil-right-top-da">
                <img src="images/soleil.png" alt="Soleil" class="soleil-icon-da soleil-left-bottom-da">
                <img src="images/soleil.png" alt="Soleil" class="soleil-icon-da soleil-right-bottom-da">

                <h3><?= htmlspecialchars($lastArticle["nom"]); ?></h3>
                <img src="<?= htmlspecialchars($lastArticle["url_photo"]); ?>" alt="Image de <?= htmlspecialchars($lastArticle["nom"]); ?>">
                <p><?= nl2br(htmlspecialchars($lastArticle["description"])); ?></p>
                <p>Prix : <?= htmlspecialchars($lastArticle["prix"]); ?> €</p>
                <p>Stock : <?= htmlspecialchars($lastArticle["quantite"]); ?></p>
                <a href="article.php?id_art=<?= htmlspecialchars($lastArticle["id_art"]); ?>" class="btn-voir-article">Voir cet article</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Carousel des articles -->
    <section class="articles-carousel">
        <h2 class="section-title">Nos articles en stock</h2>
        <div class="carousel-container">
            <button class="carousel-btn left" id="carousel-left-btn"><i class="fas fa-chevron-left"></i></button>
            <div class="carousel" id="carousel">
                <?php
                $sql = "SELECT id_art, nom, description, quantite, prix, url_photo FROM Articles WHERE quantite > 0 ORDER BY date_ajout DESC LIMIT 10";
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

    <div id="chat-container" class="expanded">
        <div id="chat-header">
            <span>Chat</span>
            <button id="chat-toggle-button" title="Réduire/Agrandir">−</button>
        </div>
        <div id="messages"></div>
        <form id="chat-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="text" name="message" id="message-input" maxlength="256" placeholder="Votre message..." required>
            <button type="submit">Envoyer</button>
        </form>
    </div>

</main>

<script src="scripts/carousel.js"></script>
<script src="scripts/chat.js"></script>
<?php include 'pied_page.php'; ?>

<?php $bdd = null; ?>
</body>
</html>
