/**
 * scripts/carousel.js
 *
 * Gère le comportement du carousel de produits.
 */

document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('carousel');
    const leftBtn = document.getElementById('carousel-left-btn');
    const rightBtn = document.getElementById('carousel-right-btn');
    const cards = carousel.querySelectorAll('.article-card');
    const cardCount = cards.length;
    let currentIndex = 0; // Index du premier article visible

    // Largeur d’un seul article + gap
    const cardWidth = cards[0].offsetWidth + 20;

    /**
     * Met à jour la position du carousel en fonction de l'index actuel.
     */
    function updateCarousel() {
        // modulo pour créer l’effet infini
        let offsetIndex = (currentIndex % cardCount + cardCount) % cardCount;
        // décalage en pixels
        const translateX = -offsetIndex * cardWidth;
        carousel.style.transform = `translateX(${translateX}px)`;
    }

    /**
     * Passe à l'article suivant dans le carousel.
     */
    rightBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % cardCount;
        updateCarousel();
    });

    /**
     * Retourne à l'article précédent dans le carousel.
     */
    leftBtn.addEventListener('click', () => {
        // cardCount avant le modulo pour éviter un résultat négatif
        currentIndex = (currentIndex - 1 + cardCount) % cardCount;
        updateCarousel();
    });

    // Initialiser la position du carousel
    updateCarousel();
});
