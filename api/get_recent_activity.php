<?php
header("Content-Type: application/json");
session_start();
require_once 'config.php';

if (!isset($_SESSION['idClient'])) {
    echo json_encode(["success" => false, "error" => "Non connecté"]);
    exit;
}

$userId = $_SESSION['idClient'];

try {
    // Vérifier et ajouter la colonne category si elle n'existe pas
    try {
        $stmtCheck = $pdo->query("SHOW COLUMNS FROM expenses LIKE 'category'");
        if (!$stmtCheck->fetch()) {
            $pdo->exec("ALTER TABLE expenses ADD COLUMN category VARCHAR(50) DEFAULT 'Autres'");
        }
    } catch (Exception $e) {}

    $sql = "
        SELECT 
            e.id, 
            e.description AS title, 
            e.amount, 
            g.name AS group_name, 
            g.logo AS logo,
            u.name AS payer_name, 
            u.id AS payer_id,
            e.category
        FROM expenses e
        JOIN `groups` g ON e.group_id = g.id
        JOIN users u ON e.payer_id = u.id
        JOIN group_users gu ON g.id = gu.group_id
        WHERE gu.user_id = :user_id
        ORDER BY e.id DESC
        LIMIT 5
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted = [];
    foreach ($activities as $act) {
        $category = $act['category'] ?? 'Autres';
        $iconMap = [
            'Repas' => 'bg-red-50 text-red-danger',
            'Transport' => 'bg-yellow-50 text-yellow-500',
            'Logement' => 'bg-orange-50 text-orange-500',
            'Courses' => 'bg-green-50 text-green-success',
            'Autres' => 'bg-slate-100 text-slate-500'
        ];
        
        $groupIconMap = [
            'home' => '🏠',
            'flight' => '✈️',
            'landscape' => '⛰️',
            'sports_bar' => '🍻',
            'more_horiz' => '📁'
        ];

        $colorClass = $iconMap[$category] ?? $iconMap['Autres'];
        $groupLogo = $groupIconMap[$act['logo']] ?? '📁';

        $formatted[] = [
            "id" => $act['id'],
            "title" => $act['title'] ?: 'Dépense',
            "amount" => (float)$act['amount'],
            "group_name" => $act['group_name'],
            "payer" => $act['payer_id'] == $userId ? 'Vous' : ltrim($act['payer_name']),
            "icon" => $groupLogo,
            "colorClass" => $colorClass
        ];
    }

    echo json_encode(["success" => true, "activities" => $formatted]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur BDD : " . $e->getMessage()]);
}
?>
