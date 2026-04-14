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

$groupId = $_POST['group_id'] ?? $_GET['id'] ?? '';

// Sécurité : vérifier que l'utilisateur est connecté
if (!isset($_SESSION['idClient'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

if (empty($groupId)) {
    echo json_encode(['success' => false, 'error' => 'ID de groupe manquant.']);
    exit;
}

$userId = $_SESSION['idClient'];

try {
    // Vérifier si l'utilisateur fait partie du groupe (autorisation simple)
    $stmtCheck = $pdo->prepare("SELECT group_id FROM group_users WHERE group_id = :group_id AND user_id = :user_id");
    $stmtCheck->execute([
        'group_id' => $groupId,
        'user_id' => $userId
    ]);

    if (!$stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Vous n\'êtes pas autorisé à supprimer ce groupe.']);
        exit;
    }

    $pdo->beginTransaction();

    // Supprimer les splits liés aux dépenses du groupe
    $stmtSplits = $pdo->prepare("DELETE splits FROM splits JOIN expenses ON splits.expense_id = expenses.id WHERE expenses.group_id = :group_id");
    $stmtSplits->execute(['group_id' => $groupId]);

    // Supprimer les dépenses du groupe
    $stmtExp = $pdo->prepare("DELETE FROM expenses WHERE group_id = :group_id");
    $stmtExp->execute(['group_id' => $groupId]);

    // Supprimer les membres du groupe
    $stmtUsers = $pdo->prepare("DELETE FROM group_users WHERE group_id = :group_id");
    $stmtUsers->execute(['group_id' => $groupId]);

    // Enfin, supprimer le groupe
    $stmtGroup = $pdo->prepare("DELETE FROM `groups` WHERE id = :group_id");
    $stmtGroup->execute(['group_id' => $groupId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Groupe supprimé avec succès.']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression du groupe.', 'details' => $e->getMessage()]);
}
?>
