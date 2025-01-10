<?php
header('Content-Type: application/json; charset=utf-8');

// Charger scoremap.json
$scoremap = json_decode(file_get_contents("scoremap.json"), true);

function classify_message($message, $scoremap) {
    $message = mb_strtolower($message, 'UTF-8');

    // Retirer ponctuation
    $message = str_replace(array('.', ',', '!', '?', ':', ';', '"', '\''), ' ', $message);
    $words = preg_split('/\s+/', trim($message));

    $total_score = 0.0;
    foreach ($words as $w) {
        if (isset($scoremap[$w])) {
            $total_score += $scoremap[$w];
        }
    }

    if ($total_score > 0) {
        return "noHate";
    } else {
        return "hate";
    }
}

// Récupérer le message envoyé en POST AJAX
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($message === '') {
    echo json_encode(["success" => false, "error" => "Message vide"]);
    exit;
}

$classification = classify_message($message, $scoremap);
if ($classification === "hate") {
    echo json_encode(["success" => false, "error" => "Message offensant, non envoyé."]);
} else {
    // Ici, insérer dans la base si nécessaire
    echo json_encode(["success" => true, "message" => "Message envoyé avec succès."]);
}
