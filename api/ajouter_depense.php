<?php
// ============================================================
// FICHIER : api/ajouter_depense.php
// RÔLE    : Enregistrer une nouvelle dépense dans la base de données.
//            Reçoit  : { "groupe_id": 1, "payeur_id": 2, "montant": 25.50, "description": "..." }
//            Renvoie : { "succes": true } ou { "succes": false, "message": "..." }
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

$donnees = json_decode(file_get_contents("php://input"), true);

if (empty($donnees["groupe_id"]) || empty($donnees["payeur_id"]) || empty($donnees["montant"]) || empty($donnees["description"])) {
    echo json_encode(["succes" => false, "message" => "Tous les champs sont requis."]);
    exit;
}

$idGroupe    = (int)$donnees["groupe_id"];
$idPayeur    = (int)$donnees["payeur_id"];
$montant     = (float)$donnees["montant"];
$description = trim($donnees["description"]);
$date        = date("Y-m-d");

if ($montant <= 0) {
    echo json_encode(["succes" => false, "message" => "Le montant doit être supérieur à 0."]);
    exit;
}

// La colonne date s'appelle "expense_date" dans la base de données
$insertion = $pdo->prepare("
    INSERT INTO expenses (group_id, payer_id, amount, description, expense_date)
    VALUES (:groupe_id, :payer_id, :amount, :description, :date)
");
$insertion->execute([
    ":groupe_id"   => $idGroupe,
    ":payer_id"    => $idPayeur,
    ":amount"      => $montant,
    ":description" => $description,
    ":date"        => $date
]);

echo json_encode(["succes" => true, "message" => "Dépense ajoutée !"]);
