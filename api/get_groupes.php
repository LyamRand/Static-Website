<?php
header("Content-Type: application/json");
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
            0.00 AS solde, -- On met 0 en attendant d'utiliser tes tables 'expenses' et 'splits' !
            (SELECT COUNT(*) FROM group_users WHERE group_id = g.id) AS participants
        FROM `groups` g
        JOIN `group_users` gu ON g.id = gu.group_id
        WHERE gu.user_id = :user_id
        ORDER BY g.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // On transforme les mots-clés en émojis pour l'affichage Vue.js
    $iconMap = [
        'home' => '🏠',
        'flight' => '✈️',
        'landscape' => '⛰️',
        'sports_bar' => '🍻'
    ];

    foreach ($groupes as &$g) {
        $g['solde'] = (float) $g['solde'];
        $g['participants'] = (int) $g['participants'];
        $g['icone'] = $iconMap[$g['icone']] ?? '📁'; // Emoji par défaut si introuvable
    }

    echo json_encode(["success" => true, "groupes" => $groupes]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur BDD : " . $e->getMessage()]);
}
?>