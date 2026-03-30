<?php
// ------ connexion à la base de données -----------------------
include('./config.php');

// ------ récupération des données du formulaire ----------------
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$icone = $_POST['group_icon'] ?? 'home';

if (empty($name) || empty($icone)) {
    echo "Veuillez vérifier que le nom et l'icône sont bien remplis.";
    http_response_code(400);
} else {
    // Note : On utilise $pdo car c'est le nom de ta connexion dans config.php (et non $bdd)
    $sql = $pdo->prepare("INSERT INTO `groups` VALUES (null, :name, :description, :group_icon)");
    $sql->execute([
        "name" => $name,
        "description" => $description,
        "group_icon" => $icone
    ]);
    
    // Redirection vers le dashboard après succès
    header("Location: ../page/dashboard.php?success=1");
    exit();
}
?>