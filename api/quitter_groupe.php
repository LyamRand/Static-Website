<?php
// ============================================================
// FICHIER : api/quitter_groupe.php
// RÔLE    : Retirer l'utilisateur d'un groupe et si le groupe devient vide, il est supprimé (ainsi que ses dépenses)
//           Reçoit  : { "groupe_id": 42 }
//           Renvoie : { "succes": true, "supprime": false }
//                     ou { "succes": true, "supprime": true } (groupe supprimé car vide)
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1); // httponly = cookie non accessible par les scripts cotés client (car si un attaquant arrive à injecter du code JS sur la page, il ne pourra pas voler les cookies
ini_set('session.cookie_secure', 1); // secure = force le HTTPS car si un attaquant arrive à intercepter les données entre le client et le serveur, il ne pourra pas voler permet de sécuriser la session 
ini_set('session.cookie_samesite', 'Strict'); // samesite = permet de sécuriser la session (ne pas envoyer le cookie à la moindre requête provenant d'un autre site)
session_start();
header("Content-Type: application/json");
require_once "config.php"; //se connecter à la base de données 

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode(["succes" => false, "message" => "Non connecté."]);
    exit;
} // si l'utilisateur n'est pas connecté, on affiche un message d'erreur
// isset = vérifie si la variable existe 
// !isset = vérifie si la variable n'existe pas 

$idUtilisateur = $_SESSION["id_utilisateur"];
$donnees = json_decode(file_get_contents("php://input"), true);

if (empty($donnees["groupe_id"])) {
    echo json_encode(["succes" => false, "message" => "groupe_id manquant."]);
    exit;
} // empty() vérifie si la variable est vide (true si vide, false si non vide)

$idGroupe = (int) $donnees["groupe_id"];

// --- 1. RETIRER L'UTILISATEUR DU GROUPE ---
// DELETE supprime la ligne qui liait cet utilisateur à ce groupe
$quitter = $pdo->prepare("DELETE FROM group_users WHERE group_id = :group_id AND user_id = :user_id");
$quitter->execute([":group_id" => $idGroupe, ":user_id" => $idUtilisateur]); // On supprime la ligne qui liait cet utilisateur à ce groupe

// --- 2. VÉRIFIER S'IL RESTE DES MEMBRES ---
// Compte combien de lignes il reste pour ce groupe dans la table de liaison
$compter = $pdo->prepare("SELECT COUNT(*) FROM group_users WHERE group_id = :group_id");
$compter->execute([":group_id" => $idGroupe]);
$nbMembres = (int) $compter->fetchColumn(); // On convertit en nombre entier

// --- 3. SUPPRESSION EN CASCADE (NETTOYAGE) ---
if ($nbMembres === 0) {
    $pdo->prepare("DELETE FROM expenses WHERE group_id = :group_id")->execute([":group_id" => $idGroupe]); // 
    $pdo->prepare("DELETE FROM groups WHERE id = :group_id")->execute([":group_id" => $idGroupe]);
    echo json_encode(["succes" => true, "supprime" => true, "message" => "Groupe supprimé car il était vide."]);
} else {
    echo json_encode(["succes" => true, "supprime" => false, "message" => "Vous avez quitté le groupe."]);
} // echo = on renvoie un message au front-end 
