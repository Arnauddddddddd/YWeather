document.addEventListener('DOMContentLoaded', function() {

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


    document.addEventListener('mousemove', (event) => {
        const elementUnderCursor = document.elementFromPoint(event.clientX, event.clientY);

        if (elementUnderCursor) {
            const cursorStyle = window.getComputedStyle(elementUnderCursor).cursor;

            if (cursorStyle === 'pointer') {
                follower.id = 'followerHover';
            } else {
                follower.id = 'follower';
            }
        }
    });

});
