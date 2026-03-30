<?php
session_start();
include('./config.php');

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$icone = $_POST['group_icon'] ?? 'home'; // On récupère l'info du formulaire

// Sécurité : vérifier que l'utilisateur est connecté
if (!isset($_SESSION['idClient'])) {
    die("Erreur : Vous devez être connecté pour créer un groupe.");
}

if (empty($name) || empty($icone)) {
    echo "Veuillez vérifier que le nom et l'icône sont bien remplis.";
    http_response_code(400);
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
        header("Location: ../page/dashboard.php?success=1");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack(); // Annule tout s'il y a une erreur
        die("Erreur lors de la création du groupe : " . $e->getMessage());
    }
}
?>