document.addEventListener('turbo:load', function() {
    // Pour chaque message flash
    const flashMessages = document.querySelectorAll('.flash-message');

    flashMessages.forEach(function(message) {
        // Fonction pour faire disparaître le message
        const fadeOutMessage = function() {
            message.style.transition = 'opacity 0.5s ease-out, transform 0.3s ease-out';
            message.style.opacity = '0';
            message.style.transform = 'translateY(-10px)';

            setTimeout(function() {
                message.remove();
            }, 500);
        };

        // Ajouter un écouteur d'événement pour fermer le message lors du clic
        message.addEventListener('click', fadeOutMessage);

        // Configurer le délai avant le fade out (3 secondes)
        setTimeout(fadeOutMessage, 3000);

        // Événement click sur le bouton de fermeture (empêche la propagation)
        const closeButton = message.querySelector('.flash-close');
        if (closeButton) {
            closeButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Empêche le clic de se propager au parent
                fadeOutMessage();
            });
        }
    });
});