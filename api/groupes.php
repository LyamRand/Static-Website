<?php
// ============================================================
// FICHIER : api/groupes.php
// RÔLE    : Renvoyer la liste des groupes de l'utilisateur connecté
//           Renvoie : [ { id, nom, icone, participants, solde, code }, ... ]
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
//ini_set() permet de modifier les paramètres de PHP.
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode(["erreur" => "Non connecté."]);
    exit;
}

$idUtilisateur = $_SESSION["id_utilisateur"];

// --- ÉTAPE 1 : RÉCUPÉRER LA LISTE DES GROUPES ---
// JOIN permet de trouver tous les groupes auxquels l'utilisateur participe
// ORDER BY groups.id DESC permet de trier du plus récent au plus ancien
$req = $pdo->prepare("
    SELECT groups.id, groups.name AS nom, groups.logo AS icone, groups.code
    FROM groups
    JOIN group_users ON group_users.group_id = groups.id
    WHERE group_users.user_id = :user_id
    ORDER BY groups.id DESC
");
$req->execute([":user_id" => $idUtilisateur]);
$groupes = $req->fetchAll(PDO::FETCH_ASSOC);

// --- ÉTAPE 2 : CALCULER LES STATS POUR CHAQUE GROUPE ---
// On fait une boucle (foreach) sur chaque groupe trouvé
// Formule du solde : ce que j'ai payé  -  (total du groupe ÷ nb membres)
foreach ($groupes as &$groupe) {
    $idGroupe = $groupe["id"];

    // Nombre de membres dans ce groupe
    $req = $pdo->prepare("SELECT COUNT(*) FROM group_users WHERE group_id = :group_id");
    $req->execute([":group_id" => $idGroupe]);
    $nbMembres = (int) $req->fetchColumn(); 

    // Ce que moi j'ai payé dans ce groupe
    $req = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE group_id = :group_id AND payer_id = :user_id");
    $req->execute([":group_id" => $idGroupe, ":user_id" => $idUtilisateur]);
    $jaiPaye = (float) $req->fetchColumn(); //COALESCE est utilisé pour renvoyer 0 si aucune dépense n'est trouvée pour éviter une erreur

    // Total de toutes les dépenses du groupe
    $req = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE group_id = :group_id");
    $req->execute([":group_id" => $idGroupe]);
    $totalGroupe = (float) $req->fetchColumn();  // COALESCE est utilisé pour renvoyer 0 si aucune dépense n'est trouvée pour éviter une erreur

    // --- ÉTAPE 3 : CALCUL DE LA PART ÉGALE ---
    $maPartEgale = $nbMembres > 0 ? $totalGroupe / $nbMembres : 0;

    $groupe["participants"] = $nbMembres;
    $groupe["solde"] = round($jaiPaye - $maPartEgale, 2); // On arrondit le solde à 2 décimales 
}

echo json_encode($groupes); 
