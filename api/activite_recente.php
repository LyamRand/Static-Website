<?php
// ============================================================
// FICHIER : api/activite_recente.php
// RÔLE    : Renvoyer les 5 dernières dépenses de l'utilisateur
//            sur tous ses groupes (pour le widget du dashboard).
//            Renvoie : [ { id, description, amount, nom_groupe, icone_groupe }, ... ]
// ============================================================

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
// GROUP BY e.id évite les doublons (un groupe avec plusieurs membres donnait plusieurs lignes).
// La colonne date s'appelle "expense_date" dans la base de données.
$requete = $pdo->prepare("
    SELECT
        e.id,
        e.amount,
        e.description,
        g.name AS nom_groupe,
        g.logo AS icone_groupe
    FROM expenses e
    JOIN groups g ON g.id = e.group_id
    JOIN group_users gu ON gu.group_id = e.group_id AND gu.user_id = :id
    GROUP BY e.id
    ORDER BY e.expense_date DESC, e.id DESC
    LIMIT 5
");
$requete->execute([":id" => $idUtilisateur]);

$activites = $requete->fetchAll(PDO::FETCH_ASSOC);

foreach ($activites as &$activite) {
    $activite["amount"] = (float)$activite["amount"];
    $activite["id"]     = (int)$activite["id"];
}

echo json_encode($activites);
