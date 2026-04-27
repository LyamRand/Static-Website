<?php
// ============================================================
// FICHIER : api/config.php
// RÔLE    : Se connecter à la base de données MySQL via PDO.
//            Ce fichier est inclus par TOUS les autres scripts PHP.
// ============================================================

// SECURITE : Reporting maximal des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// SECURITE : Forcer l'utilisation stricte de HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    http_response_code(403); // 403 = Interdit
    die(json_encode(["succes" => false, "message" => "Connexion HTTPS obligatoire."]));
}

// Si on arrive ici, la connexion est bien en HTTPS. On ajoute le header HSTS.
header("Strict-Transport-Security: max-age=31536000");

// --- Paramètres de connexion (à adapter selon votre serveur) ---
$hote = "localhost";       // Adresse du serveur MySQL (MAMP = localhost)
$baseDeDonnees = "ebus2_projet01_ttsi18"; // Nom de la base de données
$utilisateur = "st93ll48pppl";       // Nom d'utilisateur MySQL (MAMP = root)
$motDePasse = "l3!z0p=5zt";       // Mot de passe MySQL (MAMP = root)

// --- Tentative de connexion ---
// On utilise un bloc try/catch pour "essayer" et "attraper" l'erreur si ça plante.
try {
    // On crée l'objet PDO (PHP Data Objects) pour se connecter.
    // C'est notre "pont" entre PHP et MySQL.
    $pdo = new PDO(
        "mysql:host=$hote;dbname=$baseDeDonnees;charset=utf8mb4",
        $utilisateur,
        $motDePasse
    );

    // IMPORTANT : On demande à PDO de lancer une exception (erreur) si une
    // requête SQL échoue. Sans ça, les erreurs passeraient silencieusement.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $erreur) {
    // Si la connexion a échoué, on arrête tout et on affiche l'erreur.
    // En production, on éviterait d'afficher les détails (sécurité).
    http_response_code(500); // Code HTTP 500 = Erreur serveur interne
    echo json_encode(["erreur" => "Impossible de se connecter à la base de données : " . $erreur->getMessage()]);
    exit; // On arrête l'exécution du script
}
