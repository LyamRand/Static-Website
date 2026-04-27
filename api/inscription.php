<?php
require_once __DIR__ . '/config.php';
// ============================================================
// FICHIER : api/inscription.php
// RÔLE    : Créer un nouveau compte utilisateur.
//            Reçoit : { "nom": "...", "email": "...", "mot_de_passe": "..." }
//            Renvoie : { "succes": true, "utilisateur": {...} }
//                   ou { "succes": false, "message": "..." }
// ============================================================

// Démarrer la session pour pouvoir connecter l'utilisateur juste après.
session_start();

// Dire au navigateur que la réponse sera du JSON.
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// --- LIRE LES DONNÉES ENVOYÉES PAR VUE.JS ---
$donnees = json_decode(file_get_contents("php://input"), true);

// --- VALIDER LES CHAMPS REÇUS ---
if (empty($donnees["nom"]) || empty($donnees["email"]) || empty($donnees["mot_de_passe"])) {
    echo json_encode(["succes" => false, "message" => "Tous les champs sont obligatoires."]);
    exit;
}

// Nettoyer l'email en supprimant les espaces en trop.
$email = trim($donnees["email"]);

// --- VÉRIFIER SI L'EMAIL EST DÉJÀ UTILISÉ ---
// On cherche si un compte existe déjà avec cet email.
$verification = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$verification->execute([":email" => $email]);

// rowCount() retourne le nombre de lignes trouvées (ici : 0 ou 1).
if ($verification->rowCount() > 0) {
    // Un compte avec cet email existe déjà, on refuse l'inscription.
    echo json_encode(["succes" => false, "message" => "Cette adresse email est déjà utilisée."]);
    exit;
}

// --- HASHER LE MOT DE PASSE ---
// On ne stocke JAMAIS le mot de passe en clair dans la base de données !
// password_hash() le transforme en une chaîne de caractères illisible (un "hash").
// Seul password_verify() (dans connexion.php) peut vérifier si un mot de passe correspond.
$motDePasseHash = password_hash($donnees["mot_de_passe"], PASSWORD_DEFAULT);

// --- INSÉRER LE NOUVEL UTILISATEUR EN BASE DE DONNÉES ---
// On prépare la requête avec des placeholders (:nom, :email, :hash) pour la sécurité.
$insertion = $pdo->prepare(
    "INSERT INTO users (name, email, pwd_hash) VALUES (:nom, :email, :hash)"
);

// execute() remplace les placeholders par les vraies valeurs et exécute la requête.
$insertion->execute([
    ":nom"   => $donnees["nom"],
    ":email" => $email,
    ":hash"  => $motDePasseHash
]);

// --- RÉCUPÉRER L'ID DU NOUVEL UTILISATEUR ---
// lastInsertId() retourne l'identifiant (ID) que MySQL vient d'attribuer
// au nouvel enregistrement que l'on vient d'insérer.
$nouvelId = $pdo->lastInsertId();

// --- CONNECTER AUTOMATIQUEMENT L'UTILISATEUR ---
// On enregistre son ID en session pour qu'il soit directement connecté.
$_SESSION["id_utilisateur"] = $nouvelId;

// --- RENVOYER LA RÉPONSE DE SUCCÈS ---
echo json_encode([
    "succes"      => true,
    "message"     => "Compte créé avec succès !",
    "utilisateur" => [
        "id"    => $nouvelId,
        "name"  => $donnees["nom"],
        "email" => $email
    ]
]);
