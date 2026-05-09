<?php
// ============================================================
// FICHIER : api/groupe_detail.php
// RÔLE    : Renvoyer les détails d'un groupe (nom, icône, code, membres).
//            Reçoit  : ?group_id=123 (dans l'URL, via GET)
//            Renvoie : { id, nom, icone, code, membres: [{id, name}, ...] }
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
    echo json_encode(["erreur" => "Non connecté."]);
    exit;
}

if (!isset($_GET["group_id"])) {
    echo json_encode(["erreur" => "ID de groupe manquant."]);
    exit;
}

$idGroupe = (int)$_GET["group_id"];

// Note : dans la table "groups", le code d'invitation s'appelle "code" (pas "description")
$requeteGroupe = $pdo->prepare("SELECT id, name AS nom, logo AS icone, code FROM groups WHERE id = :group_id");
$requeteGroupe->execute([":group_id" => $idGroupe]);
$groupe = $requeteGroupe->fetch(PDO::FETCH_ASSOC);

if (!$groupe) {
    echo json_encode(["erreur" => "Groupe introuvable."]);
    exit;
}

// Liste des membres du groupe
$requeteMembres = $pdo->prepare("
    SELECT users.id, users.name
    FROM users
    JOIN group_users ON group_users.user_id = users.id
    WHERE group_users.group_id = :group_id
");
$requeteMembres->execute([":group_id" => $idGroupe]);
$membres = $requeteMembres->fetchAll(PDO::FETCH_ASSOC);

$groupe["membres"] = $membres;
$groupe["id"]      = (int)$groupe["id"];

echo json_encode($groupe);
