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


    const follower = document.getElementById('follower');

    let mouseX = 0, mouseY = 0; // position dans la fenêtre (viewport)
    let pageX = 0, pageY = 0;   // position absolue dans la page
    let currentX = 0, currentY = 0;
    const speed = 0.15;

    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      updatePageCoords();
    });

    window.addEventListener('scroll', updatePageCoords);

    function updatePageCoords() {
      pageX = mouseX + window.scrollX;
      pageY = mouseY + window.scrollY;
    }

    function animate() {
      currentX += (pageX - currentX) * speed;
      currentY += (pageY - currentY) * speed;

      follower.style.left = `${currentX}px`;
      follower.style.top = `${currentY}px`;

      requestAnimationFrame(animate);
    }

    animate();
});
