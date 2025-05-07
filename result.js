document.addEventListener('DOMContentLoaded', function() {
    const carouselItems = document.querySelector('.carousel-items');
    const leftArrow = document.querySelector('.carousel-arrow-left');
    const rightArrow = document.querySelector('.carousel-arrow-right');
    
    const itemsPerView = 7; // Nombre d'éléments visibles à la fois
    const weatherItems = document.querySelectorAll('.weather-item');
    const totalItems = weatherItems.length;
    const itemWidth = 100 / itemsPerView; // Largeur en pourcentage de chaque élément
    
    let currentPosition = 0; // Position actuelle du premier élément visible
    const maxPosition = totalItems - itemsPerView; // Position maximale possible
    
    // Initialiser la largeur de chaque élément
    weatherItems.forEach(item => {
        item.style.minWidth = `${itemWidth}%`;
    });
    
    // Mise à jour initiale
    updateCarouselView();
    
    // Gestion des flèches de navigation
    leftArrow.addEventListener('click', function() {
        if (currentPosition > 0) {
            currentPosition--;
            updateCarouselView();
        }
    });
    
    rightArrow.addEventListener('click', function() {
        if (currentPosition < maxPosition) {
            currentPosition++;
            updateCarouselView();
        }
    });
    
    // Mettre à jour l'affichage du carousel
    function updateCarouselView() {
        // Déplacer les éléments d'une seule case
        carouselItems.style.transform = `translateX(-${currentPosition * itemWidth}%)`;
        
        // Désactiver/activer les flèches selon la position
        leftArrow.style.opacity = currentPosition === 0 ? '0.5' : '1';
        rightArrow.style.opacity = currentPosition === maxPosition ? '0.5' : '1';
    }
});