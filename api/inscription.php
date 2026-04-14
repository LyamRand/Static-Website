<?php
// Paramètres de sécurité pour les cookies de session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$input_data = json_decode(file_get_contents('php://input'), true);
if (is_array($input_data))
    $_POST = array_merge($_POST, $input_data);

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$iban = trim($_POST['iban'] ?? '');

// Vérifier que tout est rempli
if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'empty']);
    exit;
}

try {
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->fetch()) {
        // L'utilisateur existe déjà : on ne révèle PAS cette information
        // On ne modifie PAS les données existantes (pas d'écrasement)
        // On renvoie le même message de succès pour ne pas divulguer l'existence du compte
        // En production, on enverrait un email à l'adresse indiquant qu'un compte existe déjà
        // Simulation : "Un email a été envoyé à l'adresse indiquée contenant :
        //   Objet : Tentative de création de compte Splitz
        //   Corps : Un compte existe déjà avec cette adresse email.
        //          Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.
        //          Sinon, connectez-vous ou réinitialisez votre mot de passe."
        echo json_encode(['success' => true, 'message' => 'Un email de confirmation a été envoyé à votre adresse.']);
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

    // Même message générique que pour un utilisateur existant (non-divulgation)
    // En production, on enverrait un email de bienvenue avec un lien de confirmation
    // Simulation : "Un email a été envoyé à l'adresse indiquée contenant :
    //   Objet : Bienvenue sur Splitz !
    //   Corps : Cliquez sur le lien suivant pour confirmer votre compte : [lien]"
    echo json_encode(['success' => true, 'message' => 'Un email de confirmation a été envoyé à votre adresse.']);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'database']);
    exit;
}
?>