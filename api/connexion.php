<?php
// ============================================================
// FICHIER : api/connexion.php
// RÔLE    : Vérifier les identifiants d'un utilisateur.
//            Reçoit : { "email": "...", "mot_de_passe": "..." }
//            Renvoie : { "succes": true, "utilisateur": {...} }
//                   ou { "succes": false, "message": "..." }
// ============================================================

// --- 1. DÉMARRER LA SESSION ---
// La session permet de "mémoriser" que l'utilisateur est connecté
// d'une page à l'autre (ou d'un appel API à l'autre).
session_start();

// --- 2. ENTÊTE HTTP ---
// On dit au navigateur que la réponse sera du JSON (format de données).
header("Content-Type: application/json");
// On autorise les appels depuis n'importe quelle origine (pour le développement local).
header("Access-Control-Allow-Origin: *");

// --- 3. INCLURE LA CONNEXION À LA BASE DE DONNÉES ---
// On "inclut" config.php pour avoir accès à la variable $pdo.
require_once "config.php";

// --- 4. LIRE LES DONNÉES ENVOYÉES PAR VUE.JS ---
// Vue.js envoie les données en format JSON dans le corps (body) de la requête.
// file_get_contents("php://input") lit ce corps brut.
// json_decode(..., true) convertit le JSON en tableau PHP.
$donnees = json_decode(file_get_contents("php://input"), true);

// --- 5. VÉRIFIER QUE LES CHAMPS NÉCESSAIRES SONT PRÉSENTS ---
// On vérifie que l'email et le mot de passe ont bien été envoyés.
if (empty($donnees["email"]) || empty($donnees["mot_de_passe"])) {
    // Si un champ manque, on renvoie une erreur et on arrête le script.
    echo json_encode(["succes" => false, "message" => "Email et mot de passe requis."]);
    exit;
}

// --- 6. RÉCUPÉRER L'UTILISATEUR DANS LA BASE DE DONNÉES ---
// RÈGLE DE SÉCURITÉ : On n'insère JAMAIS une variable directement dans la requête SQL.
// On utilise un "placeholder" (:email) et on le remplace ensuite de façon sécurisée.
// Ceci évite les injections SQL (une attaque de hackers très courante).
$requete = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");

// execute() remplace :email par la valeur réelle, de façon sécurisée.
$requete->execute([":email" => $donnees["email"]]);

// fetch() récupère UNE seule ligne de résultat sous forme de tableau associatif.
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

// --- 7. VÉRIFIER LE MOT DE PASSE ---
// $utilisateur sera false si aucun compte n'est trouvé avec cet email.
// password_verify() compare le mot de passe en clair avec le hash stocké en BDD.
// On utilise la même condition pour les deux cas pour éviter d'indiquer
// si c'est l'email ou le mot de passe qui est faux (sécurité).
if (!$utilisateur || !password_verify($donnees["mot_de_passe"], $utilisateur["pwd_hash"])) {
    echo json_encode(["succes" => false, "message" => "Email ou mot de passe incorrect."]);
    exit;
}

// --- 8. ENREGISTRER L'UTILISATEUR EN SESSION ---
// Si tout est correct, on stocke l'ID de l'utilisateur dans la session.
// Cela "marque" l'utilisateur comme connecté sur le serveur.
$_SESSION["id_utilisateur"] = $utilisateur["id"];

// --- 9. RENVOYER LA RÉPONSE AU NAVIGATEUR ---
// On renvoie un message de succès et les infos de l'utilisateur (SANS le mot de passe !).
echo json_encode([
    "succes"      => true,
    "message"     => "Connexion réussie !",
    "utilisateur" => [
        "id"   => $utilisateur["id"],
        "name" => $utilisateur["name"],
        "email"=> $utilisateur["email"]
        // On ne renvoie JAMAIS le pwd_hash : c'est dangereux !
    ]
]);
