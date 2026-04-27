<?php
// ============================================================
// FICHIER : api/depenses.php
// RÔLE    : Renvoyer les dépenses d'un groupe + les statistiques.
//            Reçoit  : ?groupe_id=123 (dans l'URL, via GET)
//            Renvoie : { liste: [...], stats: { total: 0 } }
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

if (!isset($_GET["groupe_id"])) {
    echo json_encode(["erreur" => "groupe_id manquant."]);
    exit;
}

$idGroupe = (int)$_GET["groupe_id"];

// Liste des dépenses avec le nom du payeur
// La colonne date s'appelle "expense_date" dans la base de données
$requeteDepenses = $pdo->prepare("
    SELECT
        e.id,
        e.amount,
        e.description,
        e.expense_date AS date,
        u.name AS nom_payeur
    FROM expenses e
    JOIN users u ON u.id = e.payer_id
    WHERE e.group_id = :groupe_id
    ORDER BY e.expense_date DESC, e.id DESC
");
$requeteDepenses->execute([":groupe_id" => $idGroupe]);
$listeDepenses = $requeteDepenses->fetchAll(PDO::FETCH_ASSOC);

foreach ($listeDepenses as &$dep) {
    $dep["id"]     = (int)$dep["id"];
    $dep["amount"] = (float)$dep["amount"];
}

// Total dépensé dans ce groupe
$requeteStats = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) AS total
    FROM expenses
    WHERE group_id = :groupe_id
");
$requeteStats->execute([":groupe_id" => $idGroupe]);
$stats = $requeteStats->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "liste" => $listeDepenses,
    "stats" => ["total" => (float)$stats["total"]]
]);
