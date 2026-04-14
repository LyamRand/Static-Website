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

if (!isset($_SESSION['idClient'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$expenseId = $_POST['expense_id'] ?? null;

if (!$expenseId) {
    echo json_encode(['success' => false, 'error' => 'ID manquant']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt2 = $pdo->prepare("DELETE FROM splits WHERE expense_id = :id");
    $stmt2->execute(['id' => $expenseId]);

    $stmt1 = $pdo->prepare("DELETE FROM expenses WHERE id = :id");
    $stmt1->execute(['id' => $expenseId]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
