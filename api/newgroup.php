<?php
session_start();
header('Content-Type: application/json');
include('./config.php');

$input_data = json_decode(file_get_contents('php://input'), true);
if (is_array($input_data))
    $_POST = array_merge($_POST, $input_data);

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$icone = $_POST['group_icon'] ?? 'home'; // On récupère l'info du formulaire

// Sécurité : vérifier que l'utilisateur est connecté
if (!isset($_SESSION['idClient'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

if (empty($name) || empty($icone)) {
    echo json_encode(['success' => false, 'error' => 'empty']);
    exit;
} else {
    try {
        // On utilise une transaction car on insère dans 2 tables à la suite
        $pdo->beginTransaction();

        // 1. On crée le groupe (attention, ta colonne s'appelle 'logo' dans ta BDD)
        $sql = $pdo->prepare("INSERT INTO `groups` (name, description, logo) VALUES (:name, :description, :logo)");
        $sql->execute([
            "name" => $name,
            "description" => $description,
            "logo" => $icone
        ]);

        // On récupère l'ID du groupe qu'on vient juste de créer
        $groupId = $pdo->lastInsertId();

        // 2. On ajoute le créateur dans ce groupe (dans ta table group_users)
        $sqlMembre = $pdo->prepare("INSERT INTO `group_users` (group_id, user_id) VALUES (:group_id, :user_id)");
        $sqlMembre->execute([
            "group_id" => $groupId,
            "user_id" => $_SESSION['idClient']
        ]);

        $pdo->commit();

        // Redirection vers le dashboard après succès
        echo json_encode(['success' => true, 'groupId' => $groupId]);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack(); // Annule tout s'il y a une erreur
        echo json_encode(['success' => false, 'error' => 'database', 'details' => $e->getMessage()]);
        exit;
    }
}
?>