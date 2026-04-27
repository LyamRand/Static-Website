<?php
// ============================================================
// FICHIER : api/verifier_session.php
// RÔLE    : Vérifier si l'utilisateur a déjà une session active.
//            Appelé au démarrage de l'app pour éviter de repasser
//            par l'accueil après un F5.
//            Renvoie : { "connecte": true, "utilisateur": {...} }
//                   ou { "connecte": false }
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    echo json_encode(["connecte" => false]);
    exit;
}

// L'utilisateur a une session → on récupère ses infos depuis la BDD
$requete = $pdo->prepare("SELECT id, name, email FROM users WHERE id = :id LIMIT 1");
$requete->execute([":id" => $_SESSION["id_utilisateur"]]);
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    // L'utilisateur n'existe plus en BDD → on détruit la session
    session_destroy();
    echo json_encode(["connecte" => false]);
    exit;
}

echo json_encode([
    "connecte"    => true,
    "utilisateur" => $utilisateur
]);
