<?php
/**
 * bandeau.php
 *
 * Bandeau de navigation pour le site TONGS SHOP.
 * Affiche le logo, le titre et les options utilisateur en fonction de la session.
 */
?>
<header class="bandeau">
    <div class="logo">
        <a href="index.php">
            <img src="images/soleil.png" alt="Soleil" class="soleil-logo">
            <span class="titre">Tongs Shop</span>
        </a>
    </div>
    <div class="page-title-container">
        <span class="page-title"></span>
    </div>
    <div class="user-options">
        <?php
        // Vérifier si un utilisateur est connecté
        if (isset($_SESSION['client'])) {
            $prenom = htmlspecialchars($_SESSION['client']['prenom']);
            $nom = htmlspecialchars($_SESSION['client']['nom']);
            echo "<a href='historique.php'>";
            echo "<img src='images/soleil.png' alt='Historique' class='soleil-icon'>";
            echo "<span>Historique</span>";
            echo "</a>";
            echo "<a href='panier.php'>";
            echo "<img src='images/soleil.png' alt='Panier' class='soleil-icon'>";
            echo "<span>Panier</span>";
            echo "</a>";
            // Formulaire de déconnexion sécurisé
            ?>
            <form id="logout-form" method="POST" action="deconnexion.php" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <button type="submit" class="logout-button">
                    <img src="images/soleil.png" alt="Déconnexion" class="soleil-icon">
                    <span>Déconnexion</span>
                </button>
            </form>
            <?php
        } else {
            // Afficher les liens "Nouveau Client" et "Se connecter" si l'utilisateur n'est pas connecté
            echo "<a href='connexion.php'>";
            echo "<img src='images/soleil.png' alt='Connexion' class='soleil-icon'>";
            echo "<span>Connexion</span>";
            echo "</a>";
            echo "<a href='nouveau.php'>";
            echo "<img src='images/soleil.png' alt='Nouveau Client' class='soleil-icon'>";
            echo "<span>Nouveau Client</span>";
            echo "</a>";
        }
        ?>
    </div>
</header>
