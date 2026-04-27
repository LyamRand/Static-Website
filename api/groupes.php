<?php
// ============================================================
// FICHIER : api/groupes.php
// RÔLE    : Renvoyer la liste des groupes de l'utilisateur connecté.
//            Renvoie : [ { id, nom, icone, participants, solde, code }, ... ]
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

$idUtilisateur = $_SESSION["id_utilisateur"];

// Étape 1 — Récupérer la liste des groupes de l'utilisateur (id, nom, icone, code)
$req = $pdo->prepare("
    SELECT g.id, g.name AS nom, g.logo AS icone, g.code
    FROM groups g
    JOIN group_users gu ON gu.group_id = g.id
    WHERE gu.user_id = :id
    ORDER BY g.id DESC
");
$req->execute([":id" => $idUtilisateur]);
$groupes = $req->fetchAll(PDO::FETCH_ASSOC);

// Étape 2 — Pour chaque groupe, on calcule le nombre de membres et le solde en PHP
// Formule du solde : ce que j'ai payé  -  (total du groupe ÷ nb membres)
foreach ($groupes as &$groupe) {
    $gid = $groupe["id"];

    // Nombre de membres dans ce groupe
    $req = $pdo->prepare("SELECT COUNT(*) FROM group_users WHERE group_id = :gid");
    $req->execute([":gid" => $gid]);
    $nbMembres = (int) $req->fetchColumn();

    // Ce que MOI j'ai payé dans ce groupe
    $req = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE group_id = :gid AND payer_id = :uid");
    $req->execute([":gid" => $gid, ":uid" => $idUtilisateur]);
    $jaiPaye = (float) $req->fetchColumn();

    // Total de toutes les dépenses du groupe
    $req = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE group_id = :gid");
    $req->execute([":gid" => $gid]);
    $totalGroupe = (float) $req->fetchColumn();

    // Ma part égale = total ÷ nb membres (si groupe non vide)
    $maPartEgale = $nbMembres > 0 ? $totalGroupe / $nbMembres : 0;

    $groupe["participants"] = $nbMembres;
    $groupe["solde"]        = round($jaiPaye - $maPartEgale, 2);
}

echo json_encode($groupes);
