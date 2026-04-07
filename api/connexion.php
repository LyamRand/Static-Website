<?php
session_start();
require_once 'config.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Vérification des champs vides
if (empty($email) || empty($password)) {
    header("Location: ../page/connexion-inscription.php?error=empty");
    exit;
}

try {
    // Chercher l'utilisateur par son email
    $stmt = $pdo->prepare("SELECT id, name, pwd_hash FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe
    if ($user && password_verify($password, $user['pwd_hash'])) {
        // Succès ! Création de la session
        $_SESSION['idClient'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // Si l'utilisateur a coché "Se souvenir de moi"
        if (isset($_POST['remember'])) {
            // Cookie valable 30 jours (86400 = 1 jour)
            setcookie("remember_user", $user['id'], time() + (86400 * 30), "/", "", false, true);
        }

        // Redirection vers le tableau de bord
        header("Location: ../page/dashboard.php");
        exit;
    } else {
        // Erreur de login
        header("Location: ../page/connexion-inscription.php?error=invalid");
        exit;
    }

} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
?>