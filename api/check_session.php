<?php
// check_auth.php
header("Content-Type: application/json");
session_start();

// On vérifie si l'utilisateur a une session active
if (isset($_SESSION['idClient']) && isset($_SESSION['user_name'])) {
    // L'utilisateur est connecté, on renvoie ses infos
    echo json_encode([
        "isLoggedIn" => true,
        "user" => [
            "id" => $_SESSION['idClient'],
            "name" => $_SESSION['user_name']
        ]
    ]);
} else {
    // Optionnel : Ici vous pourriez aussi vérifier si le cookie $_COOKIE['remember_user'] existe
    // pour reconnecter l'utilisateur automatiquement et recréer la session.
    
    // L'utilisateur n'est pas connecté
    echo json_encode([
        "isLoggedIn" => false,
        "user" => null
    ]);
}
?>