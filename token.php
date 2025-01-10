<?php
/**
 * token.php
 *
 * Contient les fonctions nécessaires pour générer et valider les tokens CSRF.
 */

/**
 * Génère un token CSRF et le stocke dans la session.
 *
 * @return string Le token CSRF généré.
 * @throws Exception Si la génération du token échoue.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide un token CSRF.
 *
 * @param string $token Le token CSRF à valider.
 * @return bool True si le token est valide, false sinon.
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
