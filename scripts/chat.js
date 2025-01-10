/**
 * scripts/chat.js
 *
 * Gère le chargement des messages, l'envoi de nouveaux messages et l'affichage du chat.
 */

/**
 * Charge les messages du chat en les récupérant depuis le serveur.
 *
 * @return void
 */
function loadMessages() {
    fetch('get_messages.php')
        .then(response => response.json())
        .then(data => {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';
            data.forEach(msg => {
                const p = document.createElement('p');
                // Format: "Bob dit 'Le message...'"
                p.textContent = msg.prenom_user + " " + " dit '" + msg.message + "'";
                messagesDiv.appendChild(p);
            });
        })
        .catch(err => console.error('Erreur de chargement des messages:', err));
}

// Chargement initial des messages
loadMessages();

// Rafraîchissement toutes les 10 secondes
setInterval(loadMessages, 10000);

/**
 * Gère la soumission du formulaire de chat pour envoyer un nouveau message.
 */
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const messageInput = document.getElementById('message-input');
    const messageText = messageInput.value.trim();

    // Valider la longueur du message
    if (messageText.length === 0 || messageText.length > 256) {
        alert("Message invalide (doit être entre 1 et 256 caractères).");
        return;
    }

    // Récupérer le token CSRF depuis le meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Envoyer le message via AJAX
    fetch('send_message.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'message=' + encodeURIComponent(messageText) + '&csrf_token=' + encodeURIComponent(csrfToken)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                loadMessages(); // Recharger les messages immédiatement
            } else {
                alert(data.error);
            }
        })
        .catch(err => console.error('Erreur lors de l\'envoi du message:', err));
});

/**
 * Gère le clic sur le header du chat pour minimiser/agrandir le chat.
 */
document.getElementById('chat-header').addEventListener('click', function(e) {
    // Si on clique sur le header ou le bouton, on toggle la classe minimized
    const container = document.getElementById('chat-container');
    container.classList.toggle('minimized');
});
