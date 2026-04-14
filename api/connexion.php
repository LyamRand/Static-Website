<?php
// Paramètres de sécurité pour les cookies de session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header('Content-Type: application/json');
require_once 'config.php';

// Support du raw JSON depuis Vue Fetch API
$input_data = json_decode(file_get_contents('php://input'), true);
if (is_array($input_data))
    $_POST = array_merge($_POST, $input_data);

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Vérification des champs vides
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'empty']);
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
        if (isset($_POST['remember']) && $_POST['remember']) {
            // Cookie valable 30 jours (86400 = 1 jour)
            setcookie("remember_user", $user['id'], time() + (86400 * 30), "/", "", false, true);
        }

        // Succès
        echo json_encode(['success' => true]);
        exit;
    } else {
        // Erreur de login
        echo json_encode(['success' => false, 'error' => 'invalid']);
        exit;
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'database']);
    exit;
}
?>