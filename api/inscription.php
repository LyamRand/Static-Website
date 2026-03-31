<?php
session_start();
require_once 'config.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$iban = trim($_POST['iban'] ?? '');

// Vérifier que tout est rempli
if (empty($name) || empty($email) || empty($password)) {
    header("Location: ../page/connexion-inscription.php?error=empty&tab=register");
    exit;
}

try {
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        // Redirection avec erreur email déjà pris, et on force l'onglet "inscription"
        header("Location: ../page/connexion-inscription.php?error=exists&tab=register");
        exit;
    }

    // Hasher le mot de passe
    $pwd_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insérer le nouvel utilisateur (l'IBAN peut être null)
    $stmt = $pdo->prepare("INSERT INTO users (name, email, pwd_hash, iban) VALUES (:name, :email, :pwd_hash, :iban)");
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'pwd_hash' => $pwd_hash,
        'iban' => empty($iban) ? null : $iban
    ]);

    // Connecter l'utilisateur automatiquement
    $_SESSION['idClient'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;

    // Rediriger vers le tableau de bord
    header("Location: ../page/dashboard.php");
    exit;

} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
?>