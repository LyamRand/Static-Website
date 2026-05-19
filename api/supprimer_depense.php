<?php
// ============================================================
// FICHIER : api/supprimer_depense.php
// RÔLE    : Supprimer une dépense par son ID.
//           Reçoit  : { "id": 42 }
//           Renvoie : { "succes": true } ou { "succes": false, "message": "..." }
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1); // httponly = cookie non accessible par les scripts cotés client (car si un attaquant arrive à injecter du code JS sur la page, il ne pourra pas voler les cookies
ini_set('session.cookie_secure', 1); // secure = force le HTTPS car si un attaquant arrive à intercepter les données entre le client et le serveur, il ne pourra pas voler force le HTTPS car si un attaquant arrive à intercepter les données entre le client et le serveur, il ne pourra pas voler
ini_set('session.cookie_samesite', 'Strict'); // samesite = permet de sécuriser la session (ne pas envoyer le cookie à la moindre requête provenant d'un autre site)
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode(["succes" => false, "message" => "Non connecté."]);
    exit;
}
// isset = vérifie si la variable existe 
// !isset = vérifie si la variable n'existe pas 

$donnees = json_decode(file_get_contents("php://input"), true);

// Vérifier que l'ID est présent
if (empty($donnees["id"])) {
    echo json_encode(["succes" => false, "message" => "ID de dépense manquant."]);
    exit;
}

$idDepense = (int) $donnees["id"];

// --- SUPPRIMER LA DÉPENSE ---
// DELETE FROM supprime la ligne et WHERE id = :id cible uniquement cette dépense
$suppression = $pdo->prepare("DELETE FROM expenses WHERE id = :expense_id");
$suppression->execute([":expense_id" => $idDepense]);

// rowCount() vérifie si une ligne a bien été supprimée
if ($suppression->rowCount() > 0) {
    echo json_encode(["succes" => true, "message" => "Dépense supprimée."]);
} else {
    echo json_encode(["succes" => false, "message" => "Dépense introuvable."]);
}
