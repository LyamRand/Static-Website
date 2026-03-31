<?php
// On récupère l'ID via l'URL
$groupId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$groupId) {
    // Si pas d'ID, on redirige vers la liste des groupes
    header('Location: groupes.php');
    exit;
}

// Ici, tu feras ta requête SQL pour récupérer les infos du groupe $groupId
// $query = "SELECT * FROM groupes WHERE id = :id";
?>

<h1 class="text-2xl font-bold">Détails du groupe n°<?php echo htmlspecialchars($groupId); ?></h1>
