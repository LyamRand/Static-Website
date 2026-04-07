<?php
// check_auth.php
header("Content-Type: application/json");
session_start();

// On vérifie si l'utilisateur a une session active
if (isset($_SESSION['idClient']) && isset($_SESSION['user_name'])) {
    // L'utilisateur est connecté, on renvoie ses infos
    echo json_encode([
        "isLoggedIn" => true,
        "user" => [
            "id" => $_SESSION['idClient'],
            "name" => $_SESSION['user_name']
        ]
    ]);
} else {
    // Vérification du cookie pour reconnexion automatique
    if (isset($_COOKIE['remember_user'])) {
        require_once 'config.php';
        try {
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = :id");
            $stmt->execute(['id' => $_COOKIE['remember_user']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Recréer la session
                $_SESSION['idClient'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                echo json_encode([
                    "isLoggedIn" => true,
                    "user" => [
                        "id" => $_SESSION['idClient'],
                        "name" => $_SESSION['user_name']
                    ]
                ]);
                exit;
            }
        } catch (PDOException $e) {
            // Ignorer l'erreur BDD ici silencieusement
        }
    }
    
    // L'utilisateur n'est pas connecté
    echo json_encode([
        "isLoggedIn" => false,
        "user" => null
    ]);
}
?>