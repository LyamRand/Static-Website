<?php
// ============================================================
// FICHIER : api/config.php
// RÔLE    : Se connecter à la base de données MySQL via PDO.
//           
// ============================================================

// ============================================================
// CONFIGURATION DE L'ENVIRONNEMENT (dev ou prod)
// ============================================================
$env = 'prod'; // Mettre à 'dev' en local, et 'prod' en ligne

if ($env === 'dev') {
    // SECURITE : Reporting maximal des erreurs (Développement)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1); // 
} else {
    // SECURITE : Reporting minimal des erreurs (Production = app publiée en ligne)
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// SECURITE : Forcer l'utilisation stricte de HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') { // si HTTPS n'est pas activé
    http_response_code(403); // 403 = Interdit
    die(json_encode(["succes" => false, "message" => "Connexion HTTPS obligatoire."]));
}

// Si on arrive ici, la connexion est bien en HTTPS. On ajoute le header HSTS.
header("Strict-Transport-Security: max-age=31536000");

// --- Paramètres de connexion ---
$hote = "localhost";
$baseDeDonnees = "ebus2_projet01_ttsi18";
$utilisateur = "st93ll48pppl";
$motDePasse = "l3!z0p=5zt";

// --- Tentative de connexion ---
try {
    $pdo = new PDO(
        // On crée l'objet PDO ("pont" entre PHP et MySQL) pour se connecter
        "mysql:host=$hote;dbname=$baseDeDonnees;charset=utf8mb4",
        $utilisateur,
        $motDePasse
    );

    // IMPORTANT : "PDO::ERRMODE_EXCEPTION" ne sert pas à afficher l'erreur, mais à déclencher une alarme (exception) qui sera interceptée par le bloc "catch" (sinon les erreurs SQL passeraient silencieusement)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $erreur) {
    http_response_code(500); // 500 = Erreur serveur interne

    // Affichage des erreurs selon l'environnement (dev ou prod) :
    if ($env === 'dev') {
        // En DÉVELOPPEMENT : erreurs techniques pour aider au débuggage
        echo json_encode(["erreur" => "Impossible de se connecter à la base de données : " . $erreur->getMessage()]);
    } else {
        // En PRODUCTION : message flou ou sans détail pour des raisons de sécurité
        echo json_encode(["erreur" => "Impossible de se connecter à la base de données."]);
    }
    exit;
}
