<?php
// ============================================================
// FICHIER : api/depenses.php
// RÔLE    : Renvoyer les dépenses d'un groupe + les statistiques
//           Reçoit  : ?groupe_id=123 (dans l'URL, via GET)
//           Renvoie : { liste: [...], stats: { total: 0 } }
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1); // httponly = cookie non accessible par les scripts cotés client (car si un attaquant arrive à injecter du code JS sur la page, il ne pourra pas voler les cookies
ini_set('session.cookie_secure', 1); // secure = force le HTTPS car si un attaquant arrive à intercepter les données entre le client et le serveur, il ne pourra pas voler permet de sécuriser la session 
ini_set('session.cookie_samesite', 'Strict'); // samesite = permet de sécuriser la session (ne pas envoyer le cookie à la moindre requête provenant d'un autre site)
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode(["erreur" => "Non connecté."]);
    exit;
}

// --- LECTURE DU PARAMÈTRE DANS L'URL ---
// $_GET permet de lire les données qui sont envoyées directement dans l'adresse web (URL)
if (!isset($_GET["groupe_id"])) {
    echo json_encode(["erreur" => "groupe_id manquant."]);
    exit;
}

$idGroupe = (int) $_GET["groupe_id"];

// --- 1. RÉCUPÉRATION DE LA LISTE DES DÉPENSES ---
// JOIN pour lier la table "expenses" (qui contient les dépenses) à la table "users" (qui contient les utilisateurs).
// Permet d'afficher le "nom" de la personne plutôt que juste son ID de payeur (payer_id)
$requeteDepenses = $pdo->prepare("
    SELECT
        expenses.id,
        expenses.amount,
        expenses.description,
        expenses.expense_date AS date,
        users.name AS nom_payeur
    FROM expenses
    JOIN users ON users.id = expenses.payer_id
    WHERE expenses.group_id = :group_id
    ORDER BY expenses.expense_date DESC, expenses.id DESC
");
$requeteDepenses->execute([":group_id" => $idGroupe]);
$listeDepenses = $requeteDepenses->fetchAll(PDO::FETCH_ASSOC);
//"Avec fetchAll, on récupère TOUTES les dépenses d'un coup"
// "sous forme de tableau associatif PHP, et on stocke le tout"
// "dans $listeDepenses pour pouvoir les lister ou faire une boucle."

foreach ($listeDepenses as &$dep) {
    $dep["id"] = (int) $dep["id"];
    $dep["amount"] = (float) $dep["amount"];
}

// --- 2. CALCUL DU TOTAL DÉPENSÉ DANS LE GROUPE ---
// SUM(amount) = additionner tous les montants
// COALESCE(..., 0) = Si le groupe n'a aucune dépense (résultat vide), renvoier 0 au lieu de NULL
$requeteStats = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) AS total
    FROM expenses
    WHERE group_id = :group_id
");
$requeteStats->execute([":group_id" => $idGroupe]);
$stats = $requeteStats->fetch(PDO::FETCH_ASSOC);
$total = (float) $stats["total"];

// --- 3. CALCUL DU NOMBRE DE MEMBRES ET PART ÉGALE ---
// COUNT(*) = compter le nombre de lignes trouvées
// fetchColumn() : au lieu de renvoyer un tableau comme fetchAll(), il renvoie direct le nombre (la valeur de la première colonne)
$requeteMembres = $pdo->prepare("SELECT COUNT(*) FROM group_users WHERE group_id = :group_id");
$requeteMembres->execute([":group_id" => $idGroupe]);
$nbMembres = (int) $requeteMembres->fetchColumn();

// Part égale par personne (total divisé par nb membres)
$partParPersonne = $nbMembres > 0 ? round($total / $nbMembres, 2) : 0;

// Ce que l'utilisateur connecté a payé dans ce groupe
$idUtilisateur = $_SESSION["id_utilisateur"];
$requeteMonPaye = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) FROM expenses
    WHERE group_id = :group_id AND payer_id = :user_id
");
$requeteMonPaye->execute([":group_id" => $idGroupe, ":user_id" => $idUtilisateur]);
$jaiPaye = (float) $requeteMonPaye->fetchColumn();

// Solde = ce que j'ai payé - ma part égale
// Positif = les autres me doivent, Négatif = je dois aux autres
$monSolde = round($jaiPaye - $partParPersonne, 2);

// --- 4. CALCUL DU SOLDE GLOBAL POUR CHAQUE MEMBRE ---
// LEFT JOIN = liste TOUS les membres du groupe, regarde ce que chacun a payé, et additionne tout
$requeteSoldes = $pdo->prepare("
    SELECT 
        users.id, 
        users.name AS nom, 
        COALESCE(SUM(expenses.amount), 0) AS total_paye
    FROM group_users
    JOIN users ON group_users.user_id = users.id
    LEFT JOIN expenses ON expenses.payer_id = users.id AND expenses.group_id = group_users.group_id
    WHERE group_users.group_id = :group_id
    GROUP BY users.id, users.name
");
$requeteSoldes->execute([":group_id" => $idGroupe]);
$membres = $requeteSoldes->fetchAll(PDO::FETCH_ASSOC);

$soldesMembres = [];
foreach ($membres as $membre) {
    $totalPaye = (float) $membre["total_paye"];
    $solde = round($totalPaye - $partParPersonne, 2);
    $soldesMembres[] = [
        "id" => (int) $membre["id"],
        "nom" => $membre["nom"],
        "total_paye" => $totalPaye,
        "solde" => $solde
    ];
}

echo json_encode([
    "liste" => $listeDepenses,
    "stats" => [
        "total" => $total,
        "nb_membres" => $nbMembres,
        "mon_solde" => $monSolde,
        "soldes_membres" => $soldesMembres
    ]
]);