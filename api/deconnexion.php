<?php
session_start();

// 1. On vide toutes les variables de la session
$_SESSION = array();

// 2. On détruit la session côté serveur
session_destroy();

// 3. On supprime le cookie en lui donnant une date d'expiration dans le passé (ex: il y a 1 heure)
// Assurez-vous d'utiliser exactement les mêmes paramètres (nom, chemin) que lors de sa création !
if (isset($_COOKIE['remember_user'])) {
    setcookie("remember_user", "", time() - 3600, "/", "", true, true);
}

// 4. On renvoie une réponse JSON à Vue.js pour dire que c'est fait
header("Content-Type: application/json");
echo json_encode(["success" => true, "message" => "Déconnexion réussie"]);
?>