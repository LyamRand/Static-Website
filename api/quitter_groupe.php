<?php
// ============================================================
// FICHIER : api/quitter_groupe.php
// RÔLE    : Retirer l'utilisateur d'un groupe.
//            Si le groupe devient vide, il est supprimé (ainsi que ses dépenses).
//            Reçoit  : { "groupe_id": 42 }
//            Renvoie : { "succes": true, "supprime": false }
//                   ou { "succes": true, "supprime": true }  (groupe supprimé car vide)
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401);
    echo json_encode(["succes" => false, "message" => "Non connecté."]);
    exit;
}

$idUtilisateur = $_SESSION["id_utilisateur"];
$donnees = json_decode(file_get_contents("php://input"), true);

if (empty($donnees["groupe_id"])) {
    echo json_encode(["succes" => false, "message" => "groupe_id manquant."]);
    exit;
}

$idGroupe = (int)$donnees["groupe_id"];

// Retirer l'utilisateur du groupe
$quitter = $pdo->prepare("DELETE FROM group_users WHERE group_id = :gid AND user_id = :uid");
$quitter->execute([":gid" => $idGroupe, ":uid" => $idUtilisateur]);

// Vérifier s'il reste encore des membres dans ce groupe
$compter = $pdo->prepare("SELECT COUNT(*) FROM group_users WHERE group_id = :gid");
$compter->execute([":gid" => $idGroupe]);
$nbMembres = (int)$compter->fetchColumn();

if ($nbMembres === 0) {
    // Plus personne dans le groupe → supprimer les dépenses puis le groupe
    $pdo->prepare("DELETE FROM expenses WHERE group_id = :gid")->execute([":gid" => $idGroupe]);
    $pdo->prepare("DELETE FROM groups WHERE id = :gid")->execute([":gid" => $idGroupe]);
    echo json_encode(["succes" => true, "supprime" => true, "message" => "Groupe supprimé car il était vide."]);
} else {
    echo json_encode(["succes" => true, "supprime" => false, "message" => "Vous avez quitté le groupe."]);
}
