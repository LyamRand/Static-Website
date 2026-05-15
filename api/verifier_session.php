<?php
// ============================================================
// FICHIER : api/verifier_session.php
// RÔLE    : Vérifier si l'utilisateur a déjà une session active (appelé au démarrage de l'app pour éviter de repasser par l'accueil après un F5)
//           Renvoie : { "connecte": true, "utilisateur": {...} }
//                     ou { "connecte": false }
// ============================================================

// SECURITE : Ne pas recréer un cookie vide inutilement (Consigne du prof)
if (!isset($_COOKIE[session_name()])) {
    header("Content-Type: application/json");
    echo json_encode(["connecte" => false]);
    exit;
}

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

// --- DOUBLE VÉRIFICATION (SÉCURITÉ) ---
// L'utilisateur a une session PHP valide mais on vérifie quand même s'il existe toujours dans la base de données
// Pourquoi ? Si connecté, puis qu'on a supprimé son compte de la base, sa session PHP serait encore active
$requete = $pdo->prepare("SELECT id, name, email FROM users WHERE id = :user_id LIMIT 1");
$requete->execute([":user_id" => $_SESSION["id_utilisateur"]]);
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    // --- CAS SPÉCIAL ---
    // La session dit "il est connecté", mais MySQL dit "cet utilisateur n'existe plus"
    // On détruit immédiatement la session par sécurité
    session_destroy();
    echo json_encode(["connecte" => false]);
    exit;
}

echo json_encode([
    "connecte" => true,
    "utilisateur" => $utilisateur
]);
