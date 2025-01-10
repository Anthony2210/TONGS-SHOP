/**
* scripts/panier.js
*
* Gère la modification des quantités des articles dans le panier via AJAX.
*/

$(document).ready(function() {
    /**
     * Affiche une notification à l'utilisateur.
     *
     * @param {string} message - Le message à afficher.
     * @param {string} type - Le type de notification ('success' ou 'error').
     */
    function showNotification(message, type = 'success') {
        const notification = $('#notification');
        notification.removeClass('hidden').removeClass('success error').addClass(type);
        notification.text(message);
        setTimeout(function() {
            notification.addClass('hidden').removeClass('success error');
        }, 3000);
    }

    // Supprimer les messages d'erreur précédents lors de la modification de la quantité
    $('.quantite-field').on('input', function() {
        const input = $(this);
        input.removeClass('input-error');
    });

    // Gérer le changement de quantité
    $('.quantite-field').on('change', function() {
        const input = $(this);
        const id_art = input.closest('.panier-item').data('id-art');
        let nouvelle_quantite = parseInt(input.val(), 10);

        // Validation côté client
        if (isNaN(nouvelle_quantite) || nouvelle_quantite < 0) {
            showNotification("Quantité invalide. Veuillez entrer un nombre positif.", 'error');
            input.val(input.prop('defaultValue'));
            return;
        }

        // Limiter la quantité à la valeur maximale disponible
        const max = parseInt(input.attr('max'), 10);
        if (nouvelle_quantite > max) {
            showNotification(`La quantité maximale disponible est de ${max}.`, 'error');
            input.val(max);
            nouvelle_quantite = max;
        }

        // Confirmation si nouvelle_quantite=0 => suppression
        if (nouvelle_quantite === 0) {
            if (!confirm("Voulez-vous supprimer cet article du panier ?")) {
                input.val(input.prop('defaultValue'));
                return;
            }
        }

        // Récupérer le token CSRF depuis le meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Appel AJAX vers modifier_panier.php
        $.ajax({
            url: 'modifier_panier.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id_art: id_art,
                quantite: nouvelle_quantite,
                csrf_token: csrfToken // Inclure le token CSRF
            }),
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Mettre à jour le prix total de l'article
                    const prixUnitaire = parseFloat(data.prix_unitaire);
                    const nouveauPrixTotal = (prixUnitaire * nouvelle_quantite).toFixed(2);
                    input.closest('.panier-item').find('.prix-total-article').text(`Prix total : ${nouveauPrixTotal} €`);

                    // Mettre à jour le montant total du panier
                    $('#total-commande').text(`${data.total_commande} €`);

                    // Si l'article a été supprimé, retirer l'élément du DOM
                    if (nouvelle_quantite === 0) {
                        input.closest('.panier-item').fadeOut(300, function() {
                            $(this).remove();
                            // Vérifier si le panier est vide
                            if ($('.panier-item').length === 0) {
                                window.location.reload(); // Recharger la page pour afficher le message "panier vide"
                            }
                        });
                    } else {
                        input.prop('defaultValue', nouvelle_quantite);
                    }

                    showNotification('Panier mis à jour avec succès.', 'success');
                } else {
                    showNotification('Erreur: ' + data.message, 'error');
                    // Remettre l'ancienne valeur en cas d'erreur
                    input.val(input.prop('defaultValue'));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                showNotification("Erreur réseau ou serveur.", 'error');
                // Remettre l'ancienne valeur
                input.val(input.prop('defaultValue'));
            }
        });
    });
});