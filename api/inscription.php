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
// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
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

// SECURITE : Anti-énumération d'utilisateurs
// Que l'utilisateur existe ou non, on affiche EXACTEMENT le même message.
$messageGenerique = "Si l'adresse est valide, un email a été envoyé avec les instructions (simulation).";

// rowCount() retourne le nombre de lignes trouvées (ici : 0 ou 1).
if ($verification->rowCount() > 0) {
    // Un compte existe déjà, on refuse l'inscription mais SANS LE DIRE au visiteur.
    $emailSimule = "À : $email\nSujet : Tentative d'inscription\nBonjour, vous avez tenté de créer un compte, mais cette adresse est déjà enregistrée. Veuillez vous connecter.";
    echo json_encode([
        "succes" => true, 
        "message" => $messageGenerique,
        "contenu_email" => $emailSimule
    ]);
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
// SECURITE : Pour ne pas différencier la réponse d'un compte existant, 
// on ne connecte PLUS automatiquement l'utilisateur ici. Il devra se connecter manuellement.
// $_SESSION["id_utilisateur"] = $nouvelId; // Ligne désactivée

$emailSimule = "À : $email\nSujet : Bienvenue !\nBonjour " . $donnees["nom"] . ", votre compte a bien été créé. Vous pouvez maintenant vous connecter.";

// --- RENVOYER LA RÉPONSE DE SUCCÈS ---
echo json_encode([
    "succes"        => true,
    "message"       => $messageGenerique,
    "contenu_email" => $emailSimule
]);
