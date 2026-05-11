<?php
// ============================================================
// FICHIER : api/activite_recente.php
// RÔLE    : Renvoyer les 5 dernières dépenses de l'utilisateur
//            sur tous ses groupes (pour le widget du dashboard).
//            Renvoie : [ { id, description, amount, nom_groupe, icone_groupe }, ... ]
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
    echo json_encode([]);
    exit;
}

$idUtilisateur = $_SESSION["id_utilisateur"];

// Les 5 dernières dépenses dans les groupes de l'utilisateur.
// GROUP BY expenses.id évite les doublons (un groupe avec plusieurs membres donnait plusieurs lignes).
// La colonne date s'appelle "expense_date" dans la base de données.
$requete = $pdo->prepare("
    SELECT
        expenses.id,
        expenses.amount,
        expenses.description,
        groups.name AS nom_groupe,
        groups.logo AS icone_groupe
    FROM expenses
    JOIN groups ON groups.id = expenses.group_id
    JOIN group_users ON group_users.group_id = expenses.group_id AND group_users.user_id = :user_id
    ORDER BY expenses.expense_date DESC, expenses.id DESC
    LIMIT 5
");
$requete->execute([":user_id" => $idUtilisateur]);

$activites = $requete->fetchAll(PDO::FETCH_ASSOC);

foreach ($activites as &$activite) {
    $activite["amount"] = (float) $activite["amount"];
    $activite["id"] = (int) $activite["id"];
}

echo json_encode($activites);
