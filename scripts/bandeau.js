/**
 * scripts/bandeau.js
 *
 * Affichage du titre au défilement de la page
 * Gère la soumission du formulaire de déconnexion via AJAX.
 */

window.addEventListener("scroll", function() {
    var h1Element = document.querySelector("h1");
    var pageTitle = document.querySelector(".page-title");

    // Vérifier si les éléments sont bien détectés
    if (h1Element && pageTitle) {
        console.log("h1 et page-title détectés");

        var h1Position = h1Element.getBoundingClientRect().top;
        var scrollPosition = window.scrollY || window.pageYOffset;

        // Si on dépasse le H1, on cache le H1 et affiche son titre dans le bandeau
        if (scrollPosition > h1Position + h1Element.offsetHeight) {
            h1Element.classList.add("h1-fusion"); // Cacher le H1
            if (!pageTitle.textContent) {
                pageTitle.textContent = h1Element.textContent; // Copier le contenu du H1
                console.log("Titre ajouté au bandeau : " + h1Element.textContent);
            }
        } else {
            h1Element.classList.remove("h1-fusion");
            pageTitle.textContent = ""; // Retirer le titre quand on remonte
            console.log("Titre retiré du bandeau");
        }
    } else {
        console.log("h1 ou page-title manquant");
    }
});

document.getElementById('logout-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche le rechargement de la page

    const formData = new FormData(this);
    const csrfToken = formData.get('csrf_token');

    fetch('deconnexion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            csrf_token: csrfToken
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Rediriger vers la page d'accueil après déconnexion
                window.location.href = 'index.php';
            } else {
                alert('Erreur lors de la déconnexion : ' + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
});
