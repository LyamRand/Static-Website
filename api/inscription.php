<?php
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
        echo json_encode(['success' => false, 'error' => 'exists']);
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
    echo json_encode(['success' => true]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'database']);
    exit;
}
?>