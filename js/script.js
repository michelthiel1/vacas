document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DO LIGHTBOX DE FOTOS ---
    const lightboxOverlay = document.getElementById('lightbox-overlay');
    const lightboxImg = document.getElementById('lightbox-img');
    const closeButton = document.querySelector('.lightbox-close');
    const imageTriggers = document.querySelectorAll('.lightbox-trigger');

    function openLightbox(e) {
        if (lightboxOverlay && lightboxImg) {
            lightboxImg.src = e.target.src;
            lightboxOverlay.style.display = 'flex';
        }
    }

    function closeLightbox() {
        if (lightboxOverlay) {
            lightboxOverlay.style.display = 'none';
        }
    }

    imageTriggers.forEach(img => {
        img.addEventListener('click', openLightbox);
    });

    if (closeButton) {
        closeButton.addEventListener('click', closeLightbox);
    }
    
    if (lightboxOverlay) {
        // Fecha também ao clicar fora da imagem
        lightboxOverlay.addEventListener('click', function(e) {
            if (e.target === lightboxOverlay) {
                closeLightbox();
            }
        });
    }

});