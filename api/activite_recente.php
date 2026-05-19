<?php
// ============================================================
// FICHIER : api/activite_recente.php
// RÔLE    : Renvoyer les 5 dernières dépenses de l'utilisateur sur tous ses groupes (pour le dashboard)
//           Renvoie : [ { id, description, amount, nom_groupe, icone_groupe }, ... ]
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start(); // J'ouvre la mémoire du serveur (la session) pour reconnaître l'utilisateur. C'est ce qui permet au serveur de se souvenir de qui est connecté entre chaque clic.

header("Content-Type: application/json"); // Je préviens le navigateur : attention, je ne vais pas te renvoyer une page web classique, mais des données brutes au format JSON pour que mon JavaScript puisse les exploiter
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) { //COMMENTAIRE EXAM
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode([]);
    exit;
} // isset = vérifie si la variable existe 
// !isset = vérifie si la variable n'existe pas 

$idUtilisateur = $_SESSION["id_utilisateur"]; // 

// Les 5 dernières dépenses dans les groupes de l'utilisateur
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
// Là, on prépare notre requête SQL avec PDO pour éviter les injections SQL. --> une interface intégrée à PHP qui sert de pont entre ton code et ta base de données.
// "Ça dit à la base de données : 'Prépare-toi à exécuter ce SELECT, 
// "je t'enverrai les variables à sécuriser juste après, au moment du execute(
$requete->execute([":user_id" => $idUtilisateur]);

$activites = $requete->fetchAll(PDO::FETCH_ASSOC); //COMMENTAIRE EXAM

// On force le montant en nombre décimal et l'identifiant en nombre entier
// "&" sert à modifier et sauvegarder directement le tableau "activites"
foreach ($activites as &$activite) {
    $activite["amount"] = (float) $activite["amount"]; // float : permet de stocker des valeurs décimales
    $activite["id"] = (int) $activite["id"]; //int : permet de stocker un nombre entier uniquement
} //COMMENTAIRE EXAM

echo json_encode($activites); // On renvoie les données au format JSON