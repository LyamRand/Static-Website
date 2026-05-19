<?php
// ============================================================
// FICHIER : api/groupe_detail.php
// RÔLE    : Renvoyer les détails d'un groupe (nom, icône, code, membres)
//           Reçoit  : ?group_id=... (dans l'URL, via GET)
//           Renvoie : { id, nom, icone, code, membres: [{id, name}, ...] }
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

if (!isset($_GET["group_id"])) {
    echo json_encode(["erreur" => "ID de groupe manquant."]);
    exit;
} // isset() vérifie si la variable existe et n'est pas NULL

$idGroupe = (int) $_GET["group_id"];

// --- 1. RÉCUPÉRATION DES INFOS DU GROUPE ---
$requeteGroupe = $pdo->prepare("SELECT id, name AS nom, logo AS icone, code FROM groups WHERE id = :group_id");
$requeteGroupe->execute([":group_id" => $idGroupe]);
// fetch() récupère UNE SEULE ligne (car un ID correspond à un seul groupe).
// S'il ne trouve rien (groupe inexistant), $groupe sera "false"
$groupe = $requeteGroupe->fetch(PDO::FETCH_ASSOC);

if (!$groupe) {
    echo json_encode(["erreur" => "Groupe introuvable."]);
    exit;
}

// --- 2. RÉCUPÉRATION DES MEMBRES DU GROUPE ---
// On lie "group_users" et "users" pour récupérer le vrai nom de la personne au lieu de juste son ID
$requeteMembres = $pdo->prepare("
    SELECT users.id, users.name
    FROM users
    JOIN group_users ON group_users.user_id = users.id
    WHERE group_users.group_id = :group_id
");
$requeteMembres->execute([":group_id" => $idGroupe]);
$membres = $requeteMembres->fetchAll(PDO::FETCH_ASSOC);

// --- 3. ASSEMBLAGE ET ENVOI AU FRONT-END ---
// Injecter le tableau des membres direct à l'intérieur du tableau principal "$groupe"
// En JSON, ça donnera : { "nom": "Coloc", "membres": [ {"name": "Alice"}, {"name": "Bob"} ] }
$groupe["membres"] = $membres;
$groupe["id"] = (int) $groupe["id"];

echo json_encode($groupe); // json_encode() transforme le tableau PHP en chaîne de caractères JSON (texte)
