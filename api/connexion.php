<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Démarrer la session pour mémoriser l'utilisateur connecté
session_start();

// Inclure la configuration de base de données
require_once 'config.php';

// Récupérer les données envoyées par Vue.js
$json = file_get_contents("php://input");
$data = json_decode($json);

// Vérifier que l'email et le mot de passe sont fournis
if (!$data || empty(trim($data->email)) || empty(trim($data->password))) {
    echo json_encode(["success" => false, "error" => "Veuillez remplir tous les champs."]);
    exit;
}

try {
    // Chercher l'utilisateur dans la base de données via son email
    $stmt = $pdo->prepare("SELECT id, name, pwd_hash FROM users WHERE email = :email");
    $stmt->execute(['email' => $data->email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe ET si le mot de passe (haché) correspond
    if ($user && password_verify($data->password, $user['pwd_hash'])) {
        
        // Stocker les informations de l'utilisateur dans la session PHP
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $data->email;

        // Réponse de succès
        echo json_encode(["success" => true, "message" => "Connexion réussie !"]);

    } else {
        // Mauvais email ou mauvais mot de passe
        echo json_encode(["success" => false, "error" => "Email ou mot de passe incorrect."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur de base de données : " . $e->getMessage()]);
}
