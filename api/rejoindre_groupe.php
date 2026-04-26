<?php
// ============================================================
// FICHIER : api/rejoindre_groupe.php
// RÔLE    : Ajouter l'utilisateur connecté à un groupe via son code.
//            Reçoit  : { "code": "A4KZ2B" }
//            Renvoie : { "succes": true } ou { "succes": false, "message": "..." }
// ============================================================

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

if (empty($donnees["code"])) {
    echo json_encode(["succes" => false, "message" => "Code requis."]);
    exit;
}

$code = strtoupper(trim($donnees["code"]));

// Note : dans la table "groups", le code s'appelle "code" (pas "description")
$rechercheGroupe = $pdo->prepare("SELECT id FROM groups WHERE code = :code LIMIT 1");
$rechercheGroupe->execute([":code" => $code]);
$groupe = $rechercheGroupe->fetch(PDO::FETCH_ASSOC);

if (!$groupe) {
    echo json_encode(["succes" => false, "message" => "Code de groupe invalide."]);
    exit;
}

$idGroupe = $groupe["id"];

// Vérifier que l'utilisateur n'est pas déjà membre
$verif = $pdo->prepare("SELECT 1 FROM group_users WHERE group_id = :gid AND user_id = :uid");
$verif->execute([":gid" => $idGroupe, ":uid" => $idUtilisateur]);

if ($verif->rowCount() > 0) {
    echo json_encode(["succes" => false, "message" => "Vous êtes déjà membre de ce groupe."]);
    exit;
}

// Ajouter l'utilisateur au groupe
$ajout = $pdo->prepare("INSERT INTO group_users (group_id, user_id) VALUES (:gid, :uid)");
$ajout->execute([":gid" => $idGroupe, ":uid" => $idUtilisateur]);

echo json_encode(["succes" => true, "message" => "Vous avez rejoint le groupe !"]);
