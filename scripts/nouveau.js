/**
 * scripts/nouveau.js
 *
 * Gestion de la validation du formulaire d'inscription et de la vérification AJAX des e-mails.
 */

$(document).ready(function() {
    var validNom = false;
    var validPrenom = false;
    var validAdresse = false;
    var validTelephone = false;
    var validEmail = false;
    var validPassword = false;
    var validConfirmPassword = false;

    /**
     * Vérifie la validité globale du formulaire et active/désactive le bouton d'inscription.
     */
    function checkFormValidity() {
        if (validNom && validPrenom && validAdresse && validTelephone && validEmail && validPassword && validConfirmPassword) {
            $('.btn-inscrire').prop('disabled', false);
        } else {
            $('.btn-inscrire').prop('disabled', true);
        }
    }

    // Validation du Nom
    function validateNom() {
        var nom = $('#n').val();
        if (nom.trim() === '') {
            $('#n').removeClass('valid').addClass('invalid');
            $('#n-error').text('Le nom ne peut pas être vide.');
            validNom = false;
        } else {
            $('#n').removeClass('invalid').addClass('valid');
            $('#n-error').text('');
            validNom = true;
        }
        checkFormValidity();
    }

    $('#n').on('input', function() {
        validateNom();
    });

    // Validation du Prénom
    function validatePrenom() {
        var prenom = $('#p').val();
        if (prenom.trim() === '') {
            $('#p').removeClass('valid').addClass('invalid');
            $('#p-error').text('Le prénom ne peut pas être vide.');
            validPrenom = false;
        } else {
            $('#p').removeClass('invalid').addClass('valid');
            $('#p-error').text('');
            validPrenom = true;
        }
        checkFormValidity();
    }

    $('#p').on('input', function() {
        validatePrenom();
    });

    // Validation de l'Adresse
    function validateAdresse() {
        var adresse = $('#adr').val();
        if (adresse.trim() === '') {
            $('#adr').removeClass('valid').addClass('invalid');
            $('#adr-error').text('L\'adresse ne peut pas être vide.');
            validAdresse = false;
        } else {
            $('#adr').removeClass('invalid').addClass('valid');
            $('#adr-error').text('');
            validAdresse = true;
        }
        checkFormValidity();
    }

    $('#adr').on('input', function() {
        validateAdresse();
    });

    // Validation du Numéro de Téléphone
    function validateTelephone() {
        var numero = $('#num').val();
        if (numero.trim() === '') {
            $('#num').removeClass('valid').addClass('invalid');
            $('#num-error').text('Le numéro de téléphone ne peut pas être vide.');
            validTelephone = false;
        } else {
            $('#num').removeClass('invalid').addClass('valid');
            $('#num-error').text('');
            validTelephone = true;
        }
        checkFormValidity();
    }

    $('#num').on('input', function() {
        validateTelephone();
    });

    // Validation de l'Email avec vérification AJAX
    function validateEmail() {
        var mail = $('#mail').val();
        var mailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (mail.trim() === '') {
            $('#mail').removeClass('valid').addClass('invalid');
            $('#mail-error').text('Le mail ne peut pas être vide.');
            validEmail = false;
            checkFormValidity();
        } else if (!mailPattern.test(mail)) {
            $('#mail').removeClass('valid').addClass('invalid');
            $('#mail-error').text('Format de mail invalide.');
            validEmail = false;
            checkFormValidity();
        } else {
            // Vérifier si le mail existe déjà via AJAX
            $.ajax({
                url: 'nouveau.php',
                method: 'POST',
                data: { mail_check: true, mail: mail },
                dataType: 'json',
                success: function(response) {
                    if (response.exists) {
                        $('#mail').removeClass('valid').addClass('invalid');
                        $('#mail-error').text('Cette adresse e-mail est déjà utilisée.');
                        validEmail = false;
                    } else {
                        $('#mail').removeClass('invalid').addClass('valid');
                        $('#mail-error').text('');
                        validEmail = true;
                    }
                    checkFormValidity();
                },
                error: function() {
                    $('#mail').removeClass('valid').addClass('invalid');
                    $('#mail-error').text('Erreur lors de la vérification de l\'e-mail.');
                    validEmail = false;
                    checkFormValidity();
                }
            });
        }
    }

    $('#mail').on('input', function() {
        validateEmail();
    });

    // Validation du Mot de Passe
    function validatePassword() {
        var password = $('#mdp1').val();
        var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{1,}$/;
        if (password.trim() === '') {
            $('#mdp1').removeClass('valid').addClass('invalid');
            $('#mdp1-error').text('Le mot de passe ne peut pas être vide.');
            validPassword = false;
        } else if (!passwordPattern.test(password)) {
            $('#mdp1').removeClass('valid').addClass('invalid');
            $('#mdp1-error').text('Le mot de passe doit contenir au moins une lettre, un chiffre et un caractère spécial.');
            validPassword = false;
        } else {
            $('#mdp1').removeClass('invalid').addClass('valid');
            $('#mdp1-error').text('');
            validPassword = true;
        }
        validateConfirmPassword(); // Valider aussi la confirmation du mot de passe
        checkFormValidity();
    }

    $('#mdp1').on('input', function() {
        validatePassword();
    });

    // Validation de la Confirmation du Mot de Passe
    function validateConfirmPassword() {
        var confirmPassword = $('#mdp2').val();
        var password = $('#mdp1').val();
        if (confirmPassword.trim() === '') {
            $('#mdp2').removeClass('valid').addClass('invalid');
            $('#mdp2-error').text('Veuillez confirmer votre mot de passe.');
            validConfirmPassword = false;
        } else if (password !== confirmPassword) {
            $('#mdp2').removeClass('valid').addClass('invalid');
            $('#mdp2-error').text('Les mots de passe ne correspondent pas.');
            validConfirmPassword = false;
        } else {
            $('#mdp2').removeClass('invalid').addClass('valid');
            $('#mdp2-error').text('');
            validConfirmPassword = true;
        }
        checkFormValidity();
    }

    $('#mdp2').on('input', function() {
        validateConfirmPassword();
    });

    // Soumission du formulaire via AJAX
    $('#registration-form').on('submit', function(e) {
        e.preventDefault();
        if (validNom && validPrenom && validAdresse && validTelephone && validEmail && validPassword && validConfirmPassword) {
            // Récupérer les données du formulaire
            var formData = {
                n: $('#n').val(),
                p: $('#p').val(),
                adr: $('#adr').val(),
                num: $('#num').val(),
                mail: $('#mail').val(),
                mdp1: $('#mdp1').val(),
                mdp2: $('#mdp2').val(),
                csrf_token: $('input[name="csrf_token"]').val() // Inclure le token CSRF
            };
            // Envoyer les données via AJAX
            $.ajax({
                url: 'enregistrement.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#message').html('<p class="success-message">' + response.message + '</p>');
                        // Redirection après 1 seconde
                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 1000);
                    } else {
                        $('#message').html('<p class="error-message">' + response.message + '</p>');
                    }
                },
                error: function() {
                    $('#message').html('<p class="error-message">Erreur lors de la création du compte.</p>');
                }
            });
        } else {
            $('#message').html('<p class="error-message">Veuillez remplir correctement tous les champs.</p>');
        }
    });
});