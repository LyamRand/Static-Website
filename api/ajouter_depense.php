<?php
// ============================================================
// FICHIER : api/ajouter_depense.php
// RÔLE    : Enregistrer une nouvelle dépense dans la base de données.
//           Reçoit  : { "groupe_id": 1, "payeur_id": 2, "montant": 25.50, "description": "..." }
//           Renvoie : { "succes": true } ou { "succes": false, "message": "..." }
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1); // httponly = cookie non accessible par les scripts cotés client (car si un attaquant arrive à injecter du code JS sur la page, il ne pourra pas voler les cookies
ini_set('session.cookie_secure', 1); // secure = force le HTTPS car si un attaquant arrive à intercepter les données entre le client et le serveur, il ne pourra pas voler permet de sécuriser la session 
ini_set('session.cookie_samesite', 'Strict'); // samesite = permet de sécuriser la session (ne pas envoyer le cookie à la moindre requête provenant d'un autre site)
session_start();
header("Content-Type: application/json");
require_once "config.php";

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode(["succes" => false, "message" => "Non connecté."]);
    exit;
} // isset = vérifie si la variable existe 
// !isset = vérifie si la variable n'existe pas 

// --- LECTURE DES DONNÉES ENVOYÉES PAR VUE.JS ---
$donnees = json_decode(file_get_contents("php://input"), true); // Récupère les données brutes envoyées par la requête HTTP (php://input) puis on les décode depuis le format JSON pour les transformer en un tableau PHP qu'on stocke dans $donnees pour pouvoir les manipuler facilement.

if (empty($donnees["groupe_id"]) || empty($donnees["payeur_id"]) || empty($donnees["montant"]) || empty($donnees["description"])) {
    echo json_encode(["succes" => false, "message" => "Tous les champs sont requis."]);
    exit;
}

$idGroupe = (int) $donnees["groupe_id"]; //int : permet de stocker un nombre entier uniquement
$idPayeur = (int) $donnees["payeur_id"];
$montant = (float) $donnees["montant"]; // float : permet de stocker des valeurs décimales
$description = trim($donnees["description"]); // "trim" permet de supprimer les espaces inutiles au début et à la fin du texte
$date = date("Y-m-d");

if ($montant <= 0) {
    echo json_encode(["succes" => false, "message" => "Le montant doit être supérieur à 0."]); // Si le montant est inférieur ou égal à 0, on affiche un message d'erreur et on arrête le script.
    exit;
}

// --- ENREGISTREMENT DANS LA BD ---
// On prépare la requête SQL d'insertion.
$insertion = $pdo->prepare("
        INSERT INTO expenses (group_id, payer_id, amount, description, expense_date)
        VALUES (:group_id, :payer_id, :amount, :description, :date)
    ");

// Remplacer les étiquettes (" : ") par les vraies valeurs des variables
$insertion->execute([
    ":group_id" => $idGroupe, //faire la requette pour aller chercher le nom du groupe et le mettre dans la base de donnee//
    ":payer_id" => $idPayeur,
    ":amount" => $montant,
    ":description" => $description,
    ":date" => $date
]);

// --- RÉPONSE AU FRONT-END ---
echo json_encode(["succes" => true, "message" => "Dépense ajoutée !"]);
