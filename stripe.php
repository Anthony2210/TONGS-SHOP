<?php
// stripe.php
require_once('vendor/autoload.php'); // Ajuster le chemin selon votre arborescence

// Clé secrète Stripe depuis une variable d'environnement
$stripe_secret_key = getenv('STRIPE_SECRET_KEY');

if (!$stripe_secret_key) {
    die("Erreur : La clé Stripe n'est pas définie.");
}

\Stripe\Stripe::setApiKey($stripe_secret_key);

// Création de l'objet Stripe Client
$stripe = new \Stripe\StripeClient($stripe_secret_key);

