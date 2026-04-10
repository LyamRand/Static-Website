<?php
header("Content-Type: application/json");
session_start();
require_once 'config.php';

// Vérifications de sécurité
if (!isset($_SESSION['idClient']) || !isset($_GET['id'])) {
    echo json_encode(["success" => false, "error" => "Requête invalide ou non connecté"]);
    exit;
}

$userId = $_SESSION['idClient'];
$groupId = $_GET['id'];

try {
    // 1. Récupérer les infos du groupe (et vérifier que l'utilisateur en fait bien partie)
    $stmt = $pdo->prepare("
        SELECT g.id, g.name AS nom, g.logo AS icone, g.code,
               (SELECT COUNT(*) FROM group_users WHERE group_id = g.id) AS participants
        FROM groups g
        JOIN group_users gu ON g.id = gu.group_id
        WHERE g.id = :group_id AND gu.user_id = :user_id
    ");
    $stmt->execute(['group_id' => $groupId, 'user_id' => $userId]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the participants details
    $stmtUsers = $pdo->prepare("
        SELECT u.id, u.name 
        FROM users u 
        JOIN group_users gu ON u.id = gu.user_id
        WHERE gu.group_id = :group_id
        LIMIT 5
    ");
    $stmtUsers->execute(['group_id' => $groupId]);
    $group['participantsInfo'] = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    if (!$group) {
        echo json_encode(["success" => false, "error" => "Groupe introuvable ou accès refusé"]);
        exit;
    }

    // Convertir le logo texte en emoji pour le design Vue.js
    $iconMap = ['home' => '🏠', 'flight' => '✈️', 'landscape' => '⛰️', 'sports_bar' => '🍻'];
    $group['icone'] = $iconMap[$group['icone']] ?? '📁';

    // 2. Récupérer les dépenses de ce groupe
    $stmtExp = $pdo->prepare("
        SELECT e.id, e.description AS title, e.amount, e.payer_id, u.name AS payer_name
        FROM expenses e
        JOIN users u ON e.payer_id = u.id
        WHERE e.group_id = :group_id
        ORDER BY e.id DESC
    ");
    $stmtExp->execute(['group_id' => $groupId]);
    $expensesRaw = $stmtExp->fetchAll(PDO::FETCH_ASSOC);

    $expenses = [];
    $totalGroupExpenses = 0;
    $myTotalPaid = 0;
    $myTotalShare = 0;

    foreach ($expensesRaw as $exp) {
        $amount = (float) $exp['amount'];
        $totalGroupExpenses += $amount;

        // Récupérer ma part exacte dans la table `splits` pour cette dépense
        $stmtSplit = $pdo->prepare("SELECT amount FROM splits WHERE expense_id = :exp_id AND user_id = :user_id");
        $stmtSplit->execute(['exp_id' => $exp['id'], 'user_id' => $userId]);
        $splitRow = $stmtSplit->fetch(PDO::FETCH_ASSOC);

        // S'il n'y a pas encore de répartition précise dans "splits", on divise par le nombre de participants
        $mySplitAmount = $splitRow ? (float) $splitRow['amount'] : ($amount / max(1, $group['participants']));
        $myTotalShare += $mySplitAmount;

        $owed = 0;
        if ($exp['payer_id'] == $userId) {
            $myTotalPaid += $amount;
            $owed = $amount - $mySplitAmount; // Ce qu'on me doit pour CETTE dépense
            $payerDisplay = "Moi";
        } else {
            $owed = -$mySplitAmount; // Ce que je dois pour CETTE dépense
            $payerDisplay = $exp['payer_name'];
        }

        // On génère une icône et une couleur aléatoire (basée sur le titre) pour faire un joli rendu HTML
        $icons = [
            'restaurant' => 'bg-red-50 text-red-danger',
            'local_gas_station' => 'bg-yellow-50 text-yellow-500',
            'home' => 'bg-orange-50 text-orange-500',
            'shopping_cart' => 'bg-green-50 text-green-success'
        ];
        $iconKeys = array_keys($icons);
        // Astuce pour que la même dépense ait toujours la même couleur
        $randomIcon = $iconKeys[crc32($exp['title'] ?: 'X') % count($iconKeys)];

        $expenses[] = [
            "id" => $exp['id'],
            "title" => $exp['title'] ?: 'Dépense sans nom',
            "payer" => $payerDisplay,
            "amount" => $amount,
            "owed" => $owed,
            "icon" => $randomIcon,
            "colorClass" => $icons[$randomIcon]
        ];
    }

    // 3. Calcul du solde final pour la carte de droite
    $myBalance = $myTotalPaid - $myTotalShare;
    $stats = [
        "total" => $totalGroupExpenses,
        "unbalanced" => abs($myBalance) // L'argent que tu dois récupérer ou donner en tout
    ];

    echo json_encode([
        "success" => true,
        "group" => $group,
        "expenses" => $expenses,
        "stats" => $stats
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur BDD : " . $e->getMessage()]);
}
?>