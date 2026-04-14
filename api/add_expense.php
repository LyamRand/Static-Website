<?php
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
$amount = $_POST['montant'] ?? null;
$description = $_POST['description'] ?? '';
$category = $_POST['categorie'] ?? 'Autres';
$payerId = $_POST['payeur'] ?? $_SESSION['idClient'];

if (!$groupId || !$amount || !$description) {
    echo json_encode(['success' => false, 'error' => 'Veuillez remplir tous les champs (montant et description).']);
    exit;
}

try {
    $pdo->beginTransaction();

    try {
        $stmtCheck = $pdo->query("SHOW COLUMNS FROM expenses LIKE 'category'");
        if (!$stmtCheck->fetch()) {
            $pdo->exec("ALTER TABLE expenses ADD COLUMN category VARCHAR(50) DEFAULT 'Autres'");
        }
    } catch (Exception $e) {}

    $stmtExp = $pdo->prepare("INSERT INTO expenses (group_id, amount, description, payer_id, category) VALUES (:group_id, :amount, :description, :payer_id, :category)");
    $stmtExp->execute([
        'group_id' => $groupId,
        'amount' => $amount,
        'description' => $description,
        'payer_id' => $payerId,
        'category' => $category
    ]);
    
    $expenseId = $pdo->lastInsertId();

    // 2. Extraire tous les utilisateurs du groupe
    $stmtUsers = $pdo->prepare("SELECT user_id FROM group_users WHERE group_id = :group_id");
    $stmtUsers->execute(['group_id' => $groupId]);
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    $partageOption = $_POST['partageOption'] ?? 'equal';
    $customPercentages = $_POST['customPercentages'] ?? [];

    if (count($users) > 0) {
        if ($partageOption === 'custom' && is_array($customPercentages)) {
            // Partage personnalisé en pourcentage
            $stmtSplit = $pdo->prepare("INSERT INTO splits (expense_id, user_id, amount) VALUES (:expense_id, :user_id, :amount)");
            foreach ($users as $u) {
                $pct = isset($customPercentages[$u['user_id']]) ? floatval($customPercentages[$u['user_id']]) : 0;
                $userAmount = $amount * ($pct / 100);
                $stmtSplit->execute([
                    'expense_id' => $expenseId,
                    'user_id' => $u['user_id'],
                    'amount' => $userAmount
                ]);
            }
        } else {
            // Partage équitable (défaut)
            $splitAmount = $amount / count($users);
            $stmtSplit = $pdo->prepare("INSERT INTO splits (expense_id, user_id, amount) VALUES (:expense_id, :user_id, :amount)");
            
            foreach ($users as $u) {
                $stmtSplit->execute([
                    'expense_id' => $expenseId,
                    'user_id' => $u['user_id'],
                    'amount' => $splitAmount
                ]);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'error' => 'Erreur SQL : ' . $e->getMessage()]);
}
?>
