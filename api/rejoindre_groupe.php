<?php
// ============================================================
// FICHIER : api/rejoindre_groupe.php
// RÔLE    : Ajouter l'utilisateur connecté à un groupe via son code
//           Reçoit  : { "code": "A4KZ2B" }
//           Renvoie : { "succes": true } ou { "succes": false, "message": "..." }
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1); 
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode(["succes" => false, "message" => "Non connecté."]);
    exit;
} // isset = vérifie si la variable existe 
// !isset = vérifie si la variable n'existe pas 

$idUtilisateur = $_SESSION["id_utilisateur"];
$donnees = json_decode(file_get_contents("php://input"), true); // je décode les données envoyées par l'utilisateur

if (empty($donnees["code"])) {
    echo json_encode(["succes" => false, "message" => "Code requis."]); // j'envoie un message d'erreur au navigateur
    exit;
}

$code = strtoupper(trim($donnees["code"])); // je stocke le code en majuscules et je retire les espaces

// --- 1. RECHERCHE DU NUMÉRO DE GROUPE ENTRÉ ---
// LIMIT 1 permet d'optimiser la recherche et MySQL s'arrête dès qu'il a trouvé le premier groupe.
$rechercheGroupe = $pdo->prepare("SELECT id FROM groups WHERE code = :code LIMIT 1");
$rechercheGroupe->execute([":code" => $code]);
$groupe = $rechercheGroupe->fetch(PDO::FETCH_ASSOC);

if (!$groupe) { // Si le groupe n'est pas trouvé
    echo json_encode(["succes" => false, "message" => "Code de groupe invalide."]); // j'envoie un message d'erreur au navigateur
    exit;
} 

$idGroupe = $groupe["id"]; // je stocke l'id du groupe

// --- 2. VÉRIFICATION QUE L'UTILISATEUR N'EST PAS DÉJÀ MEMBRE DU GROUPE ---
// Astuce de "SELECT 1" : au lieu de demander "SELECT id" (qui charge des données), on demande à MySQL de juste répondre "1" (Vrai) s'il trouve la ligne (plus rapide)
$verif = $pdo->prepare("SELECT 1 FROM group_users WHERE group_id = :group_id AND user_id = :user_id");
$verif->execute([":group_id" => $idGroupe, ":user_id" => $idUtilisateur]);

if ($verif->rowCount() > 0) { // si l'utilisateur est déjà membre du groupe
    echo json_encode(["succes" => false, "message" => "Vous êtes déjà membre de ce groupe."]); // j'envoie un message d'erreur au navigateur
    exit; // j'arrête L'exécution
}

// --- 3. AJOUT AU GROUPE ---
// Vérification OK donc on insère la liaison dans la table group_users
$ajout = $pdo->prepare("INSERT INTO group_users (group_id, user_id) VALUES (:group_id, :user_id)");
$ajout->execute([":group_id" => $idGroupe, ":user_id" => $idUtilisateur]);

echo json_encode(["succes" => true, "message" => "Vous avez rejoint le groupe !"]);
