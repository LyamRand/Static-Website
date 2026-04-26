<?php
// ============================================================
// FICHIER : api/groupe_detail.php
// RÔLE    : Renvoyer les détails d'un groupe (nom, icône, code, membres).
//            Reçoit  : ?id=123 (dans l'URL, via GET)
//            Renvoie : { id, nom, icone, code, membres: [{id, name}, ...] }
// ============================================================

session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401);
    echo json_encode(["erreur" => "Non connecté."]);
    exit;
}

if (!isset($_GET["id"])) {
    echo json_encode(["erreur" => "ID de groupe manquant."]);
    exit;
}

$idGroupe = (int)$_GET["id"];

// Note : dans la table "groups", le code d'invitation s'appelle "code" (pas "description")
$requeteGroupe = $pdo->prepare("SELECT id, name AS nom, logo AS icone, code FROM groups WHERE id = :id");
$requeteGroupe->execute([":id" => $idGroupe]);
$groupe = $requeteGroupe->fetch(PDO::FETCH_ASSOC);

if (!$groupe) {
    echo json_encode(["erreur" => "Groupe introuvable."]);
    exit;
}

// Liste des membres du groupe
$requeteMembres = $pdo->prepare("
    SELECT u.id, u.name
    FROM users u
    JOIN group_users gu ON gu.user_id = u.id
    WHERE gu.group_id = :groupe_id
");
$requeteMembres->execute([":groupe_id" => $idGroupe]);
$membres = $requeteMembres->fetchAll(PDO::FETCH_ASSOC);

$groupe["membres"] = $membres;
$groupe["id"]      = (int)$groupe["id"];

echo json_encode($groupe);
