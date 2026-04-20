<?php
header("Content-Type: application/json");
// Paramètres de sécurité pour les cookies de session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
require_once 'config.php';

// Si non connecté, on renvoie une erreur
if (!isset($_SESSION['idClient'])) {
    echo json_encode(["success" => false, "error" => "Non connecté"]);
    exit;
}

$userId = $_SESSION['idClient'];

try {
    // Requête adaptée à tes vraies tables (groups et group_users)
    $sql = "
        SELECT 
            g.id, 
            g.name AS nom, 
            g.logo AS icone, 
            (
                (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE group_id = g.id AND payer_id = :user_id)
                -
                (SELECT COALESCE(SUM(s.amount), 0) FROM splits s JOIN expenses e ON s.expense_id = e.id WHERE e.group_id = g.id AND s.user_id = :user_id)
            ) AS solde,
            (SELECT COUNT(*) FROM group_users WHERE group_id = g.id) AS participants
        FROM `groups` g
        JOIN `group_users` gu ON g.id = gu.group_id
        WHERE gu.user_id = :user_id
        ORDER BY g.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // On transforme les anciens mots-clés en émojis, ou on garde l'émoji si c'est déjà un
    $iconMap = [
        'home' => '🏠',
        'flight' => '✈️',
        'landscape' => '⛰️',
        'sports_bar' => '🍻'
    ];

    foreach ($groupes as &$g) {
        $g['solde'] = (float) $g['solde'];
        $g['participants'] = (int) $g['participants'];
        $g['icone'] = isset($iconMap[$g['icone']]) ? $iconMap[$g['icone']] : ($g['icone'] ?: '📁');
    }

    echo json_encode(["success" => true, "groupes" => $groupes]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur BDD : " . $e->getMessage()]);
}
?>