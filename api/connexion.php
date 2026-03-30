<?php
// Note : Si vous utilisez des redirections (Location), ces headers JSON/CORS ne sont généralement pas nécessaires
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// ------ connexion à la base de données -----------------------

include('./config.php');

// ------ récupération des données du formulaire ----------------

$email = $_POST['mail'] ?? '';
$mp = $_POST['mdp'] ?? '';

// ------ préparation de la requête -----------------------------

$req = $pdo->prepare('SELECT * FROM users WHERE email = :email');

// ------ insertion des paramètres ds la requête ----------------

$req->bindValue(':email', $email, PDO::PARAM_STR);

// ------ exécution de la requête -------------------------------

$req->execute();
$data = $req->fetch(PDO::FETCH_ASSOC);
$req->closeCursor(); // Termine le traitement de la requête

if ($data && password_verify($mp, $data['pwd_hash'])) {
    // Login successful
    session_start();
    $_SESSION['idClient'] = $data['id'];
    $_SESSION['user_name'] = $data['name'];
    
    // ------ CRÉATION DU COOKIE ICI -----------------------------
    
    // Exemple : Créer un cookie qui retient l'email pendant 30 jours
    $cookie_name = "remember_user";
    $cookie_value = $email; // En production, préférez un token sécurisé aléatoire lié à la BDD
    $cookie_expire = time() + (86400 * 30); // 86400 = 1 jour en secondes
    
    setcookie(
        $cookie_name, 
        $cookie_value, 
        $cookie_expire, 
        "/",          // Chemin sur le serveur où le cookie sera disponible
        "",           // Domaine (laissez vide pour le domaine actuel)
        false,        // Secure : mis à false pour fonctionner sur localhost HTTP
        true          // HttpOnly : true = inaccessible via JavaScript (sécurité contre les failles XSS)
    );

    // -----------------------------------------------------------
    
    header("Location: ../index.html");
    exit();
} else {
    header("Location: ../page/connexion-inscription.php?error=1");
    exit();
}
?>