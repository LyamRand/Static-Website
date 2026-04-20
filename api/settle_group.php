<?php
// Paramètres de sécurité pour les cookies de session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header('Content-Type: application/json');
require_once './config.php';

$input_data = json_decode(file_get_contents('php://input'), true);
if (is_array($input_data)) {
    $_POST = array_merge($_POST, $input_data);
}

// Vérifications
if (!isset($_SESSION['idClient'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$groupId = $_POST['group_id'] ?? null;
$userId = $_SESSION['idClient'];

if (!$groupId) {
    echo json_encode(['success' => false, 'error' => 'ID de groupe manquant.']);
    exit;
}

try {
    // Vérifier si l'utilisateur fait partie du groupe (autorisation simple)
    $stmtCheck = $pdo->prepare("SELECT group_id FROM group_users WHERE group_id = :group_id AND user_id = :user_id");
    $stmtCheck->execute([
        'group_id' => $groupId,
        'user_id' => $userId
    ]);

    if (!$stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Vous n\'êtes pas autorisé à équilibrer ce groupe.']);
        exit;
    }

    $pdo->beginTransaction();

    // 1. Supprimer les splits liés aux dépenses du groupe
    $stmtSplits = $pdo->prepare("DELETE splits FROM splits JOIN expenses ON splits.expense_id = expenses.id WHERE expenses.group_id = :group_id");
    $stmtSplits->execute(['group_id' => $groupId]);

    // 2. Supprimer les dépenses du groupe
    $stmtExp = $pdo->prepare("DELETE FROM expenses WHERE group_id = :group_id");
    $stmtExp->execute(['group_id' => $groupId]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'error' => 'Erreur SQL : ' . $e->getMessage()]);
}
?>
