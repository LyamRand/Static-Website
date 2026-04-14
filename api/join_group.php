<?php
// Paramètres de sécurité pour les cookies de session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header('Content-Type: application/json');
include('./config.php');

$input_data = json_decode(file_get_contents('php://input'), true);
if (is_array($input_data)) {
    $_POST = array_merge($_POST, $input_data);
}

$code = $_POST['code'] ?? '';

// Sécurité : vérifier que l'utilisateur est connecté
if (!isset($_SESSION['idClient'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

if (empty($code)) {
    echo json_encode(['success' => false, 'error' => 'Veuillez entrer un code.']);
    exit;
}

try {
    $code = strtoupper(trim($code));

    // 1. Chercher le groupe via le code
    $stmt = $pdo->prepare("SELECT id FROM `groups` WHERE code = :code");
    $stmt->execute(['code' => $code]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$group) {
        echo json_encode(['success' => false, 'error' => 'Code de groupe invalide.']);
        exit;
    }

    $groupId = $group['id'];
    $userId = $_SESSION['idClient'];

    // 2. Vérifier si l'utilisateur est déjà dans le groupe
    $stmtCheck = $pdo->prepare("SELECT group_id FROM group_users WHERE group_id = :group_id AND user_id = :user_id");
    $stmtCheck->execute([
        'group_id' => $groupId,
        'user_id' => $userId
    ]);

    if ($stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Vous êtes déjà dans ce groupe.']);
        exit;
    }

    // 3. Ajouter l'utilisateur
    $stmtInsert = $pdo->prepare("INSERT INTO group_users (group_id, user_id) VALUES (:group_id, :user_id)");
    $stmtInsert->execute([
        'group_id' => $groupId,
        'user_id' => $userId
    ]);

    echo json_encode(['success' => true, 'groupId' => $groupId, 'message' => 'Vous avez rejoint le groupe avec succès !']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la tentative de rejoindre le groupe.', 'details' => $e->getMessage()]);
}
?>
