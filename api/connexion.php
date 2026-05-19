<?php
require_once __DIR__ . '/config.php';
// ============================================================
// FICHIER : api/connexion.php
// RÔLE    : Vérifier les identifiants d'un utilisateur.
//           Reçoit : { "email": "...", "mot_de_passe": "..." }
//           Renvoie : { "succes": true, "utilisateur": {...} } ou { "succes": false, "message": "..." }
// ============================================================

// --- 1. ENTÊTE HTTP ---
header("Content-Type: application/json"); // Dire au navigateur que la réponse sera du JSON (format de données)
header("Access-Control-Allow-Origin: *"); // On autorise les appels depuis n'importe quelle origine (pour le développement local)

// --- 2. LIRE LES DONNÉES ENVOYÉES PAR VUE.JS ---
// Vue.js envoie les données en format JSON dans le corps (body) de la requête et << file_get_contents("php://input") >> lit ce corps brut
// json_decode(..., true) convertit le JSON en tableau PHP
$donnees = json_decode(file_get_contents("php://input"), true);

// --- 3. VÉRIFIER QUE LES CHAMPS NÉCESSAIRES SONT PRÉSENTS ---
// On vérifie que l'email et le mot de passe ont bien été envoyés
if (empty($donnees["email"]) || empty($donnees["mot_de_passe"])) {
    // Si un champ manque, on renvoie une erreur et on arrête le script
    echo json_encode(["succes" => false, "message" => "Email et mot de passe requis."]);
    exit;
}

// --- 4. RÉCUPÉRER L'UTILISATEUR DANS LA BASE DE DONNÉES ---
// SÉCURITÉ : On n'insère JAMAIS une variable directement dans la requête SQL
// On utilise un "placeholder" (:email) et on le remplace ensuite de façon sécurisée (PDO) pour éviter les injections SQL (hack)
$requete = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");

// execute() remplace :email par la valeur réelle
$requete->execute([":email" => $donnees["email"]]);

// fetch() récupère les infos de l'utilisateur avec UNE seule ligne de la BD
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

// --- 5. VÉRIFIER LE MOT DE PASSE ---
// $utilisateur sera false si aucun compte n'est trouvé avec cet email
// password_verify() compare le mot de passe en clair avec le hash stocké en BD
// SÉCURITÉ : On utilise la même condition pour les deux cas pour éviter d'indiquer si c'est l'email ou le mot de passe qui est faux
if (!$utilisateur || !password_verify($donnees["mot_de_passe"], $utilisateur["pwd_hash"])) {
    echo json_encode(["succes" => false, "message" => "Email ou mot de passe incorrect."]);
    exit;
}

// --- 6. ENREGISTRER L'UTILISATEUR EN SESSION ---
// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
// On crée le cookie UNIQUEMENT maintenant que l'utilisateur est validé
ini_set('session.cookie_httponly', 1); // httponly = cookie non accessible par les scripts cotés client (car si un attaquant arrive à injecter du code JS sur la page, il ne pourra pas voler les cookies
ini_set('session.cookie_secure', 1); // secure = force le HTTPS car si un attaquant arrive à intercepter les données entre le client et le serveur, il ne pourra pas voler permet de sécuriser la session 
ini_set('session.cookie_samesite', 'Strict'); // samesite = permet de sécuriser la session (ne pas envoyer le cookie à la moindre requête provenant d'un autre site)
session_start();

$_SESSION["id_utilisateur"] = $utilisateur["id"]; // Si tout est correct, on stocke l'ID de l'utilisateur dans la session et "marque" l'utilisateur comme connecté sur le serveur

// --- 7. RENVOYER LA RÉPONSE AU NAVIGATEUR ---
// On renvoie un message de succès et les infos de l'utilisateur
echo json_encode([
    "succes" => true,
    "message" => "Connexion réussie !",
    "utilisateur" => [
        "id" => $utilisateur["id"],
        "name" => $utilisateur["name"],
        "email" => $utilisateur["email"]
    ]
]);
