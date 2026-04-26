<?php
// ============================================================
// FICHIER : api/creer_groupe.php
// RÔLE    : Créer un nouveau groupe et y ajouter le créateur.
//            Reçoit  : { "nom": "...", "icone": "🏠" }
//            Renvoie : { "succes": true, "groupe_id": 42, "code": "A4KZ2B" }
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

if (empty($donnees["nom"])) {
    echo json_encode(["succes" => false, "message" => "Le nom du groupe est obligatoire."]);
    exit;
}

$nomGroupe   = trim($donnees["nom"]);
$iconeGroupe = $donnees["icone"] ?? "💬";

// Générer un code unique d'invitation (6 caractères)
$codeUnique = strtoupper(substr(str_shuffle(uniqid()), 0, 6));

// Note : dans la table "groups", le code s'appelle "code" (pas "description")
$insertion = $pdo->prepare("
    INSERT INTO groups (name, logo, code)
    VALUES (:nom, :logo, :code)
");
$insertion->execute([
    ":nom"  => $nomGroupe,
    ":logo" => $iconeGroupe,
    ":code" => $codeUnique
]);

$idNouveauGroupe = $pdo->lastInsertId();

// Ajouter le créateur comme premier membre
$ajoutMembre = $pdo->prepare("
    INSERT INTO group_users (group_id, user_id)
    VALUES (:groupe_id, :user_id)
");
$ajoutMembre->execute([
    ":groupe_id" => $idNouveauGroupe,
    ":user_id"   => $idUtilisateur
]);

echo json_encode([
    "succes"    => true,
    "groupe_id" => (int)$idNouveauGroupe,
    "code"      => $codeUnique
]);
